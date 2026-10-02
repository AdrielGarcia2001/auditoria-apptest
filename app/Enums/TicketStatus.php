<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Verified = 'verified';
    case Reopened = 'reopened';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Assigned => 'Asignado',
            self::InProgress => 'En progreso',
            self::Completed => 'Completado',
            self::Verified => 'Verificado',
            self::Reopened => 'Reabierto',
            self::Cancelled => 'Cancelado',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $status) => ['value' => $status->value, 'label' => $status->label()],
            self::cases(),
        );
    }
}
