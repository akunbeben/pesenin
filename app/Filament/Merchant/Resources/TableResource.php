<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\TableResource\Pages;
use App\Models\Table as Model;
use App\Services\Tables\QRGenerator;
use App\Traits\Tables\QRStatus;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Pennant\Feature;

class TableResource extends Resource
{
    protected static ?string $model = Model::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) Filament::getTenant()->business_id;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Backoffice');
    }

    public static function getNavigationLabel(): string
    {
        return __('Tables');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Table');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Table');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\TextInput::make('prefix')
                    ->label(__('Prefix'))
                    ->default(fn () => static::getEloquentQuery()->latest()->value('prefix'))
                    ->string(),
                Forms\Components\TextInput::make('number')
                    ->label(__('Table number'))
                    ->default(fn () => static::getEloquentQuery()->count() + 1)
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('suffix')
                    ->label(__('Suffix'))
                    ->default(fn () => static::getEloquentQuery()->latest()->value('suffix'))
                    ->string(),
                Forms\Components\TextInput::make('seats')
                    ->label(__('Seats'))
                    ->numeric()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Grid::make(4)->schema([
                Infolists\Components\Section::make()->columns(4)->schema([
                    Infolists\Components\TextEntry::make('name')
                        ->translateLabel(),
                    Infolists\Components\TextEntry::make('number')
                        ->translateLabel(),
                    Infolists\Components\TextEntry::make('seats')
                        ->label(__('Capacity'))
                        ->translateLabel()
                        ->suffix(__(' Person')),
                    Infolists\Components\TextEntry::make('qr_status')
                        ->badge()
                        ->formatStateUsing(fn (Model $record) => $record->qr_status->label())
                        ->color(fn (Model $record) => $record->qr_status->color())
                        ->icon(fn (Model $record) => $record->qr_status->icon())
                        ->label(__('QR Code')),
                ])->columnSpan(3),
                Infolists\Components\Section::make(__('QR Code'))
                    ->description(__('Tap to see more details'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Infolists\Components\ImageEntry::make('url')
                            ->getStateUsing(fn (Model $record) => $record->getFirstMediaUrl('qr'))
                            ->hiddenLabel()
                            ->height('auto')
                            ->extraImgAttributes(['class' => 'w-full rounded-xl']),
                    ])->columnSpan(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->poll('10s')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Table'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('qr_status')
                    ->badge()
                    ->formatStateUsing(fn (Model $record) => $record->qr_status->label())
                    ->color(fn (Model $record): string => $record->qr_status->color())
                    ->icon(fn (Model $record): string => $record->qr_status->icon())
                    ->label(__('QR Code')),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->toggleable()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('process')
                    ->label(__('Generate QR'))
                    ->icon('heroicon-o-arrow-path')
                    ->color(fn (Model $record) => $record->qr_status->color())
                    ->visible(fn (Model $record): bool => $record->qr_status === QRStatus::None && ! Feature::for($record->merchant)->active('feature_ikiosk'))
                    ->action(function (Model $record, QRGenerator $service) {
                        /** @var \App\Models\Table $table */
                        $table = $record;

                        if (! $table->getFirstMedia('qr')) {
                            /** @var \SimpleSoftwareIO\QrCode\Generator $service */
                            $service = app(\SimpleSoftwareIO\QrCode\Generator::class);

                            $table->addMediaFromBase64(base64_encode(
                                $service->format('png')
                                    ->margin(2)
                                    ->size(1000)
                                    ->generate($table->url)
                            ))->toMediaCollection('qr');

                            $table->update(['qr_status' => QRStatus::Generated]);
                        }
                    }),
                Tables\Actions\Action::make('open_url')
                    ->label(__('Open URL'))
                    ->icon('heroicon-m-arrow-up-right')
                    ->visible(fn (Model $record) => ! Feature::for($record->merchant)->active('feature_ikiosk'))
                    ->url(fn (Model $record) => $record->url, true),
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->color(fn (Model $record) => $record->qr_status->color())
                    ->visible(fn (Model $record): bool => $record->qr_status === QRStatus::Generated && ! Feature::for($record->merchant)->active('feature_ikiosk'))
                    ->action(function (Model $record) {
                        return response()->download($record->getFirstMediaPath('qr'), "{$record->uuid}.png");
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalWidth('xl')
                        ->hidden($deleted = fn (Model $record): bool => (bool) $record->deleted_at && Feature::for($record->merchant)->active('feature_ikiosk')),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make()->visible($deleted),
                    Tables\Actions\ForceDeleteAction::make()->visible($deleted),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['merchant']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTables::route('/'),
            'view' => Pages\ViewTable::route('/{record}'),
        ];
    }
}
