<?php

namespace App\Services\Cloudflare;

use GuzzleHttp\Client;

class Routing
{
    protected string $APIKey;

    protected string $zone;

    protected string $account;

    protected Client $client;

    public function __construct()
    {
        $this->APIKey = config('services.cloudflare.key');
        $this->zone = config('services.cloudflare.zone');
        $this->account = config('services.cloudflare.account');

        $this->client = new Client([
            'base_uri' => 'https://api.cloudflare.com',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->APIKey}",
            ],
        ]);
    }

    public function register(string $destination): string
    {
        if (! filter_var($destination, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email format');
        }

        return json_decode($this->client->post(
            "/client/v4/accounts/{$this->account}/email/routing/addresses",
            [
                'json' => ['email' => $destination],
            ]
        )->getBody()->getContents(), true)['result']['tag'];
    }

    public function removeDestinationAddress()
    {
        //
    }

    public function forward(string $destination, ?string $suffix = null): ?string
    {
        if (! filter_var($destination, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email format');
        }

        if (filled($suffix)) {
            $suffix = str($suffix)
                ->append('-')
                ->append(\Illuminate\Support\Str::random(8))
                ->slug()
                ->toString();
        }

        $slicedEmail = explode('@', $destination)[0];

        $from = "{$slicedEmail}-{$suffix}@pesenin.online";

        $response = $this->client->post(
            "/client/v4/zones/{$this->zone}/email/routing/rules",
            [
                'json' => [
                    'actions' => [[
                        'type' => 'forward',
                        'value' => [$destination],
                    ]],
                    'enabled' => true,
                    'matchers' => [[
                        'field' => 'to',
                        'type' => 'literal',
                        'value' => $from,
                    ]],
                    'name' => "Routing email from {$from} to {$destination}",
                    'priority' => 0,
                ],
            ],
        )->getBody()->getContents();

        if (! json_decode($response, true)['success']) {
            return null;
        }

        return $from;
    }
}
