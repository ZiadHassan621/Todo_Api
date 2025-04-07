<?php

namespace App\Http\Controllers\Auth;

use Hash;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login' , 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'email'=> 'required|unique:users,email',
            'password'=> 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = collect($validator->errors()->toArray())
            ->map(function ($messages) {
                return $messages[0]; // just the first message
            });
            return response()->json($errors, 402);
        }
        $user = User::create([
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'name'=> $request->name
        ]);
        return response()->json(['data' => $user , 'status' => 'Created successfully'] , 201);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make(request()->all() ,[
            'email' => 'required|exists:users,email',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
         // dd($validator->errors()->toArray());
          $errors = collect($validator->errors()->toArray())
                ->map(function ($messages) {
                    return $messages[0]; // just the first message
                });
            return response()->json($errors, 402);
        }
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
           return response()->json(['message' => 'invalid credentials'], 401);
        }

       
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
