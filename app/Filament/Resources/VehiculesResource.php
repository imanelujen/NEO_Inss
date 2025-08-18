<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculesResource\Pages;
use App\Filament\Resources\VehiculesResource\RelationManagers;
use App\Models\Vehicules;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehiculesResource extends Resource
{
    protected static ?string $model = Vehicules::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicules::route('/'),
            'create' => Pages\CreateVehicules::route('/create'),
            'edit' => Pages\EditVehicules::route('/{record}/edit'),
        ];
    }
}
