<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class ThrottleRequests
{
    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Create a new request throttler.
     *
     * @param \Illuminate\Cache\RateLimiter $limiter
     *
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param int                      $maxAttempts
     * @param int                      $decayMinutes
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = null, $decayMinutes = null)
    {
        $maxAttempts = $maxAttempts ?? \config('app.throttle.max', 60);
        $decayMinutes = $decayMinutes ?? \config('app.throttle.minute', 1);

        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            $this->logAttempts();
            return $this->buildResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request);

//        return $this->addHeaders(
//            $response,
//            $maxAttempts,
//            $this->calculateRemainingAttempts($key, $maxAttempts)
//        );
    }

    /**
     * Resolve request signature.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        return 'throttle:' . sha1(
                '|' . $request->getHost() .
                '|' . $this->ip()
            );
    }


    /**
     * @return string
     */
    protected function ip()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }


    /**
     * Create a 'too many attempts' response.
     *
     * @param string $key
     * @param int    $maxAttempts
     *
     * @return \Illuminate\Http\Response
     */
    protected function buildResponse($key, $maxAttempts)
    {
        $response = new Response('Too Many Attempts.', 429);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts),
            $this->limiter->availableIn($key)
        );
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param \Illuminate\Http\Response $response
     * @param int                       $maxAttempts
     * @param int                       $remainingAttempts
     * @param int|null                  $retryAfter
     *
     * @return \Illuminate\Http\Response
     */
    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts, $retryAfter = null)
    {
        $headers = [
            'X-RateLimit-Limit'     => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (!is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
        }

        $response->headers->add($headers);

        return $response;
    }

    /**
     * Calculate the number of remaining attempts.
     *
     * @param string $key
     * @param int    $maxAttempts
     *
     * @return int
     */
    protected function calculateRemainingAttempts($key, $maxAttempts)
    {
        return $maxAttempts - $this->limiter->attempts($key) + 1;
    }

    /**
     * Log Banned Ip
     */
    protected function logAttempts()
    {
        if (method_exists(Cache::getStore(), 'setPrefix')) {
            Cache::getStore()->setPrefix('LOG');
        }

        Cache::put($key = 'banned:' . $this->ip(), (Cache::get($key) ?? 0) + 1, \config('app.throttle.log_minute') * 60);
    }
}
