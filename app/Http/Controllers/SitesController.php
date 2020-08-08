<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Site;

class SitesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api')
      ->except('index');
  }

  /*
   * To get all sites
     *
   *@
   */
  public function index()
  {
    $sites = Site::with('users')->get(); 

    return response()->json([
      'data'     =>  $sites
    ], 200);
  }

  /*
   * To store a new site
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required',
      'email'   =>  'required',
      'phone'   =>  'required',
      'address' =>  'required',
    ]);

    $site = new Site(request()->all());
    $site->save();

    return response()->json([
      'data'    =>  $site
    ], 201); 
  }

  /*
   * To view a single site
   *
   *@
   */
  public function show(Site $site)
  {
    return response()->json([
      'data'   =>  $site,
      'success' =>  true
    ], 200);   
  }

  /*
   * To update a site
   *
   *@
   */
  public function update(Request $request, Site $site)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $site->update($request->all());
      
    return response()->json([
      'data'  =>  $site
    ], 200);
  }
}
