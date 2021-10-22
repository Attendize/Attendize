<?php

use App\Models\Event;
use App\Models\EventStats;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Superbalist\Money\Money;

class RetrofitFixScriptForStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Link tickets to their orders based on the order items on each order record. It will try and
         * find the ticket on the event and match the order item title to the ticket title.
         */
        Order::all()->map(function ($order) {
            $event = $order->event()->first();
            $tickets = $event->tickets()->get();
            $orderItems = $order->orderItems()->get();
            // We would like a list of titles from the order items to test against existing tickets
            $mapOrderItemTitles = $orderItems->map(function ($orderItem) {
                return $orderItem->title;
            });

            // Filter tickets who's title is contained in the order items set
            $ticketsFound = $tickets->filter(function ($ticket) use ($mapOrderItemTitles) {
                return $mapOrderItemTitles->contains($ticket->title);
            });

            // Attach the ticket to it's order to keep referencial integrity
            $ticketsFound->map(function ($ticket) use ($order) {
                $pivotExists = $order->tickets()->where('ticket_id', $ticket->id)->exists();
                if (! $pivotExists) {
                    \Log::debug(sprintf('Attaching Ticket (ID:%d) to Order (ID:%d)', $ticket->id, $order->id));
                    $order->tickets()->attach($ticket);
                }
            });

            /*
             * Next we need to check if the order amount is the same as the total of the order items.
             * We use the order items as the source of truth for setting order amounts to the correct ones.
             */
            $orderStringValue = $orderItems->reduce(function ($carry, $orderItem) {
                $orderTotal = (new Money($carry));
                $orderItemValue = (new Money($orderItem->unit_price))->multiply($orderItem->quantity);

                return $orderTotal->add($orderItemValue)->format();
            });

            // Refunded orders had their amounts wiped in previous versions so we need to fix that before we can work on stats
            $orderItemsValue = (new Money($orderStringValue));
            $oldOrderAmount = (new Money($order->amount));

            // We are checking to see if there is a change from what is stored vs what the order items says
            if ($oldOrderAmount->equals($orderItemsValue) === false) {
                \Log::debug(sprintf(
                    'Setting Order (ID:%d, OLD_AMOUNT:%s) amount to match Order Items Amount: %s',
                    $order->id,
                    $oldOrderAmount->format(),
                    $orderItemsValue->format()
                ));
                $order->amount = $orderItemsValue->toFloat();
                $order->save();
            }

            // If the order is cancelled but the linked attendees are not marked as cancelled and refunded, we need to fix that
            if ($order->is_refunded) {
                $order->attendees()->get()->map(function ($attendee) {
                    // Mark attendees as cancelled and refunded if the order is cancelled
                    if (! $attendee->is_refunded) {
                        \Log::debug(sprintf('Marking Attendee (ID:%d) as refunded', $attendee->id));
                        $attendee->is_refunded = true;
                    }

                    if (! $attendee->is_cancelled) {
                        \Log::debug(sprintf('Marking Attendee (ID:%d) as cancelled', $attendee->id));
                        $attendee->is_cancelled = true;
                    }
                    // Update the attendee to reflect the real world
                    $attendee->save();
                });
            }
        });

        /**
         * Next we need to check if the sales volume on the ticket is correct based on the order items again
         * as the source of truth. We ignore tickets where the order is refunded.
         */
        Ticket::all()->map(function ($ticket) {
            // NOTE: We need to ignore refunded orders when calculating the ticket sales volume.
            /** @var Ticket $ticket */
            $orders = $ticket->orders()->where('is_refunded', false)->get();

            // Calculate the ticket sales value from the order items linked to a ticket.
            $ticketStringValue = $orders->reduce(function ($ticketCarry, $order) use ($ticket) {
                $ticketTotal = (new Money($ticketCarry));

                /** @var Order $order */
                $orderItems = $order->orderItems()->get();
                $orderStringValue = $orderItems->reduce(function ($carry, $orderItem) use ($ticket) {
                    $orderTotal = (new Money($carry));
                    $orderItemValue = (new Money($orderItem->unit_price))->multiply($orderItem->quantity);

                    // Only count the order items related to the ticket
                    if (trim($ticket->title) === trim($orderItem->title)) {
                        return $orderTotal->add($orderItemValue)->format();
                    }

                    return $orderTotal->format();
                });

                $orderValue = (new Money($orderStringValue));

                return $ticketTotal->add($orderValue)->format();
            });

            // Compare the current value against the calculated one and update as needed
            $oldTicketSalesVolume = (new Money($ticket->sales_volume));
            $orderItemsTicketSalesVolume = (new Money($ticketStringValue));
            if ($oldTicketSalesVolume->equals($orderItemsTicketSalesVolume) === false) {
                \Log::debug(sprintf(
                    'Updating Ticket (ID:%d, OLD_AMOUNT:%s) - New Sales Volume (%s)',
                    $ticket->id,
                    $oldTicketSalesVolume->format(),
                    $orderItemsTicketSalesVolume->format()
                ));
                $ticket->sales_volume = $orderItemsTicketSalesVolume->toFloat();
                $ticket->save();
            }

            /**
             * Do the same check for ticket quantity sold against the order items. Lucky for us the order item
             * saved the quantity of tickets sold.
             */
            $ticketQuantity = $orders->reduce(function ($ticketCarry, $order) use ($ticket) {
                $orderItems = $order->orderItems()->get();
                $orderQuantity = $orderItems->reduce(function ($carry, $orderItem) use ($ticket) {
                    if (trim($ticket->title) === trim($orderItem->title)) {
                        return $carry + $orderItem->quantity;
                    }

                    return $carry;
                });

                return $ticketCarry + $orderQuantity;
            });

            // We need to update the ticket quantity if the order items reflect otherwise
            if ((int) $ticket->quantity_sold !== (int) $ticketQuantity) {
                \Log::debug(sprintf(
                    'Updating Ticket (ID:%d, OLD_QUANTITY:%d) - New Quantity (%d)',
                    $ticket->id,
                    $ticket->quantity_sold,
                    $ticketQuantity
                ));
                $ticket->quantity_sold = $ticketQuantity;
                $ticket->save();
            }
        });

        // We need to calculate the time based stats on events going back in time to fix any inconsistencies.
        Event::all()->map(function ($event) {
            /** @var $event Event */
            $orders = $event->orders()->where('is_refunded', false)->get();

            /**
             * We will build the event stats for all orders in an event along with their create date as
             * the key for the dashboard graphs. We are ignoring views as it's out of scope for this fix.
             */
            $orderTimeBasedStats = [];
            $orders->map(function ($order) use (&$orderTimeBasedStats) {
                /** @var $order Order */
                $orderItems = $order->orderItems()->get();
                $quantity = $orderItems->reduce(function ($carry, $orderItem) {
                    return $carry + $orderItem->quantity;
                });

                $orderDay = Carbon::createFromTimeString($order->created_at)->format('Y-m-d');
                if (! isset($orderTimeBasedStats[$orderDay])) {
                    $orderTimeBasedStats[$orderDay] = [
                        'quantity' => 0,
                        'sales_volume' => new Money(0),
                    ];
                }
                // Increment any hits on already saved days for quantity
                $orderTimeBasedStats[$orderDay]['quantity'] += $quantity;
                /** @var Money $previousSalesVolume */
                $previousSalesVolume = $orderTimeBasedStats[$orderDay]['sales_volume'];

                // Increment any hits on already saved days for amounts
                $orderAmount = new Money($order->amount);
                $orderTimeBasedStats[$orderDay]['sales_volume'] = $previousSalesVolume->add($orderAmount);
            });

            /**
             * Event stats needs to be checked so the dashboard time series graphs match the historical order amounts
             * and amount of tickets sold per day.
             */
            EventStats::where('event_id', $event->id)->get()->map(function ($eventStat) use ($orderTimeBasedStats) {
                /** @var $eventStat EventStats */
                if (isset($orderTimeBasedStats[$eventStat->date])) {
                    // Here we are comparing the calculated ticket quantity against the current one and updating if needed
                    $timeBasedQuantity = $orderTimeBasedStats[$eventStat->date]['quantity'];
                    if ($eventStat->tickets_sold !== $timeBasedQuantity) {
                        \Log::debug(sprintf(
                            'Updating Event Stat (ID:%d, OLD_QUANTITY:%d) - New Quantity %d',
                            $eventStat->id,
                            $eventStat->tickets_sold,
                            $timeBasedQuantity
                        ));
                        $eventStat->tickets_sold = $timeBasedQuantity;
                    }

                    // Here we are comparing the calculated ticket amounts against the current one and updating if needed
                    $oldEventStatsSalesVolume = new Money($eventStat->sales_volume);
                    $timeBasedSalesVolume = $orderTimeBasedStats[$eventStat->date]['sales_volume'];
                    if ($oldEventStatsSalesVolume->equals($timeBasedSalesVolume) === false) {
                        \Log::debug(sprintf(
                            'Updating Event Stat (ID:%d, OLD_SALES_VOLUME:%s) - New Sales Volume %s',
                            $eventStat->id,
                            $oldEventStatsSalesVolume->format(),
                            $timeBasedSalesVolume->format()
                        ));
                        $eventStat->sales_volume = $timeBasedSalesVolume->toFloat();
                    }
                    if ($eventStat->isDirty()) {
                        // Persist event stat changes to reflect order amounts and tickets sold
                        $eventStat->save();
                    }
                } else {
                    /*
                     * If the order stats does not exist, but the event stat has quantity and sales_volume then
                     * we need to kill the values since there is no subsequent order information to back their
                     * existence. Again this is built from the order items as the source of truth.
                     */
                    if ($eventStat->tickets_sold > 0) {
                        \Log::debug(sprintf(
                            'Clearing Event Stat (ID:%d, TICKETS_SOLD:%d, SALES_VOLUME:%f) due to no order information',
                            $eventStat->id,
                            $eventStat->tickets_sold,
                            $eventStat->sales_volume
                        ));
                        $eventStat->tickets_sold = 0;
                        $eventStat->sales_volume = 0.0;
                        $eventStat->save();
                    }
                }
            });
        });

        // This was rough I know but it was worth it.
    }

    /**
     * @return void
     */
    public function down()
    {
        // Nothing to do here. This can run multiple times and will only attempt to fix the stats across events,
        // tickets and orders in the database.
    }
}
