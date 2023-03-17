<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function updatePassword(request $request){ 
        $user = JWTAuth::user();
        $request->validate([
            'lastPassword'=>['required','min:8',function ($attribute, $value, $fail) {
                if (!Hash::check($value, $user->password)) {
                    $fail(__('The :last Password is incorrect.'));
                }
            }],
            'newPassword'=> 'required|min:8',
            'confirmPassword'=> 'required|same:newPassword'
        ]);
        $user->password= Hash::make($request->newPassword);
        $user->save();
        return  response()->json([
            'success'=>' Password edited successfully',
            'user'=> new UserResource($user)]);
    }
    public function updateName(request $request){
        $request->validate([
            'name'=>'required',
        ]);
        $user = JWTAuth::user();
        $user->name= $request->name;
        $user->save();
        return  response()->json([
            'success'=>' Name edited successfully',
            'user'=> new UserResource($user)]);
    }
    public function updateEmail(request $request){
        $request->validate([
            'email'=>'required',
        ]);
        $user = JWTAuth::user();
        $user->email= $request->email;
        $user->email_verified_at=NULL;
        $user->update();
        // user still can use the app but when it's logout and try to login he most verify email again 
        $user->sendConfirmationEmail();
        return  response()->json([
            'success'=>' Email edited successfully',
            'user'=> new UserResource($user)]);
    } 
    public function user(){
        $user = JWTAuth::user();
        return new UserResource($user);
    }
    public function users(){
        $users = User::whereDoesntHave('role', function($query) {
            $query->where('name', 'admin');
        })->get();
        return new UserCollection($users);
    }
    public function showOneUser($id){
        $user = User::find($id);
        return new UserResource($user);
    }
    public function updateUserRole(request $request){
        $request->validate([
            'user_id' =>'required|exists:users,id',
            'newRole'=>'required|exists:roles,name',
        ]);
        $user  = User::findOrFail($request->user_id);
        $user->assignRole('newRole');
        return  response()->json([
            'success'=>' Email edited successfully',
            'user'=> new UserResource($user)]);

    }

}
