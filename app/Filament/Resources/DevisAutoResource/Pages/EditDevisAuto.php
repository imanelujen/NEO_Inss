<?php

namespace App\Filament\Resources\DevisAutoResource\Pages;

use App\Filament\Resources\DevisAutoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDevisAuto extends EditRecord
{
    protected static string $resource = DevisAutoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
