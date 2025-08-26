<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Filament\Resources\ContratResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContrat extends EditRecord
{
    protected static string $resource = ContratResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
