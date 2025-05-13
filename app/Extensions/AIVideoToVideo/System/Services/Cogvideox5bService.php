<?php

namespace App\Extensions\AIVideoToVideo\System\Services;

class Cogvideox5bService extends BaseService
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
        if (data_get($openai['payload'], 'num_inference_steps')) {
            $data['num_inference_steps'] = $openai['payload']['num_inference_steps'];
        }

        return $this->request($openai, $data);
    }
}
