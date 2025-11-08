<?php

namespace App\Services\IndonesiaAddress;

use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

interface IndonesiaAddressServiceInterface {
    /**
     * Get all provinces.
     *
     * @return Collection
     */ 
    public function getProvinces(): Collection;

    /**
     * Get all cities by province ID.
     *
     * @param string $provinceId
     * @return Collection
     */
    public function getCities(string $provinceId): Collection;

    /**
     * Get all districts by city ID.
     *
     * @param string $cityId
     * @return Collection
     */
    public function getDistricts(string $cityId): Collection;

    /**
     * Get all villages by district ID.
     *
     * @param string $districtId
     * @return Collection
     */
    public function getVillages(string $districtId): Collection;
}