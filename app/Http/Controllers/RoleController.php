<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::query();
        $roles->with(['permissions']);

        if (request()->has('search')) {
            $searchTerm = request('search');
            $attributes = [
                'name',
                'description',
                'permissions.name'
            ];

            $roles->where(function ($query) use ($attributes, $searchTerm) {
                foreach ($attributes as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function ($q) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            return $q->orWhereHas($relationName, function ($q) use ($relationAttribute, $searchTerm) {
                                $q->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function ($q) use ($attribute, $searchTerm) {
                            return $q->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
        }

        if (request()->has('with_role_user') && request('with_role_user') == "true") {
            return [
                'role_user' => User::with('roles')->whereHas('roles', function($q){
                        $q->where('name', '<>', '');
                    })->get(),
                'meta' => request()->has('page') ?
                    $roles->paginate(intval(request('rows')))->appends(request()->all()) :
                    $roles->get(),
            ];
        } else {
            return request()->has('page') ?
                $roles->paginate(intval(request('rows')))->appends(request()->all()) :
                $roles->get();
        }
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $role = new Role();
            $role = $role->create(['name' => $request->role, 'description' => $request->description]);
            foreach ($request->data as $permit) {
                $permission = Permission::findByName($permit);
                $role->givePermissionTo($permission->name);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to insert "'.$request->role.'"',
                    'type' => 'error',
                ],
                500
            );
        }
        DB::commit();
        return response()->json(
            [
                'message' => '"'.$request->role.'" has been successfully added',
                'type' => 'success',
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        DB::beginTransaction();
        try {
            if (!$role) {
                return abort('No role found', 422);
            }
            $toRemove = $role->permissions()->whereNotIn('name', $request->data)->get();
            if (count($toRemove)) {
                foreach($toRemove as $value) {
                    $role->revokePermissionTo($value->name);
                }
            }
            foreach ($request->data as $permit) {
                $checkIfExist = $role->permissions()->where('name', $permit)->get();
                if (!count($checkIfExist)) {
                    $role->givePermissionTo($permit);
                }
            }
            $role->update(['name' => $request->role, 'description' => $request->description]);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function destroyRole(Request $request, Role $role) {
        DB::beginTransaction();
        try {
            $users = User::with('roles')->whereHas('roles', function($q) use($request) { $q->where('name', $request->role); })->get();
            if (count($users)) {
                foreach($users as $user) {
                    $userFind = User::find($user->id);
                    $userFind->removeRole($request->role);
                }
            }
            foreach($request->data[0]['permissions'] as $permit) {
                $role->revokePermissionTo($permit['name']);
            }
            $role->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to removed "'.$request->role.'"',
                    'type' => 'error',
                ],
                500
            );
        }
        DB::commit();
        return response()->json(
            [
                'message' => '"'.$request->role.'" has been successfully removed',
                'type' => 'success',
            ],
            200
        );
    }

}
