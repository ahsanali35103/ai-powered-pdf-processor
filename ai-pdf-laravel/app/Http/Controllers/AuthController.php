<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\VerifySignupRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\OrganizationResource;
use App\Models\SessionToken;
use App\Models\User;
use App\Models\Organization;
use App\Notifications\ForgetPasswordNotification;
use App\Notifications\SignupVerificationNotification;

class AuthController extends Controller
{
    /**
     * POST /api/auth/signup
     * Register a new user and send email verification.
     */
    public function signup(SignupRequest $request)
    {
        $data = $request->validated();

        // Create organization first with just name
        $organizationData = ['name' => $data['organization']];
        $organization = Organization::createOrganization($organizationData, 'temp_user_id');

        // Create user with organization relationship
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'organization_id' => $organization->id,
        ];

        $user = User::add($userData);

        // Update organization created_by field with actual user ID
        $organization->update(['created_by' => $user->id]);

        // Generate verification token
        $token = SessionToken::generate('signup_verification_token', $user);

        $user->notify(new SignupVerificationNotification($user, $token));

        return response()->success([
            'user' => UserResource::make($user),
            'organization' => OrganizationResource::make($organization),
        ], 'Signup successful! Please check your email for a verification link.');
    }

    /**
     * POST /api/auth/verify-signup
     * Verify the user's email using the token sent via email.
     */
    public function verifySignup(VerifySignupRequest $request)
    {
        $user        = data_get($request, 'verified_user');
        $tokenRecord = data_get($request, 'token_record');

        // Activate user
        $user->update(['is_active' => true]);

        // Delete verification token
        $tokenRecord->delete();

        return response()->success([
            'user' => UserResource::make($user),
        ], 'Account activated successfully! You can now login.');
    }

    /**
     * POST /api/auth/login
     * Authenticate user and return access token.
     */
    public function login(LoginRequest $request)
    {
        $user = data_get($request, 'user');

        $token = SessionToken::generate('access_token', $user);

        return response()->success([
            'access_token' => $token,
            'user'         => UserResource::make($user),
        ], 'Login successful!');
    }

    /**
     * POST /api/auth/logout
     * Revoke the user's access token.
     */
    public function logout(LogoutRequest $request)
    {
        $tokenRecord = data_get($request, 'token_record');

        if ($tokenRecord) {
            $tokenRecord->delete();
        }

        return response()->success(null, 'Logout successful!');
    }

    /**
     * POST /api/auth/forgot-password
     * Send a password reset link to the user's email.
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = data_get($request, 'user');

        $token = SessionToken::generate('forgot_password_token', $user);

        $user->notify(new ForgetPasswordNotification($user, $token));
        
        return response()->success(null, 'Password reset link sent to your email.');
    }

    /**
     * POST /api/auth/reset-password
     * Reset the user's password using the reset token.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $tokenRecord = data_get($request, 'token_record');
        $user        = data_get($request, 'user');

        $user->update([
            'password' => data_get($request, 'password'),
        ]);

        $tokenRecord->delete();

        return response()->success(null, 'Password reset successfully!');
    }
}
