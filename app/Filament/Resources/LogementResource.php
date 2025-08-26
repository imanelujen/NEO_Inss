<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogementResource\Pages;
use App\Filament\Resources\LogementResource\RelationManagers;
use App\Models\logement;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogementResource extends Resource
{
    protected static ?string $model = Logement::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Select::make('housing_type')
                ->options([
                    'APPARTEMENT' => 'Appartement',
                    'MAISON' => 'Maison',
                    'PAVILLON' => 'Pavillon',
                    'STUDIO' => 'Studio',
                    'LOFT' => 'Loft',
                    'VILLA' => 'Villa',
                ])
                ->required(),

            Forms\Components\TextInput::make('surface_area')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('housing_value')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('construction_year')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('ville')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('rue')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('code_postal')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('occupancy_status')
                ->options([
                    'Locataire' => 'Locataire',
                    'Propriétaire occupant' => 'Propriétaire occupant',
                    'Propriétaire non-occupant' => 'Propriétaire non-occupant',
                ])
                ->required(),
        
            ]);
    }

   public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('housing_type'),
            Tables\Columns\TextColumn::make('surface_area'),
            Tables\Columns\TextColumn::make('housing_value'),
            Tables\Columns\TextColumn::make('construction_year'),
            Tables\Columns\TextColumn::make('ville'),
            Tables\Columns\TextColumn::make('rue'),
            Tables\Columns\TextColumn::make('code_postal'),
            Tables\Columns\TextColumn::make('occupancy_status'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
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
            'index' => Pages\ListLogements::route('/'),
            'create' => Pages\CreateLogement::route('/create'),
            'edit' => Pages\EditLogement::route('/{record}/edit'),
        ];
    }
}
