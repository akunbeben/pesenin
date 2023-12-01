<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\OperatorResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OperatorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-user-circle';

    protected static ?string $tenantOwnershipRelationshipName = 'employer';

    public static function getNavigationGroup(): ?string
    {
        return __('Back of House');
    }

    public static function getNavigationLabel(): string
    {
        return __('Operators');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Operator');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Operator');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOperators::route('/'),
        ];
    }
}
