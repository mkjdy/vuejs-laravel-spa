<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // public function  register(Request $request) {
    //     DB::beginTransaction();
    //     try {
    //         $fields = $request->validate([
    //             'first_name' => 'required|string|max:55',
    //             'middle_name' => 'nullable',
    //             'last_name' => 'required|string|max:55',
    //             'username'  => 'required|string|unique:users',
    //             'password' => 'required|string|confirmed|min:5|regex:/^[A-Za-z0-9_]+$/',
    //             // 'is_active' => 'required|boolean',
    //             'avatar' => 'nullable',
    //         ]);

    //         $user =  User::create([
    //             'first_name' => $fields['first_name'],
    //             'middle_name' => $fields['middle_name'],
    //             'last_name' => $fields['last_name'],
    //             'username' =>  $fields['username'],
    //             'password' => bcrypt($fields['password']),
    //             'is_active' => 1,
    //             'avatar' => $request->hasFile('image') ? $request->avatar : NULL,
    //         ]);

    //         //$token = $user->createToken('myapptoken')->plainTextToken;

    //         if (!!json_decode($request->role)) {
    //             $user->assignRole(json_decode($request->role));
    //         }

    //         // $response = [
    //         //     'user' => $user,
    //         //     // 'token' => $token
    //         // ];

    //         // return response($response, 201);

    //         if ($request->hasFile('image')) {
    //             // $avatar_path = $request->avatar;
    //             $request->image->storeAs('images', $request->file_name, 'public');
    //         } else {
    //             // $avatar_path = NULL;
    //         }

    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return response()->json(
    //             [
    //                 'message' => 'Failed to add "'.$request->username.'"',
    //                 'type' => 'error',
    //             ],
    //             500
    //         );
    //     }
    //     DB::commit();
    //     return response()->json(
    //         [
    //             'message' => '"'.$request->username.'" has been successfully added',
    //             'type' => 'success',
    //         ],
    //         200
    //     );
    // }

    public function  login(Request $request) {
        $fields = $request->validate([
            'username'  => 'required|string',
            'password' => 'required|string'
        ]);

        //manual login user
        Auth::attempt($fields);

        $user = User::with(['roles' => fn($q) => $q->with('permissions')])->where('username', $fields['username'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        if ($user->is_active == 0) {
            return response([
                'message' => 'Account locked.'
            ], 409);
        }

        if ($user->roles->count() < 1) {
            return response([
                'message' => 'This account does not have any roles; please contact the administrator.'
            ], 409);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        // $token = $user->createToken('myapptoken', ['user:add', 'user:edit'])->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        Auth::guard('web')->logout();

        return [
            'message' => 'Logged out'
        ];
    }
}
