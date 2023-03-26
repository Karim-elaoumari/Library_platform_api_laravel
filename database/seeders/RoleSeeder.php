<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        Permission::create(['name' => 'add_book']);
        
        Permission::create(['name' => 'show_own_books']);
        Permission::create(['name' => 'edit_own_book']);
        Permission::create(['name' => 'delete_own_book']);

        Permission::create(['name' => 'edit_all_books']);
        Permission::create(['name' => 'delete_all_books']);

        Permission::create(['name' => 'add_category']);
        Permission::create(['name' => 'edit_category']);
        Permission::create(['name' => 'delete_category']);

        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'add_role']);
        Permission::create(['name' => 'edit_role']);
        Permission::create(['name' => 'delete_role']);
        Permission::create(['name' => 'edit_role_of_user']);



        Role::create(['name' => 'user']);
           

        Role::create(['name' => 'publisher'])
            ->givePermissionTo(['show_categories','add_book' ,'show_books', 'edit_own_book','show_own_books','delete_own_book']);

        Role::create(['name' => 'admin'])
            ->givePermissionTo(['show_books','add_category','show_categories','edit_all_books','delete_all_books' ,'edit_category','show_categories','delete_category','add_role','edit_role','delete_role','edit_role_of_user']);
    }
}
