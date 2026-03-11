<?php


namespace App\Zatca\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class ZatcaApiClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.zatca.base_url');
    }

    /**
     * @throws Exception
     */
    public function post(string $endpoint, array $payload): array
    {
        $response = Http::acceptJson()
            ->contentType('application/json')
            ->withOptions([
                'verify' => false,
            ])
            ->post($this->baseUrl . $endpoint, $payload);

        if ($response->failed()) {
            throw new Exception(
                $response->body() ?? 'ZATCA API request failed'
            );
        }

        return $response->json();
    }
}
