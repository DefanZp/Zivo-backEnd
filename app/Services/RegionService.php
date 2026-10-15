<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class RegionService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.rajaongkir.base_url'),
            '/'
        );

        $this->apiKey = config('services.rajaongkir.api_key');
    }

    private function fetch(string $endpoint): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $response = Http::acceptJson()
            ->withHeaders([
                'key' => $this->apiKey,
            ])
            ->connectTimeout(8)
            ->timeout(20)
            ->retry(3, 500)
            ->get($url);

        $response->throw();

        $data = $response->json();

        if (!is_array($data)) {
            throw new \UnexpectedValueException(
                'RajaOngkir returned an invalid response.'
            );
        }

        return $data;
    }

    private function rememberRegion(
        string $cacheKey,
        string $endpoint
    ): array {
        return Cache::remember(
            $cacheKey,
            now()->addDays(7),
            fn () => $this->fetch($endpoint)
        );
    }

    public function getProvinces(): array
    {
        return $this->rememberRegion(
            'regions.provinces',
            'destination/province'
        );
    }

    public function getCities(string $provinceId): array
    {
        return $this->rememberRegion(
            "regions.cities.{$provinceId}",
            "destination/city/{$provinceId}"
        );
    }

    public function getDistricts(string $cityId): array
    {
        return $this->rememberRegion(
            "regions.districts.{$cityId}",
            "destination/district/{$cityId}"
        );
    }

    public function getSubdistricts(string $districtId): array
    {
        return $this->rememberRegion(
            "regions.subdistricts.{$districtId}",
            "destination/sub-district/{$districtId}"
        );
    }
}