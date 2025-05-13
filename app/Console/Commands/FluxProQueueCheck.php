<?php

namespace App\Console\Commands;

use App\Domains\Engine\Services\FalAIService;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FluxProQueueCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:flux-pro-queue-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        self::updateFluxProImages();
    }

    public static function updateFluxProImages(): void
    {
        UserOpenai::query()
            ->where('response', 'FL')
            ->where('status', 'IN_QUEUE')
            ->whereNotNull('request_id')
            ->get()
            ->each(function ($item) {
                $output = FalAIService::check($item->request_id);

                if ($output) {
                    $payload = data_get($item, 'payload');

                    if ($payload && is_array($payload)) {
                        $payload['size'] = data_get($output, 'size');
                    }

                    $image = data_get($output, 'image.url');

                    $image = static::downloadImageToStorage($image);

                    $item->update([
                        'output'  => $image ?: $item->output,
                        'payload' => $payload,
                        'status'  => 'COMPLETED',
                    ]);
                }
            });
    }

    public static function downloadImageToStorage($url = null, $filename = null)
    {
        if (! $url) {
            return null;
        }

        // Resmi URL'den indir
        $response = Http::get($url);

        // Eğer dosya başarıyla indirildiyse devam et
        if ($response->successful()) {
            // Dosya içeriğini al
            $fileContent = $response->body();

            // Dosya uzantısını belirleyin
            $extension = pathinfo($url, PATHINFO_EXTENSION);

            // Eğer dosya adı verilmemişse, bir dosya adı oluşturun
            if (! $filename) {
                $filename = uniqid('image_') . '.' . $extension;
            } else {
                $filename .= '.' . $extension;
            }

            $image_storage = SettingTwo::query()->first()?->ai_image_storage;

            if ($image_storage === 'r2') {
                Storage::disk('r2')->put($filename, $fileContent);

                return Storage::disk('r2')->url($filename);
            } elseif ($image_storage === 's3') {

                Storage::disk('s3')->put($filename, $fileContent);

                return Storage::disk('s3')->url($filename);
            }

            // save file on local storage or aws s3
            Storage::disk('thumbs')->put($filename, $fileContent);

            $dump = Storage::disk('public')->put($filename, $fileContent);

            if ($dump) {
                return '/uploads/' . $filename;
            }

            return 'error';
        }

        // return false when fail
        return null;
    }
}
