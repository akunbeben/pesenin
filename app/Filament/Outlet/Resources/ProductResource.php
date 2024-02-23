<?php

namespace App\Filament\Outlet\Resources;

use App\Filament\Outlet\Resources\ProductResource\Pages;
use App\Models\Product;
use Detection\MobileDetect;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $detect = new MobileDetect();

        return $table
            ->columns([
                Tables\Columns\Layout\Grid::make()
                    ->extraAttributes(['class' => '!p-0'])
                    ->columns(1)
                    ->schema([
                        Tables\Columns\SpatieMediaLibraryImageColumn::make('banner')
                            ->collection('banner')
                            ->extraImgAttributes(['class' => 'w-full !aspect-square rounded-xl'])
                            ->extraAttributes(['class' => '!w-full'])
                            ->limit(1)
                            ->height('auto'),
                        Tables\Columns\TextColumn::make('name')
                            ->description(fn (Product $record) => str($record->description)->words($detect->isMobile() ? ($detect->isTablet() ? 7 : 5) : 10))
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('price')
                            ->searchable()
                            ->sortable()
                            ->formatStateUsing(fn (Product $record) => 'Rp ' . number_format($record->price, 0, ',', '.')),
                        Tables\Columns\TextColumn::make('availability')
                            ->badge()
                            ->formatStateUsing(fn (Product $record) => match ($record->availability) {
                                true => __('Available'),
                                false => __('Unavailable'),
                            })
                            ->color(fn (Product $record) => match ($record->availability) {
                                true => 'success',
                                false => 'danger',
                            }),
                    ]),
            ])
            ->contentGrid(['md' => 2, 'xl' => 4, 'sm' => 2, 'default' => 1])
            ->defaultSort('id', 'desc')
            ->paginationPageOptions([8, 16, 24])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('mark_as_unavailable')
                    ->hidden(fn (Product $record) => ! $record->availability)
                    ->icon('heroicon-m-x-mark')
                    ->color('gray')
                    ->translateLabel()
                    ->action(fn (Product $record) => $record->update(['availability' => false]))
                    ->successNotificationTitle(__('Product marked as unavailable')),
                Tables\Actions\Action::make('mark_as_available')
                    ->hidden(fn (Product $record) => $record->availability)
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->translateLabel()
                    ->action(fn (Product $record) => $record->update(['availability' => true]))
                    ->successNotificationTitle(__('Product marked as available')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
