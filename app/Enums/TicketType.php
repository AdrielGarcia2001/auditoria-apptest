<?php

namespace App\Enums;

enum TicketType: string
{
    case Preventive = 'preventive';
    case Corrective = 'corrective';

    public function label(): string
    {
        return match ($this) {
            self::Preventive => 'Preventivo',
            self::Corrective => 'Correctivo',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $type) => ['value' => $type->value, 'label' => $type->label()],
            self::cases(),
        );
    }
}
