<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-m-archive-box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Images'))
                    ->description(__('Product\'s images that will show to customer.'))
                    ->aside()
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('banner')
                            ->hiddenLabel()
                            ->minFiles(1)
                            ->collection('banner')
                            ->multiple()
                            ->image()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Product details'))
                    ->description(__('Product\'s information.'))
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->label(__('Price'))
                            ->required()
                            ->numeric()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Variants'))
                    ->description(__('Product\'s variants.'))
                    ->aside()
                    ->schema([
                        Forms\Components\Repeater::make('variants')
                            ->simple(Forms\Components\TextInput::make('size')),
                    ]),
                Forms\Components\Section::make(__('Availability'))
                    ->description(__('Product\'s availability and recommendation.'))
                    ->aside()
                    ->schema([
                        Forms\Components\Toggle::make('availability')
                            ->label(__('Availability'))
                            ->helperText(__('You can choose to display or hide this product for your customers'))
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('recommended')
                            ->label(__('Recommendation'))
                            ->helperText(__('You can activate it to feature this product for your customers'))
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Product $record) => str($record->description)->words(10))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
