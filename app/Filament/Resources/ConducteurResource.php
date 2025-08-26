<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConducteurResource\Pages;
use App\Filament\Resources\ConducteurResource\RelationManagers;
use App\Models\Conducteur;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConducteurResource extends Resource
{
    protected static ?string $model = Conducteur::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\TextInput::make('bonus_malus')
                ->numeric()
                ->default(1.00)
                ->step(0.01)
                ->required(),

            Forms\Components\Textarea::make('historique_accidents')
                ->label('Historique des accidents')
                ->rows(4),

            Forms\Components\DatePicker::make('date_obtention_permis')
                ->label('Date d\'obtention du permis')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('bonus_malus'),
            Tables\Columns\TextColumn::make('historique_accidents')
                ->limit(50),
            Tables\Columns\TextColumn::make('date_obtention_permis')->date(),
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
            'index' => Pages\ListConducteurs::route('/'),
            'create' => Pages\CreateConducteur::route('/create'),
            'edit' => Pages\EditConducteur::route('/{record}/edit'),
        ];
    }    
}
