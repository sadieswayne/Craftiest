<?php

namespace App\Services;

use App\Domains\Entity\Enums\EntityEnum;
use App\Helpers\Classes\ApiHelper;
use App\Models\Chatbot\ChatbotDataVector;
use App\Models\PdfData;
use OpenAI\Laravel\Facades\OpenAI;

class VectorService
{
    /**
     * Retrieve vectors for a given text.
     *
     * @return string
     */
    public function getMostSimilarText(string $text, $chat_id, $count = 5, $chatbot_id = null)
    {
        // api key update
        ApiHelper::setOpenAiKey();

        $chatbot_id = $chatbot_id ?? request('chatbot_id', 0);

        $vectors = PdfData::where('chat_id', $chat_id)->get();

        $chatbotVectors = ChatbotDataVector::query()
            ->where('chatbot_id', $chatbot_id)
            ->where('embedding', '!=', null)
            ->get();

        if (count($vectors) == 0 && $chatbotVectors->count() == 0) {
            return '';
        }

        $vector = OpenAI::embeddings()->create([
            'model' => EntityEnum::TEXT_EMBEDDING_ADA_002->value,
            'input' => $text,
        ])->embeddings[0]->embedding;

        $similarVectors = [];

        foreach ($vectors as $v) {

            $cosineSimilarity = $this->calculateCosineSimilarity($vector, json_decode($v['vector']));

            $similarVectors[] = [
                'id'         => $v['id'],
                'content'    => $v['content'],
                'similarity' => $cosineSimilarity,
            ];
        }

        if ($chatbotVectors) {
            foreach ($chatbotVectors as $v) {
                $cosineSimilarity = $this->calculateCosineSimilarity($vector, $v['embedding']);

                $similarVectors[] = [
                    'id'         => $v['id'],
                    'content'    => $v['content'],
                    'similarity' => $cosineSimilarity,
                ];
            }
        }

        usort($similarVectors, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $result = '';
        $resArr = array_slice($similarVectors, 0, $count);

        foreach ($resArr as $item) {
            $result = $result . $item['content'] . "\n\n\n";
        }

        return $result;
    }

    // public function getTextsFromIds(array $ids): array
    // {
    //     $texts = TextData::whereIn('id', $ids)->get()->toArray();
    //     $textsById = [];

    //     foreach ($texts as $text) {
    //         $textsById[$text['id']] = $text['text'];
    //     }

    //     $textsOrderedByIds = [];

    //     foreach ($ids as $id) {
    //         if (isset($textsById[$id])) {
    //             $textsOrderedByIds[] = $textsById[$id];
    //         }
    //     }

    //     return $textsOrderedByIds;
    // }

    // /**
    //  * Retrieve the text for a given vector ID.
    //  *
    //  * @param int $vectorId
    //  * @return string|null
    //  */
    // public function getTextForVector(int $vectorId): ?string
    // {
    //     $text = DB::table('texts')
    //         ->select('text')
    //         ->where('id', '=', DB::raw("(SELECT text_id FROM vectors WHERE id = $vectorId)"))
    //         ->first();

    //     return $text ? $text->text : null;
    // }

    // /**
    //  * Retrieve the most similar vectors for a given vector.
    //  *
    //  * @param array $vector
    //  * @param int $limit
    //  * @return array
    //  */
    // public function getMostSimilarVectors(array $vector, $file_id, int $limit = 10): array
    // {
    //     $vectors = TextVector::where('file_id', $file_id)->get()
    //         ->map(function ($vector) {
    //             return [
    //                 'id' => $vector->id,
    //                 'text_id' => $vector->text_id,
    //                 'vector' => json_decode($vector->vector, true)
    //             ];
    //         })
    //         ->toArray();

    //     $similarVectors = [];
    //     foreach ($vectors as $v) {
    //         $cosineSimilarity = $this->calculateCosineSimilarity($vector, $v['vector']);
    //         $similarVectors[] = [
    //             'id' => $v['id'],
    //             'similarity' => $cosineSimilarity
    //         ];
    //     }

    //     usort($similarVectors, function ($a, $b) {
    //         return $b['similarity'] <=> $a['similarity'];
    //     });

    //     return array_slice($similarVectors, 0, $limit);
    // }

    /**
     * Calculate the cosine similarity between two vectors.
     */
    private function calculateCosineSimilarity(array $v1, array $v2): float
    {
        $dotProduct = 0;
        $v1Norm = 0;
        $v2Norm = 0;

        foreach ($v1 as $i => $value) {
            $dotProduct += $value * $v2[$i];
            $v1Norm += $value * $value;
            $v2Norm += $v2[$i] * $v2[$i];
        }

        $v1Norm = sqrt($v1Norm);
        $v2Norm = sqrt($v2Norm);

        return $dotProduct / ($v1Norm * $v2Norm);
    }
}
