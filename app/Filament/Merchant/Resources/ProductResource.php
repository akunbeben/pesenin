<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-m-squares-2x2';

    protected static ?string $navigationGroup = 'Front of House';

    public static function getNavigationLabel(): string
    {
        return __('Products');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Product');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Product');
    }

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
                            ->conversion('thumbnail')
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
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name', fn (Builder $query) => $query->where('merchant_id', Filament::getTenant()->getKey()))
                            ->native(false)
                            ->label(__('Category'))
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Variants'))
                    ->description(__('Product\'s variants.'))
                    ->aside()
                    ->schema([
                        Forms\Components\Repeater::make('variants')->simple(Forms\Components\TextInput::make('size'))->default(null),
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
                Grid::make()->columns(1)->schema([
                    Tables\Columns\SpatieMediaLibraryImageColumn::make('banner')
                        ->collection('banner')
                        ->extraImgAttributes(['class' => 'w-full !aspect-square rounded-xl'])
                        ->extraAttributes(['class' => '!w-full'])
                        ->limit(1)
                        ->height('auto'),
                    Tables\Columns\TextColumn::make('name')
                        ->description(fn (Product $record) => str($record->description)->words(10))
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('price')
                        ->searchable()
                        ->sortable()
                        ->formatStateUsing(fn (Product $record) => 'Rp ' . number_format($record->price, 0, ',', '.')),
                ]),
            ])
            ->contentGrid(['md' => 2, 'xl' => 4, 'sm' => 2])
            ->defaultSort('id', 'desc')
            ->paginationPageOptions([8, 16, 24])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
