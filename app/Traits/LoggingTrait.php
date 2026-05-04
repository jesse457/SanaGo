<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;

trait LoggingTrait
{
    /**
     * Log a critical operation with detailed context.
     *
     * @param string $message
     * @param array $extraContext
     * @return void
     */
    protected function logCritical($message, $extraContext = [])
    {
        Log::critical($message, $this->getLogContext($extraContext));
    }

    /**
     * Log an error with detailed context.
     *
     * @param string $message
     * @param array $extraContext
     * @return void
     */
    protected function logError($message, $extraContext = [])
    {
        Log::error($message, $this->getLogContext($extraContext));
    }

    /**
     * Log a warning with detailed context.
     *
     * @param string $message
     * @param array $extraContext
     * @return void
     */
    protected function logWarning($message, $extraContext = [])
    {
        Log::warning($message, $this->getLogContext($extraContext));
    }

    /**
     * Log an informational message with detailed context.
     *
     * @param string $message
     * @param array $extraContext
     * @return void
     */
    protected function logInfo($message, $extraContext = [])
    {
        Log::info($message, $this->getLogContext($extraContext));
    }

    /**
     * Log a debug message with detailed context.
     *
     * @param string $message
     * @param array $extraContext
     * @return void
     */
    protected function logDebug($message, $extraContext = [])
    {
        Log::debug($message, $this->getLogContext($extraContext));
    }

    /**
     * Log a validation failure with detailed context.
     *
     * @param ValidationException $exception
     * @param array $extraContext
     * @return void
     */
    protected function logValidationError(ValidationException $exception, $extraContext = [])
    {
        $context = array_merge([
            'errors' => $exception->errors(),
            'redirect_url' => $exception->redirectTo,
        ], $this->getLogContext($extraContext));

        Log::error('Validation failed', $context);
    }

    /**
     * Log an exception with detailed context and stack trace.
     *
     * @param \Exception $exception
     * @param array $extraContext
     * @return void
     */
    protected function logException(\Exception $exception, $extraContext = [])
    {
        $context = array_merge([
            'exception_class' => get_class($exception),
            'error_code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stack_trace' => $exception->getTraceAsString(),
        ], $this->getLogContext($extraContext));

        Log::error($exception->getMessage(), $context);
    }

    /**
     * Get the base log context with request metadata.
     *
     * @param array $extraContext
     * @return array
     */
    private function getLogContext($extraContext = [])
    {
        $context = [
            'timestamp' => now()->toISOString(),
            'request' => [
                'method' => Request::method(),
                'url' => Request::fullUrl(),
                'headers' => $this->getSafeHeaders(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ],
        ];

        // Add user information if authenticated
        if (Auth::check()) {
            $context['user'] = [
                'id' => Auth::id(),
                'email' => Auth::user()->email,
                'name' => Auth::user()->name ?? null,
                'tenant_id' => Auth::user()->tenant_id ?? null,
            ];
        }

        // Add component/controller information
        $context['source'] = [
            'file' => $this->getCallerFile(),
            'line' => $this->getCallerLine(),
            'class' => static::class,
        ];

        // Merge extra context
        return array_merge($context, $extraContext);
    }

    /**
     * Get safe headers (excluding sensitive information).
     *
     * @return array
     */
    private function getSafeHeaders()
    {
        $headers = [];
        $headerNames = Request::header();

        foreach ($headerNames as $name => $value) {
            $normalizedName = strtolower($name);
            $headers[$normalizedName] = in_array($normalizedName, ['authorization', 'cookie', 'x-csrf-token']) ? '***REDACTED***' : $value;
        }

        return $headers;
    }

    /**
     * Get the caller file from the stack trace.
     *
     * @return string
     */
    private function getCallerFile()
    {
        $trace = debug_backtrace();

        foreach ($trace as $frame) {
            if (isset($frame['file']) && strpos($frame['file'], __FILE__) === false) {
                return $frame['file'];
            }
        }

        return __FILE__;
    }

    /**
     * Get the caller line number from the stack trace.
     *
     * @return int
     */
    private function getCallerLine()
    {
        $trace = debug_backtrace();

        foreach ($trace as $frame) {
            if (isset($frame['file']) && strpos($frame['file'], __FILE__) === false) {
                return $frame['line'];
            }
        }

        return __LINE__;
    }
}
