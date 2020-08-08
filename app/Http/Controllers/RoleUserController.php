<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;

class RoleUserController extends Controller
{
  /*
   * Assign role to user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
        'user_id'        =>  'required',
        'role_id'        =>  'required'
    ]);

    $user =  User::find($request->user_id);
    $user->assignRole($request->role_id);
    $roleUser = User::with('roles')->find($request->user_id);

    return response()->json([
    'data'    =>  $roleUser,
    'success' =>  true
    ], 201); 
  }
}
