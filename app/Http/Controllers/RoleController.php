<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Contracts\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(StoreRoleRequest $request)
    {
        $permissions = $request->input('permissions',[]);
        $role = Role::create(['name' => $request->name]);
        try{
            
            $role->givePermissionTo($permissions);
            return response()->json(['message',"role created successfully"], 201);
        }
        catch(\Exception $e){
            $role->delete();
            return response()->json(['message',$e->getMessage()], 401);
        }
       
        
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
    public function update(UpdateRoleRequest $request, $name)
    {
        $permissions = $request->input('permissions',[]);
        $role = $role = Role::findByName($name);
        $lastPermissions = $role->permissions;
        
        

        try{
            
            $role->name = $request->name;
            $role->save();
            $role->syncPermissions($permissions);
            return response()->json(['message',"role updated successfully"], 201);
        }
        catch(\Exception $e){
            $role->syncPermissions($lastPermissions);
            return response()->json(['message',$e->getMessage()], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($name)
    {
        $role = $role = Role::findByName($name);
        $role->delete();
        return response()->json(['message',"role deleted successfully"], 201);
    }
}
