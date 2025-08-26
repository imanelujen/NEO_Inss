<?php

namespace App\Filament\Resources\ContratAutoResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContratRelationManager extends RelationManager
{
    protected static string $relationship = 'Contrat';

    protected static ?string $recordTitleAttribute = 'ContratAutoRelationManager';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Select::make('id_vehicule')
                ->relationship('vehicule', 'model')
                ->required(),

            Forms\Components\Select::make('id_conducteur')
                ->relationship('conducteur', 'id')
                ->required(),

            Forms\Components\Textarea::make('garanties')->required(),

            Forms\Components\FileUpload::make('permis_path')->label('Permis de conduire'),
            Forms\Components\FileUpload::make('cin_recto_path')->label('CIN Recto'),
            Forms\Components\FileUpload::make('cin_verso_path')->label('CIN Verso'),

            Forms\Components\TextInput::make('franchise')->numeric()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('vehicule.model'),
            Tables\Columns\TextColumn::make('conducteur.id'),
            Tables\Columns\TextColumn::make('franchise'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
