<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class RegionService
{
    private string $baseurl;
    private string $apikey;

    // inject base url dan api key dari service config
    public function __construct()
    {
        $this->baseurl = config('services.rajaongkir.base_url');
        $this->apikey = config('services.rajaongkir.api_key');
    }

    public function getProvinces()
    {
        return Cache::remember('regions.provinces', now()->addDays(7), function () {
            return Http::withHeaders([
                'key' => $this->apikey,
            ])->get( 
                $this->baseurl . 'destination/province')
                ->json();
        });
    }

    public function getCities(string $provinceId)
    {
        return Cache::remember(
            "regions.cities.{$provinceId}", now()->addDays(7), function () use($provinceId) {
                return Http::withHeaders([
                    'key' => $this->apikey,
                ])->get(
                    $this->baseurl . 'destination/city/' . $provinceId
                )->json();
        });
    }

    public function getDistricts(string $cityId)
    {
        return Cache::remember(
            "regions.districts.{$cityId}", now()->addDays(7), function () use ($cityId) {
                return Http::withHeaders([
                    'key' => $this->apikey,
                ])->get(
                    $this->baseurl . 'destination/district/' . $cityId
                )->json();
            }
        );
    }

    public function getSubdistricts(string $districtId)
    {
        return Cache::remember(
            "regions.subdistricts.{$districtId}", now()->addDays(7), function () use ($districtId) {
                return Http::withHeaders([
                    'key' => $this->apikey,
                ])->get(
                    $this->baseurl . 'destination/sub-district/' . $districtId
                )->json();
            }
        );
    }
}