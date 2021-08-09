<?php

namespace App\Jobs;

use App\Mail\SendOrderNotificationMail;
use App\Models\Order;
use App\Services\Order as OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Config;
use Mail;

class SendOrderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $orderService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, OrderService $orderService)
    {
        $this->order = $order;
        $this->orderService = $orderService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new SendOrderNotificationMail($this->order, $this->orderService);
        Mail::to($this->order->event->organiser->email)
            ->locale(Config::get('app.locale'))
            ->send($mail);
    }
}
