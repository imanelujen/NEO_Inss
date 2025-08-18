<?php

namespace App\Filament\Resources\DevisHabitationResource\Pages;

use App\Filament\Resources\DevisHabitationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDevisHabitations extends ListRecords
{
    protected static string $resource = DevisHabitationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
