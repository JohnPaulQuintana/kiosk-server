<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['token' => $token, 'message' => "Welcome to Campus Navigational Kiosk", "data" => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // Handle sending reset password link
    public function sendResetLink(Request $request)
    {
        // Validate the email
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'url' => 'required|url', // Ensure the URL is valid
        ]);

        $email = $request->input('email');
        $resetUrl = $request->input('url');

        // Check if the email exists in the database
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'We could not find a user with that email address.',
            ], 404);
        }

        // Generate the password reset link
        $resetToken = Password::getRepository()->create($user);
        $fullResetUrl = $resetUrl . "?token=" . $resetToken;

        // Send the password reset notification to the user
        try {
            $user->notify(new PasswordResetNotification($fullResetUrl));

            return response()->json([
                'message' => 'A password reset link has been sent to your email.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending reset link. Please try again.',
            ], 500);
        }
    }

    public function reset(Request $request)
    {
        // Validate request
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // Get all password reset entries and find the correct one using Hash::check
        $resetEntries = \DB::table('password_resets')->get();

        $resetData = $resetEntries->first(function ($entry) use ($request) {
            return Hash::check($request->token, $entry->token);
        });

        if (!$resetData) {
            return response()->json(['message' => 'Invalid or expired token'], 400);
        }

        // Find the user by email
        $user = User::where('email', $resetData->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used token
        \DB::table('password_resets')->where('email', $resetData->email)->delete();

        return response()->json(['message' => 'Password reset successful']);
    }
}
