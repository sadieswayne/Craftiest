<?php

declare(strict_types=1);

namespace App\Support\Chatbot;

use App\Models\Chatbot\Domain;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Foundation\Vite;
use Livewire\Exceptions\ComponentNotFoundException;
use Livewire\Mechanisms\ComponentRegistry;
use ReflectionClass;
use ReflectionException;

class ChatbotHelper
{
    /**
     * @throws ReflectionException
     */
    public static function isEmbeddable($component): bool
    {
        try {
            $reflectionClass = new ReflectionClass(app(ComponentRegistry::class)->new($component));
            $embedAttribute = $reflectionClass->getAttributes(Embeddable::class)[0] ?? null;

            return is_null($embedAttribute) === false;
        } catch (ComponentNotFoundException $e) {
            return false;
        }
    }

    private static function appSecret(): string
    {
        return md5(config('app.key'));
    }

    /**
     * App Domain for Token
     */
    private static function issForToken(): string
    {
        return parse_url(config('app.url'), PHP_URL_HOST); // foo.com
    }

    private static function getDomainIfExists(?string $appKey): ?Domain
    {
        if (! $appKey) {
            return null;
        }

        return Domain::findByAppKey($appKey);
    }

    public static function generateJWT(?string $appKey): false|string
    {
        if (! $domain = static::getDomainIfExists($appKey)) {
            return false;
        }

        return JWT::encode([
            'iss' => self::issForToken(), // App Domain
            'aud' => $domain->domain, // Allowed Domain
            'iat' => time(), // Token'ın oluşturulduğu zaman
            'nbf' => time(), // Token'ın geçerli olacağı en erken zaman
            'exp' => time() + 60 * 60, // Token'ın geçerlilik süresi (1 saat)
            'sub' => $appKey, // Token'ın ait olduğu kullanıcı
        ], static::appSecret(), 'HS256');
    }

    public static function validateJWT($token): false|array
    {
        try {
            $decoded = JWT::decode($token, new Key(static::appSecret(), 'HS256'));

            return (array) $decoded;
        } catch (Exception $e) {
            return false; // Token geçersiz
        }
    }

    public static function getContentTypeFromEntryPoint(string $entryPointUrl): string
    {
        return match (str($entryPointUrl)->afterLast('.')->toString()) {
            'js'    => 'application/javascript',
            'css'   => 'text/css',
            default => 'text/plain',
        };
    }

    public static function getViteAssets(string $entryPoint)
    {
        $files = app(Vite::class)($entryPoint);

        $files = str($files)->replace('build/', 'chatbot-assets/build/');

        // ray([$entryPoint => str($files)->replace('>', '>'.PHP_EOL)->toString()])->orange();

        return $files;
    }
}
