<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['user', 'admin', 'publisher'];
        
        User::factory()->count(4)->create()->each(function ($user) use ($roles){
            $user->assignRole(Arr::random($roles));
        });
    }
}
