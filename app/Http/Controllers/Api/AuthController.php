<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function update_password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password', // Mevcut şifre kontrolü
            'new_password' => 'required|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => bcrypt($request->new_password)
        ]);

        return response()->json(['message' => 'Şifre güncellendi.']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Hatalı giriş'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    public function logout(Request $request)
    {
        // Mevcut isteği atan kullanıcının kullandığı token'ı veritabanından siler
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Başarıyla çıkış yapıldı ve token iptal edildi.'], 200);
    }

    public function logout_all(Request $request)
    {
        // Kullanıcıya ait tüm tokenları (tüm cihazlardaki oturumları) siler
        $request->user()->tokens()->delete();
        return response()->json(['code' => 200, 'message' => 'Tüm cihazlardan çıkış yapıldı.'], 200);
    }
}
