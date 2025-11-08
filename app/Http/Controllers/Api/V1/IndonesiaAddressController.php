<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\IndonesiaAddress\IndonesiaAddressServiceInterface;
use App\Helpers\ResponseHelper;

class IndonesiaAddressController extends Controller
{
    protected $indonesiaAddressService;

    public function __construct(IndonesiaAddressServiceInterface $indonesiaAddressService)
    {
        $this->indonesiaAddressService = $indonesiaAddressService;
    }

    /**
     * Get all provinces.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProvinces()
    {
        return ResponseHelper::success($this->indonesiaAddressService->getProvinces());
    }

    /**
     * Get all cities by province ID.
     *
     * @param string $provinceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(string $provinceId)
    {
        return ResponseHelper::success($this->indonesiaAddressService->getCities($provinceId));
    }

    /**
     * Get all districts by city ID.
     *
     * @param string $cityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistricts(string $cityId)
    {
        return ResponseHelper::success($this->indonesiaAddressService->getDistricts($cityId));
    }

    /**
     * Get all villages by district ID.
     *
     * @param string $districtId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVillages(string $districtId)
    {
        return ResponseHelper::success($this->indonesiaAddressService->getVillages($districtId));
    }
}