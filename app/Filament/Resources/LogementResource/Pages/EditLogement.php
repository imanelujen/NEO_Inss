<?php

namespace App\Filament\Resources\LogementResource\Pages;

use App\Filament\Resources\LogementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogement extends EditRecord
{
    protected static string $resource = LogementResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
