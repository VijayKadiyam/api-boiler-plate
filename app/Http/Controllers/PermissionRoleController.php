<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Permission;

class PermissionRoleController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * Assign permission to role
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'permission_id'  =>  'required',
      'role_id'        =>  'required'
    ]);

    $role =  Role::find($request->role_id);
    if($request->op == 'assign')
      $role->assignPermission($request->permission_id);
    if($request->op == 'unassign')
      $role->unassignPermission($request->permission_id);
    $permissionRole = Role::with('permissions')->find($request->role_id);

    return response()->json([
    'data'    =>  $permissionRole
    ], 201);
  }
}
