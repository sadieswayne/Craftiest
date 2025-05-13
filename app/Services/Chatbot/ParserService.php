<?php

namespace App\Services\Chatbot;

use Smalot\PdfParser\Parser;

class ParserService
{
    public string $text;

    public string $pdfPath;

    public function __construct(
        public Parser $parser
    ) {}

    public function parse(): string
    {
        $this->setText(
            $this->parser
                ->parseFile($this->getPdfPath())
                ->getText()
        );

        return $this->getText();
    }

    public function setPdfPath(string $pdfPath): ParserService
    {
        $this->pdfPath = $pdfPath;

        return $this;
    }

    public function getPdfPath(): string
    {
        return $this->pdfPath;
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
