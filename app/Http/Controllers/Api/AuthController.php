<?php

namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth; //

class AuthController extends Controller
{

   public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]); //защищаем, кроме login и register
    }
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
       $validator = Validator::make($request->all(), [
          'name' => 'required|string|max:255',
          'email' => 'required|string|email|max:255|unique:users',
          'password' => 'required|string|min:6|confirmed',
       ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
             'is_admin' => false,
        ]);

        $token = JWTAuth::fromUser($user); //Создаем токен

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Get a JWT via given credentials.
     *
     */
    public function login(Request $request): JsonResponse
    {
       $validator = Validator::make($request->all(), [
          'email' => 'required|email',
          'password' => 'required|string|min:6',
       ]);

        if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
        }
        $credentials = $request->only('email', 'password');

        if (! $token = Auth::attempt($credentials)) { //Пытаемся залогинить и получить токен
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([  //Если все ок, отдаем токен
          'access_token' => $token,
          'token_type' => 'bearer',
          'expires_in' => Auth::factory()->getTTL() * 6000000000, //время жизни токена
          'user' => Auth::user() //Пользователь
        ]);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}