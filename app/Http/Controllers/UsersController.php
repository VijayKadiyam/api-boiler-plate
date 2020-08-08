<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;

class UsersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'site']);
  }

  /*
   * To get all the users
   *
   *@
   */
  public function index(Request $request)
  {
    $role = 3;
    $users = [];
    if($request->search == 'all')
      $users = $request->site->users()->with('roles')
        ->whereHas('roles',  function($q) {
          $q->where('name', '!=', 'Admin');
        })->latest()->get();
    else if($request->role_id) {
      $role = Role::find($request->role_id);
      $users = $request->site->users()
        ->whereHas('roles', function($q) use($role) { 
          $q->where('name', '=', $role->name);
        })->latest()->get();
    }

    return response()->json([
          'data'  =>  $users
      ], 200);
  }

  /*
   * To store a new site user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'first_name'  =>  ['required', 'string', 'max:255'],
      'last_name'   =>  ['required', 'string', 'max:255'],
      'user_name'   =>  ['required'],
      'initials'    =>  ['required'],
      'email'       =>  ['required', 'string', 'email', 'max:255', 'unique:users'],
      'active'      =>  ['required']
    ]);

    $user  = $request->all();
    $user['password'] = bcrypt('123456');

    $user = new User($user);

    $user->save();

    return response()->json([
      'data'     =>  $user
    ], 201); 
  }

  /*
   * To show particular user
   *
   *@
   */
  public function show($id)
  {
    $user = User::where('id' , '=', $id)
      ->with('roles', 'sites')->first();

    return response()->json([
      'data'  =>  $user,
      'success' =>  true
    ], 200); 
  }

  /*
   * To update user details
   *
   *@
   */
  public function update(Request $request, User $user)
  {
    $user->update($request->all());
    $user->roles = $user->roles;
    $user->sites = $user->sites;
    
    return response()->json([
      'data'  =>  $user,
      'success' =>  true
    ], 200);
  }
}