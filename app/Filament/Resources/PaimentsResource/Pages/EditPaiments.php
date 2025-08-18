<?php

namespace App\Filament\Resources\PaimentsResource\Pages;

use App\Filament\Resources\PaimentsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaiments extends EditRecord
{
    protected static string $resource = PaimentsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
