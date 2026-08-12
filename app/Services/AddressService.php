<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\DB;

class AddressService
{
    // Inject RegionService agar bisa melalukan lookup nama wilayah
    public function __construct(
        protected RegionService $regionService
    ) {}

    public function getAddresses(int $userId) {
        return Address::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->latest()
            ->get();
    }

    public function createAddresses(int $userId, array $data) 
    {
        // gunakan transaction karena ada beberapa query (update dan insert)
        return DB::transaction(function () use ($userId, $data) {

            if ($data['is_default']) {
                Address::where('user_id', $userId)
                    ->update([
                        'is_default' => false
                    ]);
            }
            

            return Address::create([
                'user_id' => $userId,
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'label' => $data['label'],
                'full_address' => $data['full_address'],
                'province_id' => $data['province_id'],
                'province_name' => $data['province_name'],
                'city_id' => $data['city_id'],
                'city_name' => $data['city_name'],
                'district_id' => $data['district_id'],
                'district_name' => $data['district_name'],
                'subdistrict_id' => $data['subdistrict_id'] ?? null,
                'subdistrict_name' => $data['subdistrict_name'] ?? null,
                'postal_code' => $data['postal_code'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'is_default' => $data['is_default'] ?? false,
            ]);
        });
    }

    public function updateAddresses(int $userId, int $addressId, array $data)
    {
        return DB::transaction(function () use ($userId, $addressId, $data) {
            $address = Address::where('id', $addressId)
                ->where('user_id', $userId)
                ->firstOrFail();
         
            // jika address ini default dan address yang di update bukan default, maka set address yang di update menjadi default
            if ($address->is_default && !$data['is_default'])
            {
                $data['is_default'] = true;
            }

            // jika address ini default, maka set semua address lainnya menjadi non default
            if ($data['is_default'])
            {
                Address::where('user_id', $userId)
                    ->where('id', '!=', $addressId)
                    ->update([
                        'is_default' => false
                    ]);
            }

            $address->update([
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'label' => $data['label'],
                'full_address' => $data['full_address'],

                'province_id' => $data['province_id'],
                'province_name' => $data['province_name'],

                'city_id' => $data['city_id'],
                'city_name' => $data['city_name'],

                'district_id' => $data['district_id'],
                'district_name' => $data['district_name'],

                'subdistrict_id' => $data['subdistrict_id'] ?? null,
                'subdistrict_name' => $data['subdistrict_name'] ?? null,

                'postal_code' => $data['postal_code'],

                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,

                'is_default' => $data['is_default'] ?? false,
            ]);

            return $address->fresh();
        });
    }

    public function setDefaultAddress(int $userId, int $addressId) {

        $address = Address::where('id', $addressId)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        Address::where('user_id', $userId)
            ->update([
                'is_default' => false
            ]);

        $address->update([
            'is_default' => true
        ]);

        return $address->fresh();
    }

    public function deleteAddress(int $userId, int $addressId) {

        $address = Address::where('id', $addressId)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        $wasDefault = $address->is_default;

        $address->delete();

        if ($wasDefault) {

            // Cari address lain yang masih ada
            $newDefaultAddress = Address::where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->first();
            

            // Jika masih ada address lain, maka set address tersebut menjadi default
            if ($newDefaultAddress) {
                $newDefaultAddress->update([
                    'is_default' => true
                ]);
            }
        }
    }

}