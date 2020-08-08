<?php

namespace App\Http\Middleware;

use Closure;
use App\Site;

class SiteMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    $request['site'] = Site::where('id' , '=', request()->header('site-id'))->first();

    return $next($request);
  }
}
