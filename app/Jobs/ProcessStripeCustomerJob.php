<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessStripeCustomerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $stripe;

    protected $user;

    protected $userData;

    /**
     * Create a new job instance.
     */
    public function __construct($stripe, $user, $userData)
    {
        $this->stripe = $stripe;
        $this->user = $user;
        $this->userData = $userData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $stripeCustomer = $this->stripe->customers->create($this->userData);
        $this->user->stripe_id = $stripeCustomer->id;
        $this->user->save();
    }
}
