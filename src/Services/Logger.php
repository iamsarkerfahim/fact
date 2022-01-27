<?php
declare(strict_types=1);

namespace App\Services;

class Logger
{
    public static function log(string $message, int $messageType = LOG_ERR): void
    {
        error_log($message . PHP_EOL . PHP_EOL, $messageType, 'error.log');
    }
}
