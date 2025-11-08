<?php

namespace App\Models;

use Hitech\IndonesiaLaravel\Models\City;
use Hitech\IndonesiaLaravel\Models\District;
use Hitech\IndonesiaLaravel\Models\Province;
use Hitech\IndonesiaLaravel\Models\Village;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'address_line1',
        'address_line2',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the address.
     *
     * @return BelongsTo<User, UserAddress>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the province for the address.
     *
     * @return BelongsTo<Province, UserAddress>
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    /**
     * Get the city for the address.
     *
     * @return BelongsTo<City, UserAddress>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Get the district for the address.
     *
     * @return BelongsTo<District, UserAddress>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Get the village for the address.
     *
     * @return BelongsTo<Village, UserAddress>
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
}

