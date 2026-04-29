<?php

namespace App\Enum;

enum NotificationLevel: string
{
    case INFO = 'info';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';

    public function getCssClass(): string
    {
        return match ($this) {
            self::INFO => 'info',
            self::SUCCESS => 'success',
            self::WARNING => 'warning',
            self::ERROR => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::INFO => 'fa-circle-info',
            self::SUCCESS => 'fa-circle-check',
            self::WARNING => 'fa-triangle-exclamation',
            self::ERROR => 'fa-circle-exclamation',
        };
    }
}
