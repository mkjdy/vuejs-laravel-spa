<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        // return User::get();

        $users = User::query();

        $users->with(['roles' => function($q){ $q->with('permissions'); }]);

        if (request()->has('search')) {
            $users->whereColumnContains(request('search'));
        }

        if (request()->has('order_by')) {
            [$column, $sort] = explode(",", request('order_by'));
            $users->orderBy($column, $sort);
        } else {
            $users->orderBy('created_at', 'DESC');
        }

        return request()->has('page') ?
            $users->paginate(intval(request('rows')))->appends(request()->all()) :
            $users->get();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $fields = $request->validate([
                'first_name' => 'required|string|max:55',
                'middle_name' => 'nullable',
                'last_name' => 'required|string|max:55',
                'username'  => 'required|string|unique:users',
                'password' => 'required|string|confirmed|min:5|regex:/^[A-Za-z0-9_]+$/',
                // 'is_active' => 'required|boolean',
                'avatar' => 'nullable',
            ]);

            $user =  User::create([
                'first_name' => $fields['first_name'],
                'middle_name' => $fields['middle_name'],
                'last_name' => $fields['last_name'],
                'username' =>  $fields['username'],
                'password' => bcrypt($fields['password']),
                'is_active' => 1,
                'avatar' => $request->hasFile('image') ? $request->avatar : NULL,
            ]);

            //$token = $user->createToken('myapptoken')->plainTextToken;

            if (!!json_decode($request->role)) {
                $user->assignRole(json_decode($request->role));
            }

            // $response = [
            //     'user' => $user,
            //     // 'token' => $token
            // ];

            // return response($response, 201);

            if ($request->hasFile('image')) {
                // $avatar_path = $request->avatar;
                $request->image->storeAs('images', $request->file_name, 'public');
            } else {
                // $avatar_path = NULL;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to add "'.$request->username.'"',
                    'type' => 'error',
                ],
                500
            );
        }
        DB::commit();
        return response()->json(
            [
                'message' => '"'.$request->username.'" has been successfully added',
                'type' => 'success',
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        DB::beginTransaction();
        try {
            $old_image_path = (clone $user)->avatar;

            if (json_decode($request->change_username_password)) {
                $fields = $request->validate([
                    'first_name' => 'required|string|max:55',
                    'middle_name' => 'nullable',
                    'last_name' => 'required|string|max:55',
                    'username'  => 'required|string|unique:users',
                    'password' => 'required|string|confirmed|min:5|regex:/^[A-Za-z0-9_]+$/',
                    'avatar' => 'nullable',
                ]);

                $user->update([
                    'first_name' => $fields['first_name'],
                    'middle_name' => $fields['middle_name'],
                    'last_name' => $fields['last_name'],
                    'username' =>  $fields['username'],
                    'password' => bcrypt($fields['password']),
                    'avatar' => $request->hasFile('image') ? $request->avatar : $user->avatar,
                ]);
            } else {
                $fields = $request->validate([
                    'first_name' => 'required|string|max:55',
                    'middle_name' => 'nullable',
                    'last_name' => 'required|string|max:55',
                    'avatar' => 'nullable',
                ]);

                $user->update([
                    'first_name' => $fields['first_name'],
                    'middle_name' => $fields['middle_name'],
                    'last_name' => $fields['last_name'],
                    'avatar' => $request->hasFile('image') ? $request->avatar : $user->avatar,
                ]);
            }

            if ($request->hasFile('image')) {
                try {
                    if (explode("/", $old_image_path)[0] != "images") {
                        unlink($old_image_path);
                    }
                } catch (\Throwable $th) {}
                $request->image->storeAs('images', $request->file_name, 'public');
            }

            if (!!json_decode($request->role)) {
                $user->syncRoles(json_decode($request->role));
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to update',
                    'type' => 'error',
                ],
                500
            );
        }
        DB::commit();
        return response()->json(
            [
                'message' => 'Record has been successfully updated',
                'type' => 'success',
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            try {
                if (explode("/", $user->avatar)[0] != "images") {
                    unlink($user->avatar);
                }
            } catch (\Throwable $th) {}
            $roles = array_map(function ($role) { return $role->name; }, $user->roles->all());
            foreach ($roles as $role) $user->removeRole($role);
            $user->delete();

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to removed "'.$user->username.'"',
                    'type' => 'error',
                ],
                500
            );
        }
        DB::commit();
        return response()->json(
            [
                'message' => '"'.$user->username.'" has been successfully removed',
                'type' => 'success',
            ],
            200
        );
    }
}
