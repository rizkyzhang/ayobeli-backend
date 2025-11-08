<?php

namespace App\Services\IndonesiaAddress;

use Hitech\IndonesiaLaravel\Services\IndonesiaService;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndonesiaAddressService implements IndonesiaAddressServiceInterface
{
    protected $indonesiaService;

    public function __construct(IndonesiaService $indonesiaService)
    {
        $this->indonesiaService = $indonesiaService;
    }

    /**
     * Get all provinces.
     *
     * @return Collection
     */
    public function getProvinces(): Collection
    {
        return $this->indonesiaService->allProvinces();
    }

    /**
     * Get all cities by province ID.   
     *
     * @param string $provinceId
     * @return Collection
     */
    public function getCities(string $provinceId): Collection
    {
        return $this->indonesiaService->findCitiesByProvinceCode($provinceId);
    }

    /**
     * Get all districts by city ID.
     *
     * @param string $cityId
     * @return Collection
     */
    public function getDistricts(string $cityId): Collection
    {
        return $this->indonesiaService->findDistrictsByCityCode($cityId);
    }

    /**
     * Get all villages by district ID.
     *
     * @param string $districtId
     * @return Collection
     */
    public function getVillages(string $districtId): Collection
    {
        return $this->indonesiaService->findVillagesByDistrictCode($districtId);
    }
}