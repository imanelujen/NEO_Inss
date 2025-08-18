<?php

namespace App\Filament\Resources\ConducteurResource\Pages;

use App\Filament\Resources\ConducteurResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConducteurs extends ListRecords
{
    protected static string $resource = ConducteurResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
