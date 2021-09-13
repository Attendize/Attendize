<?php

if (!function_exists('money')) {
    /**
     * Format a given amount to the given currency
     *
     * @param $amount
     * @param \App\Models\Currency $currency
     * @return string
     */
    function money($amount, \App\Models\Currency $currency)
    {
        return $currency->symbol_left . number_format($amount, $currency->decimal_place, $currency->decimal_point,
            $currency->thousand_point) . $currency->symbol_right;
    }

    /**
     * Get extras price from questions
     * @param  object $ticket_order the ordered tickets
     * @return float               total extras price
     */
    function getExtrasPrice($ticket_order, $ticket_questions)
    {
        $extras_price = 0.0;
        foreach ($ticket_order['tickets'] as $attendee_details) {
            for ($i = 0; $i < $attendee_details['qty']; $i++) {
                foreach ($attendee_details['ticket']->questions as $question) {
                    $ticket_answer = isset($ticket_questions[$attendee_details['ticket']->id][$i][$question->id]) ? $ticket_questions[$attendee_details['ticket']->id][$i][$question->id] : null;
                    if (is_null($ticket_answer) || $ticket_answer == "") {
                        continue;
                    }

                    switch ($question->question_type_id) {
                    case 3: // Dropdown (single selection)
                    case 6: // Radio input
                        $extras_price += $question->options->firstWhere('id', $ticket_answer)->price;
                        break;
                    case 4: // Dropdown (multiple selection)
                    case 5: // Checkbox
                        foreach ($ticket_answer as $answer) {
                            $extras_price += $question->options->firstWhere('id', $answer)->price;
                        }
                        break;
                    default:
                        break;
                    }
                }
            }
        }
        return $extras_price;
    }
}


