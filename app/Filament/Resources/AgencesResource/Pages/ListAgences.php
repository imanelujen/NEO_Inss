<?php

namespace App\Filament\Resources\AgencesResource\Pages;

use App\Filament\Resources\AgencesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgences extends ListRecords
{
    protected static string $resource = AgencesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
