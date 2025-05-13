<?php

namespace RachidLaasri\LaravelInstaller\Repositories;

use App\Models\SettingTwo;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ApplicationStatusRepository implements ApplicationStatusRepositoryInterface
{
    public string $baseLicenseUrl = 'https://portal.liquid-themes.com/api/license';

    public function financePage(string $view = 'panel.admin.finance.gateways.particles.finance'): string
    {
        if ($this->licenseType() === 'Extended License') {
            return $view;
        }

        return 'panel.admin.finance.gateways.particles.license';
    }

    public function financeLicense(): bool
    {
        return $this->licenseType() === 'Extended License';
    }

    public function licenseType(): ?string
    {
        $portal = $this->portal();

        return data_get($portal, 'liquid_license_type');
    }

    public function check(string $licenseKey, bool $installed = false): bool
    {
        $response = Http::get($this->baseLicenseUrl . DIRECTORY_SEPARATOR . $licenseKey);

        if ($response->ok() && $response->json('success')) {
            $portal = $this->portal() ?: [];

            $data = array_merge($portal, [
                'liquid_license_type'       => $response->json('licenseType'),
                'liquid_license_domain_key' => $licenseKey,
                'installed'                 => $installed,
            ]);

            return $this->save($data);
        }

        return false;
    }

    public function portal()
    {
        $data = Storage::disk('local')->get('portal');

        if ($data) {
            return unserialize(trim($data));
        }

        return null;
    }

    public function getVariable(string $key)
    {
        $portal = $this->portal();

        return data_get($portal, $key);
    }

    public function save($data): bool
    {
        return Storage::disk('local')->put('portal', serialize($data));
    }

    public function setLicense(): void
    {
        $data = $this->portal();

        if (is_null($data)) {
            return;
        }

        $data['installed'] = true;

        $this->save($data);

        if (
            Schema::hasTable('settings_two')
            && Schema::hasColumn('settings_two', 'liquid_license_type')
            && Schema::hasColumn('settings_two', 'liquid_license_domain_key')
        ) {
            SettingTwo::query()->first()->update([
                'liquid_license_type'       => $data['liquid_license_type'],
                'liquid_license_domain_key' => $data['liquid_license_domain_key'],
            ]);
        }
    }

    public function generate(Request $request): bool
    {
        if ($request->exists(['liquid_license_status', 'liquid_license_domain_key', 'liquid_license_domain_key'])) {
            return $this->check($request->input('liquid_license_domain_key'), true);
        }

        return false;
    }

    public function next($request, Closure $next)
    {

        $portal = $this->portal();

        if (is_null($portal)) {
            return redirect()->route('LaravelInstaller::license');
        }

        $liquid_license_domain_key = data_get($portal, 'liquid_license_domain_key');

        if (! $liquid_license_domain_key) {
            return redirect()->route('LaravelInstaller::license');
        }

        $blocked = data_get($portal, 'blocked');

        if ($blocked) {
            abort(500);
        }

        return $next($request);
    }

    public function webhook($request)
    {
        $portal = $this->portal();

        if ($portal) {
            $liquid_license_domain_key = data_get($portal, 'liquid_license_domain_key');
            $request_liquid_license_domain_key = $request->get('key');
            $app_key = $request->get('app_key');

            if ($liquid_license_domain_key == $request_liquid_license_domain_key && $request->get('isDisabled')) {

                $portal['blocked'] = true;

                $this->save($portal);

                return response()->noContent();
            } elseif ($liquid_license_domain_key == $request_liquid_license_domain_key) {
                $portal['blocked'] = false;

                $this->save($portal);

                return response()->noContent();
            }

            if ($request->get('forceBlock') && $app_key == $this->appKey()) {
                $portal['blocked'] = true;

                $this->save($portal);

                return response()->noContent();
            }
        }

        return response()->noContent();
    }

    public function appKey(): string
    {
        return md5(config('app.key'));
    }
}
