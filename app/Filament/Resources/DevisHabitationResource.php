<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DevisHabitationResource\Pages;
use App\Filament\Resources\DevisHabitationResource\RelationManagers;
use App\Models\DevisHabitation;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DevisHabitationResource extends Resource
{
    protected static ?string $model = DevisHabitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\Select::make('id_devis')
                ->relationship('devis', 'id')
                ->required(),
            Forms\Components\TextInput::make('id_logement')->numeric()->required(),
            Forms\Components\Textarea::make('formules_choisis')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('devis.id')->label('Devis ID'),
            Tables\Columns\TextColumn::make('id_logement'),
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
            'index' => Pages\ListDevisHabitations::route('/'),
            'create' => Pages\CreateDevisHabitation::route('/create'),
            'edit' => Pages\EditDevisHabitation::route('/{record}/edit'),
        ];
    }
}
