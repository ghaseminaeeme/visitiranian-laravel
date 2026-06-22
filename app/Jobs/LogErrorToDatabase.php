<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ErrorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

final class LogErrorToDatabase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        private readonly string $message,
        private readonly ?string $exceptionClass = null,
        private readonly ?string $file = null,
        private readonly ?int $line = null,
        private readonly ?string $stackTrace = null,
        private readonly array $context = [],
        private readonly string $level = 'error',
    ) {}

    public static function fromThrowable(Throwable $throwable, array $context = []): self
    {
        return new self(
            message: $throwable->getMessage(),
            exceptionClass: $throwable::class,
            file: $throwable->getFile(),
            line: $throwable->getLine(),
            stackTrace: $throwable->getTraceAsString(),
            context: $context,
        );
    }

    public function handle(): void
    {
        $request = Request::instance();

        ErrorLog::query()->create([
            'level' => $this->level,
            'message' => $this->message,
            'exception_class' => $this->exceptionClass,
            'file' => $this->file,
            'line' => $this->line,
            'stack_trace' => $this->stackTrace,
            'url' => $request?->fullUrl(),
            'http_method' => $request?->method(),
            'user_id' => Auth::id(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => $this->context,
            'request_input' => $request?->except(['password', 'password_confirmation']),
            'occurred_at' => now(),
            'status' => 'new',
        ]);
    }
}
