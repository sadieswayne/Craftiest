<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BadWord
{
    /**
     * Varsayılan olarak hatasız olarak başlatılır. Eğer hata varsa true olur.
     */
    protected bool $error = false;

    /**
     * Uygunsuz kelimeleri tutar. Bu kelimeler, kullanıcının veritabanına kaydettiği kelimelerdir.
     */
    protected array $badWords = [];

    /**
     * Hata olan kelimeleri tutar.
     */
    protected Collection $errorWords;

    /**
     * BadWord constructor.
     *
     * @param  string  $text  kullanıcının girdiği metin ya da prompt
     */
    public function __construct(
        public string $text,
    ) {
        $this->errorWords = collect();

        $this->check();
    }

    /**
     * Kullanıcının girdiği metni, veritabanındaki uygunsuz kelimelerle karşılaştırır.
     * Büyük küçük harf duyarlılığı yoktur.
     */
    public function check(): void
    {
        foreach ($this->getBadWords() as $badWord) {
            if (str_contains(strtolower($this->text), $badWord)) {
                $this->error = true;
                $this->errorWords->push($badWord);
            }
        }
    }

    /**
     * Eğer hata varsa true döner.
     */
    public function hasError(): bool
    {
        return $this->error;
    }

    /**
     * Eğer hata varsa, hatalı kelimeleri döner.
     */
    public function getErrors(): Collection
    {
        return $this->errorWords;
    }

    /**
     * Kullanıcının veritabanına kaydettiği uygunsuz kelimeleri döner.
     */
    protected function getBadWords(): array
    {
        return Auth::user()->badWord->getWordsAsArray();
    }
}
