<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\StoreBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    /**
     * Create a new business.
     */
    
    public function store(StoreBusinessRequest $request)
    {
        $business = DB::transaction(function () use ($request) {

            $user = $request->user();

            // Create Business
            $business = Business::create([
                ...$request->validated(),

                'slug' => Str::slug($request->name)
                    . '-' . Str::lower(Str::random(6)),

                'currency' => $request->currency ?? 'BDT',
            ]);

            // Assign authenticated user as business owner
            $business->users()->attach(
                $user->id,
                [
                    'role' => 'owner',
                    'joined_at' => now(),
                ]
            );

            // Set first business as active business
            if (! $user->active_business_id) {
                $user->active_business_id = $business->id;
                $user->save();
            }

            return $business;
        });

        return response()->json([
            'success' => true,
            'message' => 'Business created successfully.',
            'data' => [
                'business' => $business,
                'role' => 'owner',
            ],
        ], 201);
    }



    /**
     * Get all businesses of authenticated user.
     */
    public function index(Request $request)
    {
        $businesses = $request->user()
            ->businesses()
            ->select([
                'businesses.id',
                'businesses.name',
                'businesses.slug',
                'businesses.business_type',
                'businesses.email',
                'businesses.phone',
                'businesses.currency',
                'businesses.is_active',
            ])
            ->latest('businesses.created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Businesses retrieved successfully.',
            'data' => $businesses,
        ]);
    }


    /**
     * Get business details.
     */
    public function show(Request $request, int $id)
    {
        $business = $request->user()
            ->businesses()
            ->where('businesses.id', $id)
            ->first();

        if (! $business) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Business details retrieved successfully.',
            'data' => [
                'business' => $business,
                'role' => $business->pivot->role,
            ],
        ]);
    }


    /**
     * Update business information.
     */
    public function update(UpdateBusinessRequest $request, int $id)
    {
        $business = $request->user()
            ->businesses()
            ->where('businesses.id', $id)
            ->wherePivot('role', 'owner')
            ->first();

        if (! $business) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found or unauthorized.',
            ], 404);
        }

        $business->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Business updated successfully.',
            'data' => [
                'business' => $business->fresh(),
            ],
        ]);
    }


    public function switch(Request $request, int $id)
    {
        $user = $request->user();

        // Verify that the user belongs to this business
        $business = $user->businesses()
            ->where('businesses.id', $id)
            ->first();

        if (! $business) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this business.',
            ], 403);
        }

        // Set active business
        $user->active_business_id = $business->id;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Active business switched successfully.',
            'data' => [
                'active_business' => $business,
            ],
        ]);
    }


    public function active(Request $request)
    {
        $business = $request->attributes->get('activeBusiness');

        return response()->json([
            'success' => true,
            'message' => 'Active business retrieved successfully.',
            'data' => [
                'active_business' => $business,
            ],
        ]);
    }





}