<?php


namespace App\Zatca\Services;

use Exception;

class ZatcaOnboardingService
{
    protected ZatcaApiClient $client;

    public function __construct(ZatcaApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function onboard(string $otp, array $csrConfig): array
    {
        return $this->client->post('/onboard', [
            'otp' => $otp,
            'csrConfig' => $csrConfig,
        ]);
    }
    /**
     * @throws Exception
     */
    public function renewCertificate(string $otp, string $csr, string $secret, string $binarySecurityToken): array
    {
        return $this->client->post('/renew', [
            'otp' => $otp,
            'csr' => $csr,
            'secret' => $secret,
            'binarySecurityToken' => $binarySecurityToken,
        ]);
    }
}

