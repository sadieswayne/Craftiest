<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRazorpayCustomerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $razorpay;

    protected $user;

    protected $userData;

    /**
     * Create a new job instance.
     */
    public function __construct($razorpay, $user, $userData)
    {
        $this->razorpay = $razorpay;
        $this->user = $user;
        $this->userData = $userData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $razorpayCustomer = $this->razorpay->customer->create($this->userData);
        $this->user->razorpay_id = $razorpayCustomer->id;
        $this->user->save();
    }
}
