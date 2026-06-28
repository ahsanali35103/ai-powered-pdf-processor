<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FcmTokenController extends Controller
{
    /**
     * Store FCM Token for the authenticated user
     *
     * POST /api/fcm/token
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Retrieve user object from request (populated by CheckTokenMiddleware)
            $user = $request->input('user');

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found in request'
                ], 401);
            }

            // Update user's FCM token
            $user->fcm_token = $request->input('token');
            $user->save();


            return response()->json([
                'success' => true,
                'message' => 'FCM Token stored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store FCM Token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
