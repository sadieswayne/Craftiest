<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Datlechin\GoogleTranslate\Facades\GoogleTranslate;
use Throwable;

class TranslateController extends Controller
{
    public function autoTranslate($lang)
    {
        // Load the English (en) and target language JSON files
        $enJsonFilePath = base_path('lang/en.json');
        $targetJsonFilePath = base_path("lang/$lang.json");

        // Get all keys from en.json file
        $enDataToTranslate = json_decode(file_get_contents($enJsonFilePath), true);

        // If the target language file doesn't exist, create a copy from en.json with null values
        if (! file_exists($targetJsonFilePath)) {
            $nullValuesArray = array_fill_keys(array_keys($enDataToTranslate), null);
            $newJsonF = json_encode($nullValuesArray, JSON_PRETTY_PRINT);
            file_put_contents($targetJsonFilePath, $newJsonF);
        }

        // Get all keys from the target language JSON file
        $targetDataToTranslate = json_decode(file_get_contents($targetJsonFilePath), true);

        // Identify missing keys in the target language file and add them
        $missingKeys = array_diff_key($enDataToTranslate, $targetDataToTranslate);
        if (! empty($missingKeys)) {
            foreach ($missingKeys as $key) {
                if (! isset($targetDataToTranslate[$key])) {
                    // Update the target language JSON file based on the translation
                    $targetDataToTranslate[$key] = null;
                }
            }
            // Update the target language JSON file
            $newJson = json_encode($targetDataToTranslate, JSON_PRETTY_PRINT);
            file_put_contents($targetJsonFilePath, $newJson);
        }

        // Translate 100 untranslated keys or all if less than 100
        $untranslatedKeys = array_filter($targetDataToTranslate, function ($value, $key) {
            if (empty($key) || $key === null) {
                // Handle the case where $key is empty or null (if needed)
                return false; // or return true; depending on your logic
            }

            return $value === null || $value === '';
        }, ARRAY_FILTER_USE_BOTH);
        $keysToTranslate = array_slice(array_keys($untranslatedKeys), 0, 100);

        // Check if there are keys to translate
        if (! empty($keysToTranslate)) {
            foreach ($keysToTranslate as $key) {

                // if there is an key in target not exist in main then copy it
                try {
                    $enKeyValue = $enDataToTranslate[$key];
                } catch (Throwable $th) {
                    $enDataToTranslate[$key] = $key;
                    // Update the English language JSON file
                    $newEnJson = json_encode($enDataToTranslate, JSON_PRETTY_PRINT);
                    file_put_contents($enJsonFilePath, $newEnJson);
                }
                if ($enDataToTranslate[$key] == null || empty($enDataToTranslate[$key]) || $enDataToTranslate[$key] == ' ') {
                    $enDataToTranslate[$key] = $key;
                }

                try {
                    // Translate the string using Google Translate API
                    $result = GoogleTranslate::withSource('en')
                        ->withTarget($lang)
                        ->translate($enDataToTranslate[$key]);
                } catch (Throwable $th) {
                    continue;
                }

                $translatedText = $result->getTranslatedText();

                // Update the target language JSON file based on the translation
                $targetDataToTranslate[$key] = $translatedText;

                // Update the Strings model based on the translation
                $column_name = $lang;
                $query = \Elseyyid\LaravelJsonLocationsManager\Models\Strings::query();

                if ($column_name == 'edit') {
                    // Logic for handling 'edit' column
                    $query->update([$column_name => $translatedText]);
                } else {
                    // Logic for other columns
                    $query->where('en', '=', $key)->update([$column_name => $translatedText]);
                }
            }

            // Update the target language JSON file
            $newJson = json_encode($targetDataToTranslate, JSON_PRETTY_PRINT);
            file_put_contents($targetJsonFilePath, $newJson);

            return back()->with(['message' => __('Translations have been updated successfully.'), 'type' => 'success']);
        } else {
            // All keys have been translated
            return back()->with(['message' => __('All translations have been completed.'), 'type' => 'info']);
        }

    }
}
