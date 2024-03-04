<?php

namespace App\Services\Pawoon;

use App\Models\Integration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Service
{
    protected Client $client;

    public function __construct(Client $client, protected Integration $integration)
    {
        $this->client = $client;
    }

    public static function new(Integration $integration): static
    {
        $client = new Client([
            'base_uri' => 'https://open-api.pawoon.com',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        return new static($client, $integration);
    }

    public function connect(): bool
    {
        try {
            $response = $this->client->post('oauth/token', [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->integration->client_id,
                    'client_secret' => $this->integration->client_secret,
                ],
            ]);
        } catch (GuzzleException $th) {
            logger()->error($th->getMessage());

            if (!app()->isProduction()) {
                throw $th;
            }

            return false;
        }

        $responseContent = json_decode($response->getBody()->getContents(), true);

        return $this->integration->update([
            'access_token' => $responseContent['access_token'],
            'token_expiration' => $responseContent['expires_in'],
        ]);
    }

    public static function make(Integration $integration): static
    {
        $client = new Client([
            'base_uri' => 'https://open-api.pawoon.com',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$integration->access_token}"
            ],
        ]);

        return new static($client, $integration);
    }

    public function outlets(): ?array
    {
        try {
            $response = $this->client->get('/outlets');
        } catch (GuzzleException $th) {
            logger()->error($th->getMessage());

            return null;
        }

        return collect(json_decode($response->getBody()->getContents(), true)['data'])->transform(function ($outlet) {
            return (object) [
                'id' => $outlet['id'],
                'name' => $outlet['name'],
            ];
        })->toArray();
    }

    public function registerWebhook(string $type, string $url, string $businessId): ?string
    {
        try {
            $response = $this->client->post("/callback_urls/{$type}", [
                'headers' => ['for-user-id' => $businessId],
                'json' => ['url' => $url],
            ]);
        } catch (GuzzleException $th) {
            logger()->error($th->getMessage());

            return null;
        }

        return json_decode($response->getBody()->getContents(), true)['callback_token'];
    }
}
