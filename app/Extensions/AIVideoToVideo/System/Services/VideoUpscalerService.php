<?php

namespace App\Extensions\AIVideoToVideo\System\Services;

class VideoUpscalerService extends BaseService
{
    public function generate(): bool
    {
        $openai = $this->getOpenai();

        return $this->request($openai, [
            'video_url'   => url('uploads' . DIRECTORY_SEPARATOR . $openai['payload']['video']),
        ]);
    }
}
