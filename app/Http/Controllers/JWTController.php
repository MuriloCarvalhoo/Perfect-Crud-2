<?php

namespace App\Http\Controllers;

use App\Http\Requests\JWTLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\FormatCpf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class JWTController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'cpf' => 'required|string|min:11|max:11|unique:users', new FormatCpf,
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validated = $validator->validate();

        if($validated == true){
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'cpf' => $validated['cpf'],
                'password' => Hash::make($validated['password'])
            ]);

            return response()->json([
                'message' => 'User successfully registered',
                'user' => new UserResource($user)
            ], 201);
        }

        return response()->json([
            'message' => 'User register failed',
        ], 400);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cpf' => 'required', new FormatCpf,
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully logged out.']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function profile()
    {
        if(in_array(1, auth()->user()->roles->pluck('id')->toArray())){
            return User::select('id', 'name', 'email', 'cpf', 'ativo')
            ->with('roles:id,key,label', 'roles.permissions:id,key,label', 'posts:id,title,body')
            ->find(auth()->user()->id);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}