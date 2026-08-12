<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RegionService;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(
        protected RegionService $regionService
    ){}

    public function provinces()
    {
        return response()->json(
            $this->regionService->getProvinces()
        );
    }

    public function cities(string $provinceId)
    {
        return response()->json(
            $this->regionService->getCities($provinceId)
        );
    }

    public function districts(string $cityId)
    {
        return response()->json(
            $this->regionService->getDistricts($cityId)
        );
    }

    public function subdistricts(string $districtId)
    {
        return response()->json(
            $this->regionService->getSubdistricts($districtId)
        );
    }

}
