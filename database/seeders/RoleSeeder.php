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
        Permission::create(['name' => 'edit_book']);
        Permission::create(['name' => 'delete_book']);


        
        Permission::create(['name' => 'show_own_books']);
        Permission::create(['name' => 'edit_own_book']);
        Permission::create(['name' => 'delete_own_book']);
        Permission::create(['name' => 'filter_own_by_category']);

        Permission::create(['name' => 'add_category']);
        Permission::create(['name' => 'edit_category']);
        Permission::create(['name' => 'delete_category']);

        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'add_role']);
        Permission::create(['name' => 'edit_role']);
        Permission::create(['name' => 'delete_role']);
        Permission::create(['name' => 'edit_role_of_user']);



        Role::create(['name' => 'user'])
            ->givePermissionTo(['show_books','show_categories' ,'filter_by_category']);

        Role::create(['name' => 'publisher'])
            ->givePermissionTo(['show_categories','add_book', 'edit_own_book','show_own_books','delete_own_book' ,'filter_own_by_category']);

        Role::create(['name' => 'admin'])
            ->givePermissionTo(['show_books','show_categories' ,'filter_by_category','edit_category','delete_category','add_role','edit_role','delete_role','edit_role_of_user']);
    }
}
