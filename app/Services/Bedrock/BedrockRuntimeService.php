<?php

namespace App\Services\Bedrock;

use App\Enums\BedrockEngine;
use App\Models\SettingTwo;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BedrockRuntimeService
{
    protected BedrockRuntimeClient $client;

    public function __construct(array $config)
    {
        if (! empty($config['credentials']['key']) && ! empty($config['credentials']['secret'])) {
            $this->client = new BedrockRuntimeClient($config);
        }
    }

    public function invokeClaude($prompt)
    {
        return $this->invokeModel(setting('anthropic_bedrock_model', BedrockEngine::CLAUDE_21->value), $prompt);
    }

    protected function invokeModel($modelId, $prompt)
    {
        if ($modelId == BedrockEngine::CLAUDE_3_SONNET->value || $modelId == BedrockEngine::CLAUDE_3_HAIKU->value) {
            $result = $this->client->invokeModel([
                'modelId'     => $modelId,
                'contentType' => 'application/json',
                'accept'      => 'application/json',
                'body'        => json_encode([
                    'anthropic_version' => 'bedrock-2023-05-31',
                    'max_tokens'        => (int) setting('anthropic_max_output_length', 200),
                    'messages'          => [
                        [
                            'role'    => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                ]),
            ]);

            $data = json_decode($result['body'], true);
            $message = $data['content'][0]['text'];
            $response['completion'] = $message;

            return $response;
        } else {

            $formattedPrompt = "\n\nHuman: " . $prompt . "\n\nAssistant:";

            $result = $this->client->invokeModel([
                'modelId'     => $modelId,
                'contentType' => 'application/json',
                'body'        => json_encode([
                    'prompt'               => $formattedPrompt,
                    'max_tokens_to_sample' => (int) setting('anthropic_max_output_length', 200),
                    'temperature'          => 0.5,
                    'stop_sequences'       => ["\n\nHuman:"],
                ]),
            ]);

            return json_decode($result['body'], true);

        }
    }

    public function invokeStableDiffusion($prompt, $seed, $width, $height, $style_preset = null)
    {
        $base64_image_data = '';

        try {
            $setting = SettingTwo::getCache();
            $modelId = $setting->stablediffusion_bedrock_model;

            $body = [
                'text_prompts' => [
                    ['text' => $prompt],
                ],
                'seed'      => (int) $seed,
                'cfg_scale' => 10,
                'steps'     => 30,
                'width'     => (int) $width,
                'height'    => (int) $height,
            ];

            if ($style_preset) {
                $body['style_preset'] = $style_preset;
            }

            $result = $this->client->invokeModel([
                'modelId'     => $modelId,
                'contentType' => 'application/json',
                'accept'      => 'application/json',
                'body'        => json_encode($body),
            ]);

            $response_body = json_decode($result['body']);

            $base64_image_data = $response_body->artifacts[0]->base64;

            $nameOfImage = Str::random(12) . '-DALL-E-' . $prompt . '.png';
            Storage::disk('public')->put($nameOfImage, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64_image_data)));

            return '/uploads/' . $nameOfImage;

        } catch (AwsException $e) {
            echo "Error: ({$e->getStatusCode()}) - {$e->getAwsErrorMessage()}\n";
        }

        return $base64_image_data;
    }
}
