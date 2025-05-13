<?php

namespace App\Observer;

use App\Helpers\Classes\MarketplaceHelper;
use App\Models\Usage;
use App\Models\User;
use Exception;
use Spatie\Newsletter\Facades\Newsletter;
use Throwable;

class UserObserver
{
    public function created($user): void
    {

        Usage::getSingle()->updateUserCount(1);

        $user->update(['entity_credits' => User::getFreshCredits()]);

        if ((int) setting('mailchimp_register') === 1) {
            try {
                Newsletter::subscribeOrUpdate(
                    $user->email,
                    ['FNAME' => $user->name, 'LNAME' => $user->surname],
                );
            } catch (Exception $e) {
            }
        }

        if ((int) setting('hubspot_crm_contact_register') === 1) {
            try {
                (new \App\Extensions\Hubspot\System\Services\HubspotService)->createCrmContacts($user->email, $user->name, $user->surname);
            } catch (Exception $e) {
            }
        }

        if (MarketplaceHelper::isRegistered('xero')) {
            try {
                config([
                    'xero.clientId'     => setting('XERO_CLIENT_ID'),
                    'xero.clientSecret' => setting('XERO_CLIENT_SECRET'),
                    'xero.redirectUri'  => setting('XERO_REDIRECT_URI'),
                    'xero.landingUri'   => setting('XERO_LANDING_URL'),
                ]);
                $response = \Dcblogdev\Xero\Facades\Xero::contacts()->store([
                    'Name' => $user->name,
                ]);
                $user->xero_account_id = $response['ContactID'] ?? null;
                $user->save();
            } catch (Throwable $e) {
            }
        }

        cache()->forget('instance_usage');
    }

    public function deleted($user): void
    {
        Usage::getSingle()->updateUserCount(-1);
        cache()->forget('instance_usage');
    }
}
