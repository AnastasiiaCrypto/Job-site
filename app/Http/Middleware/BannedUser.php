<?php
/**
 * JobClass - Geolocalized Job Board Script
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Middleware;

use Closure;
use Prologue\Alerts\Facades\Alert;

class BannedUser
{
	/**
	 * @param \Illuminate\Http\Request $request
	 * @param Closure $next
	 * @param null $guard
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function handle($request, Closure $next, $guard = null)
	{
		if (auth()->check()) {
			// Block access if user is banned
			if (auth()->guard($guard)->user()->blocked) {
				if ($request->ajax() || $request->wantsJson()) {
					return response(trans('admin::messages.unauthorized'), 401);
				} else {
					auth()->logout();
					
					$message = "This user has been banned.";
					
					if ($request->path() == admin_uri('login')) {
						Alert::error($message)->flash();
						return redirect()->guest(admin_uri('login'));
					} else {
						flash()->error($message);
						return redirect()->guest(trans('routes.login'));
					}
				}
			}
		}
		
		return $next($request);
	}
}
