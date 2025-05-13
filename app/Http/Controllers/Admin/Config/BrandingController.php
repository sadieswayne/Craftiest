<?php

namespace App\Http\Controllers\Admin\Config;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandingController extends Controller
{
    protected $settings;

    public function __construct()
    {
        $this->settings = Setting::query()->first();
    }

    private function getFileInfo($filePath): array
    {
        if (file_exists($filePath) && is_file($filePath)) {
            $fileSizeKB = round(filesize($filePath) / 1024, 2);
            $dimensions = getimagesize($filePath);
            $width = $dimensions[0];
            $height = $dimensions[1];
            $size = $width . ' X ' . $height;
        } else {
            $fileSizeKB = 0;
            $size = 'N/A';
        }

        return [
            'fileSizeKB' => $fileSizeKB,
            'size'       => $size,
        ];
    }

    public function index(): View
    {
        // $xInfo = $this->getFileInfo(public_path('upload/images/logo/' . $this->settings->x_logo));
        // $xxInfo = $this->getFileInfo(public_path('upload/images/logo/' . $this->settings->xx_logo));
        // $faviconInfo = $this->getFileInfo(public_path('upload/images/favicon/' . $this->settings->favicon));

        /*
        return view('panel.admin.config.branding', [
            'xFileSizeKB'   => $xInfo['fileSizeKB'],
            'xSize'         => $xInfo['size'],
            'xxFileSizeKB'  => $xxInfo['fileSizeKB'],
            'faviconSizeKB' => $faviconInfo['fileSizeKB'],
            'xxSize'        => $xxInfo['size'],
            'faviconSize'   => $faviconInfo['size'],
        ]);
        */

        return view('panel.admin.config.branding');
    }

    public function favicon(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $faviconPath = 'upload/images/favicon/';

        if ($request->hasFile('favicon')) {
            $request->validate([
                'favicon' => 'image|mimes:jpg,jpeg,png,svg|max:2048',
            ]);
            $image = $request->file('favicon');
            $image_name = Str::random(4) . '-' . Str::slug($this->settings->site_name) . '-favicon.' . $image->getClientOriginalExtension();
            $image->move($faviconPath, $image_name);
            $this->settings->update([
                'favicon'      => $image_name,
                'favicon_path' => $faviconPath . $image_name,
            ]);
        }

        Setting::forgetCache();

        return back()->with(['message' => 'Updated Successfully.', 'type' => 'success']);
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $settings = Setting::query()->first();

        $logo_types = [
            'logo'                => '',
            'logo_dark'           => 'dark',
            'logo_sticky'         => 'sticky',
            'logo_dashboard'      => 'dashboard',
            'logo_dashboard_dark' => 'dashboard-dark',
            'logo_collapsed'      => 'collapsed',
            'logo_collapsed_dark' => 'collapsed-dark',
            // retina
            'logo_2x'                => '2x',
            'logo_dark_2x'           => 'dark-2x',
            'logo_sticky_2x'         => 'sticky-2x',
            'logo_dashboard_2x'      => 'dashboard-2x',
            'logo_dashboard_dark_2x' => 'dashboard-dark-2x',
            'logo_collapsed_2x'      => 'collapsed-2x',
            'logo_collapsed_dark_2x' => 'collapsed-dark-2x',
        ];

        foreach ($logo_types as $logo => $logo_prefix) {

            if ($request->hasFile($logo)) {
                $path = 'upload/images/logo/';
                $image = $request->file($logo);
                $image_name = Str::random(4) . '-' . $logo_prefix . '-' . Str::slug($settings->site_name) . '-logo.' . $image->getClientOriginalExtension();

                // Resim uzantı kontrolü
                $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                    $data = [
                        'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                    ];

                    return response()->json($data, 419);
                }

                $image->move($path, $image_name);

                $settings->{$logo . '_path'} = $path . $image_name;
                $settings->{$logo} = $image_name;
                $settings->save();
            }

        }

        if ($request->hasFile('favicon')) {
            $path = 'upload/images/favicon/';
            $image = $request->file('favicon');
            $image_name = Str::random(4) . '-' . Str::slug($settings->site_name) . '-favicon.' . $image->getClientOriginalExtension();

            // Resim uzantı kontrolü
            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                $data = [
                    'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                ];

                return response()->json($data, 419);
            }

            $image->move($path, $image_name);

            $settings->favicon_path = $path . $image_name;
            $settings->favicon = $image_name;
            $settings->save();
        }

        $this->settings->update([
            'site_name'  => $request->get('site_name'),
            'site_url'   => $request->get('site_url'),
            'site_email' => $request->get('site_email'),
        ]);

        $this->settings->save();

        Setting::forgetCache();

        return back()->with(['message' => 'Updated Successfully.', 'type' => 'success']);
    }
}
