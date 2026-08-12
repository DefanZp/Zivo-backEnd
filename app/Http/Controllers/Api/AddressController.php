<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AddressController extends Controller
{
    public function __construct(
        protected AddressService $addressService
    ){}

    public function index (Request $request) {

        $userId = $request->user()->id;
        
        $addresses = $this->addressService
            ->getAddresses($userId);

        return response()->json([
            'success' => true,
            'message' => 'Addresses retrieved successfully',
            'data' => $addresses
        ], 200);
    }

    public function store(Request $request) {

        $validatedData = $request->validate([
            'recipient_name' => 'required|string|max:255',  
            'phone' => 'required|string|max:20',
            'label' => 'required|string|max:50',
            'full_address' => 'required|string',
            'province_id' => 'required|string',
            'province_name' => 'required|string',
            'city_id' => 'required|string',
            'city_name' => 'required|string',
            'district_id' => 'required|string',
            'district_name' => 'required|string',
            'subdistrict_id' => 'nullable|string',
            'subdistrict_name' => 'nullable|string',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'required|boolean'
        ]);

        $userId = $request->user()->id;

        $addresses = $this->addressService
            ->createAddresses(
                $userId, 
                $validatedData
        );

        return response()->json([
            'success' => true,
            'message' => 'Addresses created successfully',
            'data' => $addresses
        ], 201);
    }

    public function update(Request $request, int $addressId) {

        $validatedData = $request->validate([
            'recipient_name' => 'required|string|max:255',  
            'phone' => 'required|string|max:20',
            'label' => 'required|string|max:50',
            'full_address' => 'required|string',
            'province_id' => 'required|string',
            'province_name' => 'required|string',
            'city_id' => 'required|string',
            'city_name' => 'required|string',
            'district_id' => 'required|string',
            'district_name' => 'required|string',
            'subdistrict_id' => 'nullable|string',
            'subdistrict_name' => 'nullable|string',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'required|boolean'
        ]);

        $userId = $request->user()->id;

        $addresses = $this->addressService
            ->updateAddresses(
                $userId,
                $addressId,
                $validatedData
            );

        return response()->json([
            'success' => true,
            'message' => 'Addresses updated successfully',
            'data' => $addresses
        ], 200);
    }

    public function setDefault(Request $request, int $addressId) {
        $userId = $request->user()->id;

        $address = $this->addressService
            ->setDefaultAddress(
                $userId,
                $addressId
        );

        return response()->json([
            'success' => true,
            'message' => 'Address set as default successfully',
            'data' => $address
        ], 200);
    }

    public function destroy(Request $request, int $addressId) {
        $userId = $request->user()->id;

        $this->addressService
            ->deleteAddress(
                $userId,
                $addressId
        );

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ], 200);
    }
}
