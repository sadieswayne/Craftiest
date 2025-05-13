<?php

namespace App\Extensions\Midjourney\System\Http\Controllers;

use App\Extensions\Midjourney\System\Services\PiAPIService;
use App\Http\Controllers\Controller;
use App\Models\UserOpenai;
use Illuminate\Http\Request;

class MidjourneyCheckStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = UserOpenai::query()
            ->where('status', 'IN_QUEUE')
            ->where('response', 'PI')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([]);
        }

        self::updateImages();

        return response()->json([
            'data' => UserOpenai::query()
                ->whereIn('id', $data->pluck('id')->toArray())
                ->where('status', '<>', 'IN_QUEUE')
                ->get()
                ->map(function ($item) {
                    $item->setAttribute('imgId', 'img-' . $item->response . '-' . $item->id);
                    $item->setAttribute('payloadId', 'img-' . $item->response . '-' . $item->id . '-payload');
                    $item->setAttribute('img', ThumbImage($item->output));

                    return $item;
                }),
        ]);
    }

    public static function updateImages(): void
    {
        UserOpenai::query()
            ->where('response', 'PI')
            ->where('status', 'IN_QUEUE')
            ->whereNotNull('request_id')
            ->get()
            ->each(function ($item) {

                $output = PiAPIService::check($item->request_id);

                if ($output) {

                    $image = PiAPIService::downloadImageToStorage($output);

                    $item->update([
                        'output'  => $image ?: $item->output,
                        'status'  => 'COMPLETED',
                    ]);
                }
            });
    }
}
