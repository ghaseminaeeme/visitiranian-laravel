<?php

declare(strict_types=1);

namespace App\Logging;

use App\Jobs\LogErrorToDatabase;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

final class DatabaseLogHandler extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        if ($record->level->value < $this->level->value) {
            return;
        }

        $context = $record->context;
        $throwable = $context['exception'] ?? null;

        if ($throwable instanceof \Throwable) {
            dispatch(LogErrorToDatabase::fromThrowable($throwable, $context));

            return;
        }

        dispatch(new LogErrorToDatabase(
            message: (string) $record->message,
            context: $context,
            level: strtolower($record->level->getName()),
        ));
    }
}
