<?php

namespace App\Enums;

enum VisitStatus: string
{
    case Scheduled = 'scheduled';
    case InRoute = 'in_route';
    case OnSite = 'on_site';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Programada',
            self::InRoute => 'En ruta',
            self::OnSite => 'En sitio',
            self::Completed => 'Completada',
            self::Cancelled => 'Cancelada',
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
