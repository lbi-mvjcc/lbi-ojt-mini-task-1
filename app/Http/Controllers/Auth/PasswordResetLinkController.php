<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle password reset with code
     */
    public function resetWithCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Find valid reset code
        $resetCode = PasswordResetCode::where('user_id', $user->id)
            ->where('code', strtoupper($request->code))
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetCode) {
            return back()->withErrors(['code' => 'Invalid or expired reset code.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Mark code as used
        $resetCode->used = true;
        $resetCode->save();

        return redirect()->route('login')->with('status', 'Password has been reset successfully! You can now login with your new password.');
    }
}
