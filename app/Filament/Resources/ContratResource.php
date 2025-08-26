<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContratResource\Pages;
use App\Filament\Resources\ContratResource\RelationManagers;
use App\Models\Contrat;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContratResource extends Resource
{
    protected static ?string $model = Contrat::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                            Forms\Components\Select::make('type_contrat')
                ->options([
                    'AUTO' => 'Auto',
                    'HABITATION' => 'Habitation',
                ])
                ->required(),

            Forms\Components\Select::make('id_client')
                ->relationship('client', 'nom') // adjust field
                ->searchable()
                ->required(),

            Forms\Components\Select::make('id_devis')
                ->relationship('devis', 'id') // adjust field
                ->searchable()
                ->required(),

            Forms\Components\Select::make('id_agent')
                ->relationship('agent', 'name') // if agent is user
                ->searchable()
                ->required(),

            Forms\Components\Select::make('id_paiement')
                ->relationship('paiement', 'id') // adjust
                ->searchable(),

            Forms\Components\DatePicker::make('start_date')->required(),
            Forms\Components\DatePicker::make('end_date')->required(),

            Forms\Components\TextInput::make('prime')->numeric()->required(),

            Forms\Components\Select::make('statut')
                ->options([
                    'ACTIF' => 'Actif',
                    'SUSPENDU' => 'Suspendu',
                    'RESILIE' => 'Résilié',
                    'EXPIRE' => 'Expiré',
                ])
                ->required(),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('type_contrat'),
            Tables\Columns\TextColumn::make('client.nom'),
            Tables\Columns\TextColumn::make('prime'),
            Tables\Columns\TextColumn::make('start_date')->date(),
            Tables\Columns\TextColumn::make('end_date')->date(),
            Tables\Columns\BadgeColumn::make('statut')
                ->colors([
                    'success' => 'ACTIF',
                    'warning' => 'SUSPENDU',
                    'danger'  => 'RESILIE',
                    'gray'    => 'EXPIRE',
                ]),
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
            'index' => Pages\ListContrats::route('/'),
            'create' => Pages\CreateContrat::route('/create'),
            'edit' => Pages\EditContrat::route('/{record}/edit'),
        ];
    }    
}
