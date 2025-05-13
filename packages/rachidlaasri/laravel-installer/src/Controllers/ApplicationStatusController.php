<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Helpers\Classes\Helper;
use App\Models\SettingTwo;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RachidLaasri\LaravelInstaller\Repositories\ApplicationStatusRepositoryInterface;
use RachidLaasri\LaravelInstaller\Requests\LicenseKeyRequest;

class ApplicationStatusController extends Controller
{
    public function __construct(public ApplicationStatusRepositoryInterface $licenseRepository) {}

    public function activate(Request $request): RedirectResponse
    {
        cache()->forget('check_license_domain_' . $request->getHost());

        $repository = app(ApplicationStatusRepositoryInterface::class);

        if ($repository->generate($request)) {

            $portal = $repository->portal();

            SettingTwo::query()->first()?->update([
                'liquid_license_domain_key' => $portal['liquid_license_domain_key'],
                'liquid_license_type'       => $portal['liquid_license_type'],
            ]);

            return redirect()->route('dashboard.index')->with([
                'type'    => 'success',
                'message' => 'License activated successfully',
            ]);
        }

        return redirect()->route('dashboard.index')->with([
            'type'    => 'error',
            'message' => 'License activation failed',
        ]);
    }

    public function license(Request $request, $regenerate = null)
    {
        cache()->forget('check_license_domain_' . $request->getHost());

        $this->licenseRepository->generate($request);

        $portalData = $this->licenseRepository->portal();

        try {
            if (! $portalData) {
                $check = Helper::settingTwo('liquid_license_domain_key');

                if ($check) {
                    $success = $this->licenseRepository->check(
                        $check, true
                    );

                    if ($success) {
                        return to_route('dashboard.user.index')->with([
                            'type'    => 'success',
                            'message' => 'License activated successfully',
                        ]);
                    }
                }
            }

        } catch (Exception $e) {
        }

        return view('vendor.installer.license', [
            'portal' => $portalData,
            'text'   => 'Activate',
        ]);
    }

    public function upgrade(Request $request): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        cache()->forget('check_license_domain_' . $request->getHost());

        $this->licenseRepository->generate($request);

        return view('vendor.installer.license', [
            'portal' => null,
            'text'   => 'Upgrade',
        ]);
    }

    public function licenseCheck(LicenseKeyRequest $request): RedirectResponse
    {
        cache()->forget('check_license_domain_' . $request->getHost());

        $this->licenseRepository->setLicense();

        return redirect()->route('dashboard.user.index');
    }

    public function webhook(Request $request)
    {
        cache()->forget('check_license_domain_' . $request->getHost());

        $this->licenseRepository->webhook($request);

        return response()->noContent();
    }
}
