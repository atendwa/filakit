<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Utils;

use Filament\Notifications\Notification;
use Throwable;

class FilamentNotifier
{
    private ?string $body;

    public function __construct(Throwable|string|null $body)
    {
        $this->body = $body instanceof Throwable ? $body->getMessage() : $body;
    }

    public function success(?string $title = null): void
    {
        if (blank($title)) {
            $title = collect(arrayOfStrings(config('filakit.success_notification_titles')))->random();
        }

        $this->notify($title, 'success');
    }

    public function info(string $title = 'A Heads Up!'): void
    {
        $this->notify($title, 'info');
    }

    public function warning(string $title = 'A Heads Up!'): void
    {
        $this->notify($title, 'warning');
    }

    public function error(string $title = 'Something Went Wrong!'): void
    {
        $this->notify($title, 'danger');
    }

    private function notify(string $title, string $type): void
    {
        $notification = Notification::make('alert')->icon($this->icon($type))->title($title)->body($this->body);

        (match ($type) {
            'warning' => $notification->warning()->persistent(),
            'danger' => $notification->danger()->persistent(),
            default => $notification->success(),
            'info' => $notification->info(),
        })->send();
    }

    private function icon(string $type): string
    {
        return match ($type) {
            'info' => 'heroicon-o-chat-bubble-left-ellipsis',
            'danger' => 'heroicon-o-exclamation-triangle',
            'warning' => 'heroicon-o-exclamation-circle',
            default => 'heroicon-o-sparkles',
        };
    }
}
