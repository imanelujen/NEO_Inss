<?php

namespace App\Filament\Resources\ConducteurResource\Pages;

use App\Filament\Resources\ConducteurResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConducteur extends EditRecord
{
    protected static string $resource = ConducteurResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
