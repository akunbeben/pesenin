<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-m-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label(__('Name'))->required()->autofocus()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Name')),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label(__('Created at')),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->label(__('Updated at')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('lg'),
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
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}