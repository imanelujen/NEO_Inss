<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDevis extends EditRecord
{
    protected static string $resource = DevisResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
