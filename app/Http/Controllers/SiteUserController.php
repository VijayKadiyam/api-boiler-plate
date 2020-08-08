<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Site;

class SiteUserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api')
      ->except('store');
  }

  /*
   * Assign site to user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'user_id'    =>  'required',
      'site_id' =>  'required'
    ]);

    $user = User::find($request->user_id);
    $site = Site::find($request->site_id);
    $user->assignSite($site->id);
    $siteUser = User::with('sites')->find($request->user_id);

    return response()->json([
        'data'  =>  $siteUser,
        'success' =>  true
    ], 201); 
  }
}
