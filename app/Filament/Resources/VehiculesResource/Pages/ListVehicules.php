<?php

namespace App\Filament\Resources\VehiculesResource\Pages;

use App\Filament\Resources\VehiculesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicules extends ListRecords
{
    protected static string $resource = VehiculesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
