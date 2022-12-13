<?php

namespace Database\Seeders;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //clean directory in public/storage/images
        $file = new Filesystem;
        $directory = public_path('storage/' . 'images');
        $file->cleanDirectory($directory);

        \App\Models\User::factory()->create([
            'first_name' => 'Admin',
            'middle_name' => NULL,
            'last_name' => 'Admin',
            'username' => 'admin',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
        ]);
        \App\Models\User::factory()->create([
            'first_name' => 'User',
            'middle_name' => NULL,
            'last_name' => 'User',
            'username' => 'user',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
        ]);
        \App\Models\User::factory(10)->create();

        $this->call([
            PermissionSeeder::class,
        ]);


        // \App\Models\User::factory()->create([
        //     'email' => 'johndoe@gmail.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
        // ]);
    }
}
