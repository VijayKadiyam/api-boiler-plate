<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Permission;

class PermissionUserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function store(Request $request)
  {
    $request->validate([
      'permission_id'  =>  'required',
      'user_id'        =>  'required'
    ]);

    $user = User::find($request->user_id);
    if($request->op == 'assign')
      $user->assignPermission($request->permission_id);
    if($request->op == 'unassign')
      $user->unassignPermission($request->permission_id);
    $permissionUser = User::with('permissions')->find($request->user_id);

    return response()->json([
    'data'    =>  $permissionUser
    ], 201);
  }
}
