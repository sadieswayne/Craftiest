<?php

namespace App\Extensions\AIVideoToVideo\System\Services;

class FastAnimatediffTurboService extends BaseService
{
    public function generate(): bool
    {
        $openai = $this->getOpenai();

        $data = [
            'video_url'   => url('uploads' . DIRECTORY_SEPARATOR . $openai['payload']['video']),
            'prompt'      => $openai['payload']['prompt'],
        ];

        if (data_get($openai['payload'], 'negative_prompt')) {
            $data['negative_prompt'] = $openai['payload']['negative_prompt'];
        }

        if (data_get($openai['payload'], 'first_n_seconds')) {
            $data['first_n_seconds'] = $openai['payload']['first_n_seconds'];
        }

        return $this->request($openai, $data);
    }
}
