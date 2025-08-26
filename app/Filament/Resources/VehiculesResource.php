<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculesResource\Pages;
use App\Filament\Resources\VehiculesResource\RelationManagers;
use App\Models\Vehicule;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehiculesResource extends Resource
{
    protected static ?string $model = Vehicule::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Select::make('vehicle_type')
                ->options([
                    'sedan' => 'Sedan',
                    'suv' => 'SUV',
                    'truck' => 'Truck',
                    'motorcycle' => 'Motorcycle',
                ])
                ->required(),

            Forms\Components\TextInput::make('make')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('model')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('fuel_type')
                ->options([
                    'ESSENCE' => 'Essence',
                    'DIESEL' => 'Diesel',
                    'ELECTRIQUE' => 'Électrique',
                    'HYBRIDE' => 'Hybride',
                ])
                ->required(),

            Forms\Components\TextInput::make('tax_horsepower')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('vehicle_value')
                ->numeric()
                ->required(),

            Forms\Components\DatePicker::make('registration_date')
                ->required(),
        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('vehicle_type'),
            Tables\Columns\TextColumn::make('make'),
            Tables\Columns\TextColumn::make('model'),
            Tables\Columns\TextColumn::make('fuel_type'),
            Tables\Columns\TextColumn::make('tax_horsepower'),
            Tables\Columns\TextColumn::make('vehicle_value'),
            Tables\Columns\TextColumn::make('registration_date')->date(),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
