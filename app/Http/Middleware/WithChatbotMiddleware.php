<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Chatbot\Domain;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait WithChatbotMiddleware
{
    protected ?string $appKey;

    protected ?string $originDomain;

    protected ?Domain $domain = null;

    protected Request $request;

    protected function checkDomain(bool $withKey = true): bool
    {
        $originDomain = parse_url($this->request()->headers->get('Origin'), PHP_URL_HOST);

        $domain = Domain::whereDomain($originDomain)
            ->when($withKey, function ($query) {
                return $query->whereAppKey($this->getAppKey());
            });

        if ($domain->exists()) {
            $this->setDomain($domain->first());

            $this->setOriginDomain($originDomain);

            return true;
        }

        return false;
    }

    protected function getAppKey(): ?string
    {
        return $this->appKey;
    }

    protected function getToken(): ?string
    {
        return $this->request()->bearerToken();
    }

    protected function request(): Request
    {
        return $this->request;
    }

    protected function getOriginDomain(): ?string
    {
        return $this->originDomain;
    }

    protected function getOriginUrl(): ?string
    {
        if ($originDomain = $this->getOriginDomain()) {
            return 'https://' . $originDomain;
        }

        return null;
    }

    protected function setAppKey(string $key): void
    {
        $this->appKey = $key;
    }

    protected function checkAppKey(bool $fromInput = false): bool
    {
        $appKey = $fromInput ? $this->request()->input('appKey') : $this->request()->headers->get('X-MagicAI-Chatbot');

        if (blank($appKey)) {
            return false;
        }

        if (! Str::isUuid($appKey)) {
            return false;
        }

        $this->setAppKey($appKey);

        return true;
    }

    public static function corsHeaders(string $originUrl = '*'): array
    {
        return [
            'Access-Control-Allow-Origin'      => $originUrl,
            'Access-Control-Allow-Methods'     => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Accept, Origin, Content-Type, X-MagicAI-Chatbot, X-Livewire, Authorization',
            'Access-Control-Allow-Credentials' => 'true',
        ];
    }

    protected function getCorsHeaders(): array
    {
        return static::corsHeaders($this->getOriginUrl());
    }

    protected function setOriginDomain(string $domain): void
    {
        $this->originDomain = $domain;
    }

    protected function setDomain(Domain $domain): void
    {
        $this->domain = $domain;
    }

    protected function getDomain(): Domain
    {
        return $this->domain;
    }

    private function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    private function loginUser(): void
    {
        if ($this->domain && $this->domain->chatbot) {
            if (! ($user = $this->domain->chatbot->user)) {
                $user = User::admins()->first();
            }

            Auth::login($user);
        } else {
            // ray(['User not found', $this->domain, $this->getToken(), $this->getAppKey(), $this->domain->chatbot->user]);
        }
    }
}
