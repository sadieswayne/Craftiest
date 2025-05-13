<?php

namespace App\Services\Ai\OpenAI;

use App\Helpers\Classes\ApiHelper;
use App\Models\SettingTwo;
use App\Models\UserOpenaiChat;
use Exception;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\VectorStores\VectorStoreResponse;
use RuntimeException;

class FileSearchService
{
    protected string $apiKey;

    protected SettingTwo $settings_two;

    protected string $apiUrl = 'https://api.openai.com/v1/responses';

    public function __construct()
    {
        $this->apiKey = ApiHelper::setOpenAiKey();
        $this->settings_two = SettingTwo::getCache();
        set_time_limit(500);
    }

    /**
     * Upload a file to OpenAI's API.
     */
    public function uploadFile(string $filePath, string $purpose = 'user_data'): ?string
    {
        try {
            $response = OpenAI::files()->upload([
                'file'    => fopen($filePath, 'rb'),
                'purpose' => $purpose,
            ]);

            if ($response->status === 'processed') {
                return $response->id;
            }

            throw new RuntimeException("File upload failed with status: {$response->status}");
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create a vector store using OpenAI's API.
     */
    public function createVectorStore(string $name, string $fileIds): ?VectorStoreResponse
    {
        try {
            return OpenAI::vectorStores()->create([
                'name'     => $name,
                'file_ids' => [$fileIds],
            ]);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Check a vector store's status using OpenAI's API.
     */
    public function checkVectorStoreStatus(string $vectorStoreId): ?VectorStoreResponse
    {
        try {
            return OpenAI::vectorStores()->retrieve($vectorStoreId);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Perform a file search using OpenAI's API.
     */
    public function searchInFile(array $options, ?UserOpenaiChat $chat): ?string
    {
        $history = $options['messages'];
        $lastPrompt = end($history)['content'] ?? '';

        $payload = [
            'model' => $options['model'],
            'tools' => [
                [
                    'type'             => 'file_search',
                    'vector_store_ids' => [$chat?->openai_vector_id],
                ],
            ],
            'input' => $lastPrompt,
        ];

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post($this->apiUrl, $payload);

        if ($response->successful()) {
            $responseData = $response->json();

            $outputText = null;
            if (isset($responseData['output']) && is_array($responseData['output'])) {
                foreach ($responseData['output'] as $item) {
                    if ($item['type'] === 'message' && isset($item['content']) && is_array($item['content'])) {
                        foreach ($item['content'] as $content) {
                            if ($content['type'] === 'output_text' && isset($content['text'])) {
                                $outputText = $content['text'];

                                break 2;
                            }
                        }
                    }
                }
            }

            return $outputText;
        }

        return '';
    }
}
