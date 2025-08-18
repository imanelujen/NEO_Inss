<?php

namespace App\Filament\Resources\AgencesResource\Pages;

use App\Filament\Resources\AgencesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgences extends EditRecord
{
    protected static string $resource = AgencesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
