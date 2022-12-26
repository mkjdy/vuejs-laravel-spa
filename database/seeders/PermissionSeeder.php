<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\{User};
use Spatie\Permission\Models\{Role, Permission};

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'admin',
            'user',
        ];

        $permissions = [
            //dashboard
            'view-dashboard',

            //reports
            'generate-reports',

            //user management
            'view-user',
            'add-user',
            'edit-user',
            'delete-user',

            //role & permission
            'view-role',
            'add-role',
            'edit-role',
            'delete-role',

            //setting
            'view-setting',
        ];

        foreach($permissions as $permit) Permission::create(['name' => $permit, 'guard_name' => 'sanctum']);

        foreach($roles as $role_key=>$role) {
            $role = Role::create(['name' => $role, 'description' => ($role == 'admin' ? 'All access' : 'Limited Access'), 'guard_name' => 'sanctum']);
            if ($role_key == 0) {
                foreach($permissions as $key=>$value) {
                    $permission = Permission::find($key+1);
                    $role->givePermissionTo($permission->name);
                    $permission->assignRole($role->name);
                }
            } else if($role_key == 1) {
                foreach($permissions as $key=>$value) {
                    if (!in_array(explode('-', $value)[1], ['reports', 'role', 'user'])) {
                        $permission = Permission::find($key+1);
                        $role->givePermissionTo($permission->name);
                        $permission->assignRole($role->name);
                    }
                }
            }
        }

        foreach(User::get() as $key=>$value) {
            switch ($key+1) {
                case 1: User::find(1)->assignRole('admin'); break;
                case 2: User::find(2)->assignRole('user'); break;
            }
        }
    }

}
