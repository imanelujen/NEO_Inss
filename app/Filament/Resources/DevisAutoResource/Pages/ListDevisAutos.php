<?php

namespace App\Filament\Resources\DevisAutoResource\Pages;

use App\Filament\Resources\DevisAutoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDevisAutos extends ListRecords
{
    protected static string $resource = DevisAutoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
