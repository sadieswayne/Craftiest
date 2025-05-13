<?php

declare(strict_types=1);

namespace App\Domains\Marketplace\Helpers;

/**
 * Class StringParser
 *
 * This class is responsible for parsing a specially formatted string that contains both elements separated by commas
 * and an array enclosed in square brackets. It breaks down the input string into individual parts and converts the array
 * part into a PHP array format for easier handling.
 */
class StringParser
{
    /**
     * Splits the input string into individual elements and the last array part.
     *
     * @param  string  $inputString  the string to be parsed
     *
     * @return array an associative array containing 'elements' (split by commas) and 'last_array' (parsed array)
     */
    public function splitStringAndArray(string $inputString): array
    {
        // Extract the last array from the string
        $lastArray = $this->extractLastArray($inputString);
        // Split the remaining parts by comma
        $views = $this->splitViews($inputString);
        // Parse the extracted array into a PHP array
        $parsedArray = $this->parseArray($lastArray);

        return [
            'views'      => $views,
            'data'       => $parsedArray,
        ];
    }

    /**
     * Extracts the last array part from the input string.
     *
     * @param  string  &$inputString  The input string, passed by reference to modify the original
     *
     * @return string the extracted array part as a string
     */
    private function extractLastArray(string &$inputString): string
    {
        preg_match('/\[(.*?)\]$/', $inputString, $lastArray);
        if (! empty($lastArray)) {
            // Extract the last array and remove it from the original string
            $lastArray = $lastArray[0];
            $inputString = preg_replace('/\[(.*?)\]$/', '', $inputString) ?? $inputString;
        } else {
            $lastArray = '';
        }

        return $lastArray;
    }

    /**
     * Splits the input string by commas and trims whitespace from each element.
     *
     * @param  string  $inputString  the string to be split
     *
     * @return array an array of trimmed elements
     */
    private function splitViews(string $inputString): array
    {
        return array_filter(array_map(function ($element) {
            return trim($element, "' ");
        }, explode(',', $inputString)));
    }

    /**
     * Parses the extracted array string into a PHP array.
     *
     * @param  string  $lastArray  the extracted array string
     *
     * @return array the parsed PHP array
     */
    private function parseArray(string $lastArray): array
    {
        if ($lastArray) {
            // Remove the square brackets and split by commas
            $lastArray = trim($lastArray, '[]');
            $lastArrayElements = array_map('trim', explode(',', $lastArray));
            $parsedArray = [];
            foreach ($lastArrayElements as $element) {
                // Check if the element is a key-value pair
                if (strpos($element, '=>') !== false) {
                    [$key, $value] = explode('=>', $element);
                    $key = trim($key, "' ");
                    $value = trim($value, "' ");
                    $parsedArray[$key] = $value;
                } else {
                    // If it's not a key-value pair, add it as a value
                    $parsedArray[] = trim($element, "' ");
                }
            }

            return $parsedArray;
        }

        return [];
    }
}
