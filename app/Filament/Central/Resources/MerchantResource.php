<?php

namespace App\Filament\Central\Resources;

use App\Filament\Central\Resources\MerchantResource\Pages;
use App\Models\Merchant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MerchantResource extends Resource
{
    protected static ?string $model = Merchant::class;

    protected static ?string $navigationIcon = 'heroicon-m-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->autofocus()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('phone')
                    ->label(__('Phone'))
                    ->maxLength(13)
                    ->tel()
                    ->telRegex('/^0[8][1-9]\d{1}[\s-]?\d{4}[\s-]?\d{2,5}$/')
                    ->helperText(__('Example: 081234567890'))
                    ->unique(ignorable: fn (?Merchant $record) => $record)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('address')
                    ->label(__('Address'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->getStateUsing(fn (Merchant $record) => $record->getFilamentAvatarUrl()),
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Merchant $record) => $record->address)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->modalWidth('xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Information')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-m-adjustments-horizontal')
                            ->columns(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('name'),
                                Infolists\Components\TextEntry::make('phone'),
                                Infolists\Components\TextEntry::make('address'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('History')
                            ->icon('heroicon-m-clock')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')->dateTime(),
                                Infolists\Components\TextEntry::make('updated_at')->dateTime(),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMerchants::route('/'),
            'view' => Pages\ViewMerchant::route('/{record}'),
        ];
    }
}
