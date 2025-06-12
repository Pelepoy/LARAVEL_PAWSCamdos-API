<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

/**
 * Authentication Controller
 *
 * Handles user authentication operations including registration, login, logout,
 * and password management.
 */
class AuthController extends Controller
{
    protected $otp;

    /**
     * Create a new controller instance
     * @return void
     */
    public function __construct()
    {
        $this->otp = new Otp();
    }

    /**
     * Register a new user
     *
     * @param  RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $token = $user->createToken($request->first_name)->plainTextToken;
        $otp = $this->otp->generate($user->email, 'numeric', 6, 5);

        // Send OTP to user's email
        $user->notify(new EmailVerificationNotification($otp->token));

        return response()->json(
            [
                'status'   => 'success',
                'message'  => 'Registration Successful. OTP sent to your email.',
                'redirect' => env('APP_ENV') . '/api/email-verification',
                'token'    => $token,
            ],
            201
        );
    }

    /**
     * Login a user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(
                ['error' => 'Invalid credentials'],
                401
            );
        }

        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json(
            [
                'status'  => 'success',
                'message' => 'Login successful',
                'token'   => $token,
            ]
        );
    }

    /**
     * Logout the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(
            [
                'status'  => 'success',
                'message' => 'Logout successful',
            ]
        );
    }

    /**
     * Send password reset link
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'], 200)
            : response()->json(['message' => 'Unable to send reset link.'], 400);
    }

    /**
     * Reset user password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(
                    [
                        'password' => Hash::make($password),
                    ]
                )->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully.'], 200)
            : response()->json(['message' => 'Invalid token or email.'], 400);
    }
}
