<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('prenom')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->unique(ignoreRecord: true)
                ->required(),

            Forms\Components\TextInput::make('phone')
                ->tel(),

            Forms\Components\TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => \Hash::make($state))
                ->required(fn (string $context) => $context === 'create')
                ->maxLength(255),

            Forms\Components\Select::make('statut')
                ->options([
                    'ACTIF' => 'Actif',
                    'INACTIF' => 'Inactif',
                    'SUSPENDU' => 'Suspendu',
                ])
                ->default('ACTIF')
                ->required(),

            Forms\Components\DatePicker::make('date_inscription')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('prenom')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('phone'),
Tables\Columns\BadgeColumn::make('statut')
    ->enum([
        'ACTIF' => 'Actif',
        'INACTIF' => 'Inactif',
        'SUSPENDU' => 'Suspendu',
    ])
    ->colors([
        'success' => 'ACTIF',
        'warning' => 'INACTIF',
        'danger' => 'SUSPENDU',
    ]),

            Tables\Columns\TextColumn::make('date_inscription')
                ->date(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
