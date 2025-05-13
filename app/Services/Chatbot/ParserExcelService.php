<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ParserExcelService
{
    public string $text;

    public string $path;

    public function parse(): string
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(
            $this->path
        );

        $data = $spreadsheet->getActiveSheet()->toArray();

        if (empty($data)) {
            throw ValidationException::withMessages([
                'file' => trans('File format is not correct.'),
            ]);
        }

        $data = Arr::flatten($data);

        $data = array_filter($data, function ($value) {
            return ! empty($value);
        });

        $this->setText(implode(' ', $data));

        return $this->getText();
    }

    public function setPath(string $path): ParserExcelService
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
