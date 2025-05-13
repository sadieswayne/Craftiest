<?php

namespace App\Helpers\Classes;

use App\Domains\Entity\Enums\EntityEnum;
use App\Models\Chatbot\ChatbotData;
use App\Models\Chatbot\ChatbotDataVector;
use Exception;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAiHelper
{
    public static function embeddingData($chatbotId, array $data = [], array $trainedData = []): void
    {
        ApiHelper::setOpenAiKey();

        if (! is_array($trainedData)) {
            $trainedData = [];
        }

        $text = '';

        foreach ($data as $chatbotDataId => $text) {

            if (in_array($chatbotDataId, $trainedData)) {
                continue;
            }

            $trainedData[] = $chatbotDataId;

            $page = $text;

            if (! mb_check_encoding($text, 'UTF-8')) {
                $page = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));
            }

            $countWords = strlen($page) / 500 + 1;

            $meta_index = 1;

            for ($i = 0; $i < $countWords; $i++) {
                if (500 * $i + 1000 > strlen($page)) {
                    try {
                        $subtxt = substr($page, 500 * $i, strlen($page) - 500 * $i);
                        $subtxt = mb_convert_encoding($subtxt, 'UTF-8', 'UTF-8');
                        $subtxt = iconv('UTF-8', 'UTF-8//IGNORE', $subtxt);

                        $response = self::textVector($subtxt);

                        if (strlen(substr($page, 500 * $i, strlen($page) - 500 * $i)) > 10) {

                            ChatbotDataVector::query()->create([
                                'chatbot_id'      => $chatbotId,
                                'chatbot_data_id' => $chatbotDataId,
                                'content'         => substr($page, 500 * $i, strlen($page) - 500 * $i),
                                'embedding'       => $response,
                            ]);

                            self::trained($chatbotDataId);

                            $meta_index++;
                        }
                    } catch (Exception $e) {
                        //                        dd($e->getMessage());
                    }
                } else {
                    try {
                        $subtxt = substr($page, 500 * $i, 1000);
                        $subtxt = mb_convert_encoding($subtxt, 'UTF-8', 'UTF-8');
                        $subtxt = iconv('UTF-8', 'UTF-8//IGNORE', $subtxt);

                        $response = self::textVector($subtxt);

                        if (strlen(substr($page, 500 * $i, 1000)) > 10) {
                            ChatbotDataVector::query()->create([
                                'chatbot_id'      => $chatbotId,
                                'chatbot_data_id' => $chatbotDataId,
                                'content'         => substr($page, 500 * $i, 1000),
                                'embedding'       => $response,
                            ]);

                            self::trained($chatbotDataId);
                            $meta_index++;
                        }
                    } catch (Exception $e) {
                        //                        dd($e->getMessage());
                    }
                }
            }
        }
    }

    public static function trained($chatbotDataId)
    {
        ChatbotData::query()->where('id', $chatbotDataId)->update([
            'status' => 'trained',
        ]);
    }

    public static function textVector($text): ?array
    {
        $response = OpenAI::embeddings()->create([
            'model' => EntityEnum::TEXT_EMBEDDING_ADA_002->value,
            'input' => $text,
        ]);

        $embeddings = $response->embeddings;

        if (is_array($embeddings)) {
            $embedding = Arr::first($embeddings);

            if ($embedding && $embedding?->embedding) {
                return $embedding->embedding;
            }
        }

        return null;
    }
}
