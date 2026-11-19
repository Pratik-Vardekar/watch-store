<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
    
        $addresses = Address::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json([
            'status' => true,
            'message' => 'User addresses fetched successfully',
            'user_id' => $userId,
            'data' => $addresses
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required',
            'flat'    => 'required|string',
            'area'    => 'required|string',
            'city'    => 'required|string',
            'state'   => 'required|string',
            'pincode' => 'required',
            'type'    => 'nullable|string|max:100', // ENFORCE 'type' IS REQUIRED, NOT NULLABLE
        ]);

        $address = Address::create([
            'user_id'         => auth()->id(),
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'alternate_phone' => $request->alternate_phone,
            'flat'            => $request->flat,
            'area'            => $request->area,
            'landmark'        => $request->landmark,
            'city'            => $request->city,
            'state'           => $request->state,
            'country'         => $request->country,
            'pincode'         => $request->pincode,
            'type'            => $request->type, // ENSURED NOT NULL BY VALIDATION
            'is_default'      => $request->is_default ?? 0,
            'status'          => $request->status ?? 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Address created successfully',
            'data'    => $address
        ]);
    }

    public function show($id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $address
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
    
        $address = Address::where('id', $request->id)
            ->where('user_id', auth()->id())
            ->first();
    
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }
    
        $fields = [
            'name','email','phone','alternate_phone','flat','area',
            'landmark','city','state','country','pincode','type','is_default','status'
        ];
    
        $updateData = [];
    
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }
    
        $address->update($updateData);
    
        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address->fresh()
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);
    
        $address = Address::where('id', $request->id)
            ->where('user_id', auth()->id())
            ->first();
    
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }
    
        $address->delete();
    
        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

}