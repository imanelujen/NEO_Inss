<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DevisResource\Pages;
use App\Filament\Resources\DevisResource\RelationManagers;
use App\Models\Devis;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DevisResource extends Resource
{
    protected static ?string $model = Devis::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\DatePicker::make('date_creation')->required(),
            Forms\Components\DatePicker::make('date_expiration')->required(),
            Forms\Components\TextInput::make('montant_base')->numeric()->required(),
            Forms\Components\Textarea::make('OFFRE_CHOISIE')->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'BROUILLON' => 'Brouillon',
                    'EN_COURS' => 'En cours',
                    'FINALISE' => 'Finalisé',
                    'ENVOYE' => 'Envoyé',
                    'EXPIRE' => 'Expiré',
                    'ACCEPTE' => 'Accepté',
                    'REFUSE' => 'Refusé',
                ])
                ->default('BROUILLON')
                ->required(),
            Forms\Components\Select::make('typedevis')
                ->options([
                    'AUTO' => 'Auto',
                    'HABITATION' => 'Habitation',
                ])
                ->required(),
            Forms\Components\TextInput::make('id_simulationsession')->numeric()->required(),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('date_creation')->date(),
            Tables\Columns\TextColumn::make('date_expiration')->date(),
            Tables\Columns\TextColumn::make('montant_base'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'secondary' => 'BROUILLON',
                    'warning' => 'EN_COURS',
                    'success' => 'ACCEPTE',
                    'danger' => 'REFUSE',
                ]),
            Tables\Columns\TextColumn::make('typedevis'),
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
            'index' => Pages\ListDevis::route('/'),
            'create' => Pages\CreateDevis::route('/create'),
            'edit' => Pages\EditDevis::route('/{record}/edit'),
        ];
    }
}
