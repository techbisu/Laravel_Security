<?php namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class PreventReplayAttack
{

	/**
	 * Variable that holds the cache driver.
	 * @var Cache
	 */
	private $cache;

	/**
	 * Seconds between each request.
	 * @var secondsBetweenRequest
	 */
	private $secondsBetweenRequest = 5;

	/**
	 * Cache key prefix.
	 * @var prefix
	 */
	private $prefix = "caller_";

	/**
	 * Input field that holds the user identifier.
	 * @var inputIdentifier
	 */
	private $inputIdentifier = 'session_token';

	/**
	 * Specify wherever to use Session::token() or inputIdentifier.
	 * @var useSessionToken
	 */
	private $useSessionToken = true;

	/**
	 * The constructor.
	 * @param Cache $cache
	 */
	public function __construct( Cache $cache )
	{
		$this->cache = $cache;
	}


	/**
	 * Handle an incoming request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle( $request, Closure $next )
	{
		if ( $this->isRequestToFast($request) )
			return Response::json(["error" => "You are performing an replay attack."]);

		return $next($request);
	}

	/**
	 * Check if the request was to fast.
	 * @param $request
	 * @return bool
	 * @throws \Exception
	 */
	private function isRequestToFast( $request )
	{
		if ( !$this->useSessionToken && !$request->input($this->inputIdentifier) )
			throw new \Exception("The input identifier cannot be empty.");

		$key = $this->getCacheKey($request);

		if ( $this->cache->has($key) ) return true;

		$this->cache->put($key, Carbon::now(), ($this->getExpiration()));

		return false;
	}

	/**
	 * Get the expiration seconds.
	 * @return float
	 */
	private function getExpiration()
	{
		return ($this->secondsBetweenRequest / 60);
	}

	/**
	 * Generate the cache key.
	 * @param $request
	 * @return string
	 */
	private function getCacheKey( $request )
	{
		$suffix = ($this->useSessionToken) ? Session::token() : $request->input($this->inputIdentifier);
		return $this->prefix . $suffix;
	}

}