<?php

namespace App\Services\Xendit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;

class Account
{
    protected string $APIKey;

    protected Client $client;

    public function __construct()
    {
        $this->APIKey = base64_encode(config('services.xendit.secret_key') . ':');

        $this->client = new Client([
            'base_uri' => 'https://api.xendit.co',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Basic {$this->APIKey}",
            ],
        ]);
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

    public function createAccount(array $data = []): ?string
    {
        $rules = [
            'email' => ['required', 'email', 'max:255'],
            'type' => ['in:MANAGED'],
            'public_profile' => ['required', 'array'],
            'public_profile.business_name' => ['required', 'string', 'max:255'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            logger()->error('Account creation fails: ', $validator->errors()->toArray());

            return null;
        }

        try {
            $response = $this->client->post('/v2/accounts', ['json' => $data]);
        } catch (GuzzleException $th) {
            logger()->error($th->getMessage());

            return null;
        }

        return json_decode($response->getBody()->getContents(), true)['id'];
    }
}
