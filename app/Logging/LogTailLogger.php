<?php

namespace App\Logging;

use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;

class LogTailLogger
{
    public function __invoke()
    {
        $logger = new Logger('logtail-source');
        $logger->pushHandler(new LogtailHandler("yxQvJBGuq1zSv4nZs2uteH34"));
        return $logger;
    }
}
