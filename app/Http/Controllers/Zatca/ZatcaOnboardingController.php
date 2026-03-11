<?php

namespace App\Http\Controllers\Zatca;

use App\Http\Controllers\Controller;
use App\Http\Requests\Zatca\ZatcaOnboardingRequest;
use App\Models\SubscriberZatcaCredential;
use App\Zatca\Services\ZatcaOnboardingService;
use AWS\CRT\HTTP\Request;
use Google\Api\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ZatcaOnboardingController extends Controller
{
    public function __construct(
        protected ZatcaOnboardingService $onboardingService
    ) {}

    public function store(ZatcaOnboardingRequest $request): JsonResponse
    {
        $subscriber = auth('admin')->user()->subscribers;
        $existing = SubscriberZatcaCredential::where('subscriber_id', $subscriber->id)
            ->where('environment', config('services.zatca.environment'))
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Subscriber already onboarded for this environment.'
            ], 409);
        }
        $appName = config('app.name');
        $version = config('app.version');


        $uuid = (string) Str::uuid();

        $csrConfig = [
            'commonName' => $appName,

            'serialNumber' => sprintf(
                '1-%s|2-%s|3-%s',
                $appName,
                $version,
                $uuid
            ),

            'organizationIdentifier' => $subscriber->tax_number,

            'organizationUnitName' => 'Main Branch',

            'organizationName' => $subscriber->company_name,

            'locationAddress' => $subscriber->address->locationAddress,

            'countryName' => $subscriber->country_code,

            'industryBusinessCategory' => 'Dental Laboratory Services',

            'functionalityMap' => 'BOTH',
        ];
        DB::beginTransaction();

        try {
            $response = $this->onboardingService->onboard(
                $request->otp,
                $csrConfig
            );
            SubscriberZatcaCredential::create(
                [
                    'subscriber_id' => $subscriber->id,
                    'private_key' => $response['privateKey'],
                    'csr' => $response['csr'],
                    'binary_security_token' => $response['binarySecurityToken'],
                    'secret' => $response['secret'],
                    'last_invoice_hash' => config('services.zatca.initial_invoice_hash'),
                    'onboarded_at' => now(),
                    'certificate_expiry_date' => $response['expiryDate'],
                ]
            );

            DB::commit();
            Cache::forget("subscriber_onboarded:{$subscriber->id}");
            Cache::put("subscriber_onboarded:{$subscriber->id}", true, now()->addHours(12));

            return response()->json([
                'message' => 'ZATCA onboarding completed successfully.',
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'ZATCA onboarding failed.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
    public function renew(ZatcaOnboardingRequest $request): JsonResponse
    {


        $subscriber = auth('admin')->user();

        $credential = SubscriberZatcaCredential::where('subscriber_id', $subscriber->subscriber_id)
            ->latest('onboarded_at')
            ->first();

        if (!$credential) {
            return response()->json([
                'message' => 'No onboarded certificate found to renew.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $response = $this->onboardingService->renewCertificate(
                $request->otp,
                $credential->csr,
                $credential->secret,
                $credential->binary_security_token
            );

            $credential->update([
                'private_key' => $response['privateKey'] ?? $credential->private_key,
                'csr' => $response['csr'] ?? $credential->csr,
                'binary_security_token' => $response['binarySecurityToken'] ?? $credential->binary_security_token,
                'secret' => $response['secret'] ?? $credential->secret,
                'certificate_expiry_date' => $response['expiryDate'] ?? $credential->certificate_expiry_date,
                'onboarded_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Certificate renewed successfully.',
                'data' => $response['dispositionMessage']
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Certificate renewal failed.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
