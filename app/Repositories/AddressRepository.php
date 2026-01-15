<?php

namespace App\Repositories;

use App\Enums\Roles;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\CartAccessToken;
use App\Http\Requests\AddressRequest;
use Abedin\Maker\Repositories\Repository;

class AddressRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Address::class;
    }

    /**
     * Store an address using the given request.
     *
     * @param  AddressRequest  $request  The address request
     */
    public static function storeByRequest(AddressRequest $request): Address
    {
        $isDefault = $request->is_default ? true : false;
        $customer = auth()->user()->customer;

        $addresses = $customer?->addresses;

        if ($isDefault && ($addresses->count() > 0)) {
            $customer->addresses()->update(['is_default' => false]);
        }

        return self::create([
            'customer_id' => auth()->user()->customer->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'area' => $request->area,
            'flat_no' => $request->flat_no,
            'post_code' => $request->post_code,
            'address_line' => $request->address_line,
            'address_line2' => $request->address_line2,
            'address_type' => $request->address_type,
            'is_default' => $customer->addresses ? $isDefault : true,
            'latitude' => $request->longitude,
            'longitude' => $request->longitude,
        ]);
    }

    /**
     * Update an address using the provided request data.
     *
     * @param  AddressRequest  $request  The request data for the address update
     * @return Address The updated address
     */
    public static function updateByRequest(AddressRequest $request, Address $address): Address
    {
        $isDefault = $request->is_default ? true : false;

        $customer = auth()->user()->customer;

        if ($isDefault) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $address->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'area' => $request->area,
            'flat_no' => $request->flat_no,
            'post_code' => $request->post_code,
            'address_line' => $request->address_line,
            'address_line2' => $request->address_line2,
            'address_type' => $request->address_type,
            'is_default' => $isDefault,
        ]);

        return $address;
    }


    public static function storeByGuestUser(Request $request): Address
    {
        $user = UserRepository::query()->where('phone', $request->phone)->orWhere('email', $request->email)->first();
        $tokens = cartAccessToken(request());
        if ($user) {
            if ($user->customer?->addresses()->count() > 0) {
                $user->customer?->addresses()->update(['is_default' => false]);
            }
            CartAccessToken::where('access_token', $tokens['access_token'])->update(['customer_id' => $user->customer->id]);
            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            return Address::updateOrCreate(
                [
                    'customer_id' => $user->customer->id,
                    'phone' => $request->phone,
                ],
                [
                    'name' => $request->name,
                    'area' => $request->area,
                    'flat_no' => $request->flat_no,
                    'post_code' => $request->post_code,
                    'address_line' => $request->address_line,
                    'address_line2' => $request->address_line2,
                    'address_type' => $request->address_type,
                    'is_default' => true,
                    'latitude' => $request->longitude,
                    'longitude' => $request->longitude,
                ]
            );
        }

        // Create a new user
        $user = UserRepository::registerNewUser($request);

        if ($request->device_key) {
            DeviceKeyRepository::storeByRequest($user, $request);
        }
        // Create a new customer
        CustomerRepository::storeByRequest($user);

        // create wallet
        WalletRepository::storeByRequest($user);

        $user->assignRole(Roles::CUSTOMER->value);

        CartAccessToken::where('access_token', $tokens['access_token'])->update(['customer_id' => $user->customer->id]);

        return self::create([
            'customer_id' => $user->customer->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'area' => $request->area,
            'flat_no' => $request->flat_no,
            'post_code' => $request->post_code,
            'address_line' => $request->address_line,
            'address_line2' => $request->address_line2,
            'address_type' => $request->address_type,
            'is_default' => true,
            'latitude' => $request->longitude,
            'longitude' => $request->longitude,
        ]);
    }
}
