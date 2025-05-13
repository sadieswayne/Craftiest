<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\Setting;
use App\Models\SettingTwo;

class ExtensionController extends Controller
{
    protected $settings;

    protected $settings_two;

    public function __construct()
    {
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
    }

    // /**
    //  * Gets installed extensions
    //  *
    //  * @OA\Get(
    //  *      path="/api/extensions",
    //  *      operationId="extensionIndex",
    //  *      tags={"Extensions"},
    //  *      security={{ "passport": {} }},
    //  *      summary="Gets installed extensions",
    //  *      description="Returns installed extensions",
    //  *      @OA\Response(
    //  *          response=200,
    //  *          description="Successful operation",
    //  *      ),
    //  *      @OA\Response(
    //  *          response=401,
    //  *          description="Unauthenticated",
    //  *      ),
    //  */
    public function extensionIndex()
    {
        $extensions = Extension::where('installed', 1)->get();

        return response()->json($extensions);
    }
}
