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
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class CheckForMaintenanceMode
{
	/**
	 * The application implementation.
	 *
	 * @var \Illuminate\Contracts\Foundation\Application
	 */
	protected $app;
	
	protected $exceptRoutes = [];
	
	/**
	 * Create a new middleware instance.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application $app
	 * @return void
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
		
		$this->exceptRoutes = [
			admin_uri() . '/*',
			admin_uri(),
			'upgrade',
		];
	}
	
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 *
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 */
	public function handle($request, Closure $next)
	{
		if ($this->app->isDownForMaintenance() && !$this->shouldPassThrough($request)) {
			$filePath = $this->app->storagePath() . '/framework/down';
			if (file_exists($filePath)) {
				$data = json_decode(file_get_contents($filePath), true);
				
				throw new MaintenanceModeException($data['time'], $data['retry'], $data['message']);
			}
		}
		
		return $next($request);
	}
	
	/**
	 * @param $request
	 * @return bool
	 */
	protected function shouldPassThrough($request)
	{
		$canPass = false;
		
		if ($this->shouldPassThroughRoutes($request) || $this->shouldPassThroughIp($request)) {
			$canPass = true;
		}
		
		return $canPass;
	}
	
	/**
	 * @param $request
	 * @return bool
	 */
	protected function shouldPassThroughRoutes($request)
	{
		foreach ($this->exceptRoutes as $exceptRoute) {
			if ($exceptRoute !== '/') {
				$exceptRoute = trim($exceptRoute, '/');
			}
			if ($request->is($exceptRoute)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @param $request
	 * @return bool
	 */
	protected function shouldPassThroughIp($request)
	{
		$exceptOwnIp = config('larapen.core.exceptOwnIp');
		if (is_array($exceptOwnIp)) {
			if (in_array($request->ip(), $exceptOwnIp)) {
				return true;
			}
		}
		
		return false;
	}
}
