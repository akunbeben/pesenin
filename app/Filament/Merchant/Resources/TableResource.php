<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\TableResource\Pages;
use App\Models\Table as Model;
use App\Services\Tables\QRGenerator;
use App\Traits\Tables\QRStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Hashids\Hashids;
use Illuminate\Support\Collection;
use Livewire\Component;

class TableResource extends Resource
{
    protected static ?string $model = Model::class;

    protected static ?string $navigationIcon = 'heroicon-m-square-3-stack-3d';

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Table')),
                Tables\Columns\TextColumn::make('url')
                    ->default('Open URL')
                    ->badge()
                    ->icon('heroicon-m-arrow-up-right')
                    ->url(fn (Model $record) => route(
                        'redirector',
                        [
                            'uid' => $record->uuid,
                            'k' => (new Hashids(config('app.key'), 10))->encode($record->created_at->timestamp),
                        ],
                    ), true),
                Tables\Columns\TextColumn::make('qr_status')
                    ->badge()
                    ->formatStateUsing(fn (Model $record) => $record->qr_status->label())
                    ->color(fn (Model $record) => $record->qr_status->color())
                    ->icon(fn (Model $record) => $record->qr_status->icon())
                    ->label(__('QR Code')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Time'))
                    ->description(fn (Model $record) => $record->updated_at->format('M j, Y H:i:s'))
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
                    ->visible(fn (Model $record): bool => $record->qr_status === QRStatus::None)
                    ->action(function (Component $livewire, Model $record, QRGenerator $service) {
                        $service->handle($livewire, $record);
                    }),
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->color(fn (Model $record) => $record->qr_status->color())
                    ->visible(fn (Model $record): bool => $record->qr_status === QRStatus::Generated)
                    ->action(function (Component $livewire, Model $record) {
                        dd($livewire, $record);
                    }),
                Tables\Actions\EditAction::make()
                    ->modalWidth('xl')
                    ->hidden($deleted = fn (Model $record): bool => (bool) $record->deleted_at),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make()->visible($deleted),
                Tables\Actions\ForceDeleteAction::make()->visible($deleted),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('download_all')
                        ->label(__('Download available QR'))
                        ->icon('heroicon-o-cloud-arrow-down')
                        ->color('primary')
                        ->action(function (Component $livewire, Collection $records, QRGenerator $service) {
                            $service->handle($livewire, $records);
                        })
                        ->hidden(function (HasTable $livewire): bool {
                            $trashedFilterState = $livewire->getTableFilterState(TrashedFilter::class) ?? [];

                            if (! array_key_exists('value', $trashedFilterState)) {
                                return false;
                            }

                            if ($trashedFilterState['value']) {
                                return false;
                            }

                            return filled($trashedFilterState['value']);
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTables::route('/'),
        ];
    }
}
