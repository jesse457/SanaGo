<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;
use Throwable;

/**
 * The technical half of the `Error` page.
 *
 * Production shows the branded copy only — a stack trace on screen tells an
 * attacker more than it tells the user. Where `app.debug` is on, the page also
 * carries what actually broke: the exception class, its message, the file and
 * line it came from and the frames leading there. That keeps a local failure
 * readable in the browser instead of sending everyone to
 * `storage/logs/laravel.log`.
 */
final class ErrorReport
{
    /** A long SQL statement must not push the trace off the screen. */
    private const MESSAGE_LIMIT = 1200;

    /** Enough frames to walk the request back to the offending call. */
    private const FRAME_LIMIT = 12;

    /**
     * @return array{
     *     exception: string,
     *     message: string,
     *     location: string|null,
     *     previous: string|null,
     *     request: string|null,
     *     trace: array<int, array{call: string, location: string|null}>
     * }
     */
    public static function collect(Throwable $error, ?Request $request = null): array
    {
        return [
            'exception' => $error::class,
            'message' => self::summary($error->getMessage()),
            'location' => $error->getFile() === ''
                ? null
                : self::location($error->getFile(), $error->getLine()),
            // A QueryException buries "no such column: paid_amount" in its
            // previous PDOException, so the chain is not noise here.
            'previous' => $error->getPrevious() === null
                ? null
                : $error->getPrevious()::class.': '.self::summary($error->getPrevious()->getMessage()),
            'request' => $request === null
                ? null
                : self::summary($request->method().' '.$request->fullUrl(), 300),
            'trace' => self::trace($error),
        ];
    }

    /**
     * @return array<int, array{call: string, location: string|null}>
     */
    private static function trace(Throwable $error): array
    {
        return array_map(
            fn (array $frame): array => [
                'call' => self::callSignature($frame),
                'location' => isset($frame['file'])
                    ? self::location($frame['file'], (int) ($frame['line'] ?? 0))
                    : null,
            ],
            array_slice($error->getTrace(), 0, self::FRAME_LIMIT),
        );
    }

    /**
     * @param  array<string, mixed>  $frame
     */
    private static function callSignature(array $frame): string
    {
        $function = (string) ($frame['function'] ?? 'unknown');

        if (! isset($frame['class'])) {
            return $function;
        }

        return $frame['class'].((string) ($frame['type'] ?? '::')).$function;
    }

    /**
     * Repository-relative paths keep the line readable and never leak where the
     * app happens to be checked out on this machine.
     */
    private static function location(string $file, int $line): string
    {
        if (str_starts_with($file, base_path().DIRECTORY_SEPARATOR)) {
            $file = substr($file, strlen(base_path()) + 1);
        }

        return $file.':'.$line;
    }

    private static function summary(string $text, int $limit = self::MESSAGE_LIMIT): string
    {
        return mb_strlen($text) <= $limit
            ? $text
            : mb_substr($text, 0, $limit).'…';
    }
}
