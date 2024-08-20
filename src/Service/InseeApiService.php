<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class InseeApiService
{
    private $client;
    private $consumerKey;
    private $consumerSecret;

    public function __construct(HttpClientInterface $client, string $consumerKey, string $consumerSecret)
    {
        $this->client = $client;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    public function getToken(): ?string
    {
        $response = $this->client->request('POST', 'https://api.insee.fr/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->consumerKey . ':' . $this->consumerSecret),
            ],
            'body' => [
                'grant_type' => 'client_credentials',
            ],
            'verify_peer' => false, // équivalent de -k dans curl
        ]);

        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();
            return $data['access_token'] ?? null;
        }

        return null;
    }

    public function getCompanyInfoBySiret(string $siret): ?array
    {
        $token = $this->getToken();

        if (!$token) {
            throw new \Exception('Unable to retrieve token');
        }

        $response = $this->client->request('GET', 'https://api.insee.fr/entreprises/sirene/V3.11/siret/' . $siret, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'verify_peer' => false,
        ]);

        if ($response->getStatusCode() === 200) {
            return $response->toArray();
        }

        return null;
    }
}
