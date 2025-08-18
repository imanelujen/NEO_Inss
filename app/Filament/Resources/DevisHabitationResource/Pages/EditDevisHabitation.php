<?php

namespace App\Filament\Resources\DevisHabitationResource\Pages;

use App\Filament\Resources\DevisHabitationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDevisHabitation extends EditRecord
{
    protected static string $resource = DevisHabitationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
