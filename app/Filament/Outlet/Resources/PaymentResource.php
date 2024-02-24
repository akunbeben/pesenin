<?php

namespace App\Filament\Outlet\Resources;

use App\Filament\Outlet\Resources\PaymentResource\Pages;
use App\Models\Item;
use App\Models\Payment;
use App\Traits\Orders\Serving;
use App\Traits\Orders\Status;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationGroup(): ?string
    {
        return __('Transaction');
    }

    public static function getNavigationLabel(): string
    {
        return __('Payment history');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Payment history');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Grid::make([
                'default' => 1,
                'md' => 2,
            ])->schema([
                Infolists\Components\Grid::make()->schema([
                    Infolists\Components\Section::make(__('Details of :label', ['label' => __('payment')]))
                        ->collapsible()
                        ->columns(2)
                        ->schema([
                            Infolists\Components\TextEntry::make('status')
                                ->getStateUsing(fn (Payment $record) => $record->data->status)
                                ->badge()
                                ->color(fn (string $state) => match ($state) {
                                    'PENDING' => 'warning',
                                    'SETTLED', 'PAID' => 'success',
                                    default => 'gray',
                                }),
                            Infolists\Components\TextEntry::make('payment_channel')
                                ->getStateUsing(fn (Payment $record) => $record->data->payment_channel)
                                ->translateLabel(),
                            Infolists\Components\TextEntry::make('paid_at')
                                ->getStateUsing(fn (Payment $record) => Carbon::parse($record->data->paid_at))
                                ->dateTime()
                                ->translateLabel(),
                            Infolists\Components\TextEntry::make('paid_amount')
                                ->getStateUsing(
                                    fn (Payment $record) => Number::currency($record->data->paid_amount, 'IDR', config('app.locale'))
                                )
                                ->translateLabel(),
                            Infolists\Components\RepeatableEntry::make('data.fees')
                                ->getStateUsing(fn (Payment $record) => $record->data?->fees ?? [])
                                ->hidden(fn (?array $state) => blank($state))
                                ->columnSpanFull()
                                ->columns(2)
                                ->label(__('Other fees'))
                                ->schema([
                                    Infolists\Components\TextEntry::make('type')
                                        ->formatStateUsing(fn (string $state) => str(match ($state) {
                                            'fee' => __('Payment gateway fee'),
                                            default => __('Tax'),
                                        })->ucfirst())
                                        ->hiddenLabel(),
                                    Infolists\Components\TextEntry::make('value')
                                        ->formatStateUsing(fn (int $state) => Number::currency(
                                            $state,
                                            'IDR',
                                            config('app.locale')
                                        ))
                                        ->hiddenLabel(),
                                ]),
                        ]),
                    Infolists\Components\Section::make(__('Device'))
                        ->collapsible()
                        ->collapsed()
                        ->columns(2)
                        ->schema([
                            Infolists\Components\TextEntry::make('order.scan.ip')
                                ->label(__('IP'))
                                ->copyable()
                                ->icon('heroicon-o-clipboard')
                                ->iconPosition(IconPosition::After),
                            Infolists\Components\TextEntry::make('order.scan.updated_at')
                                ->label(__('Scanned at'))
                                ->dateTime(),
                            Infolists\Components\TextEntry::make('order.scan.agent')
                                ->label(__('User Agent'))
                                ->columnSpanFull()
                                ->copyable(),
                        ]),
                ])->columnSpan([
                    'default' => 'full',
                    'md' => 1,
                ]),
                Infolists\Components\Section::make(__('Details of :label', ['label' => __('order')]))->columnSpan([
                    'default' => 'full',
                    'md' => 1,
                ])
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('order_status')
                            ->getStateUsing(fn (Payment $record) => $record->order->serving)
                            ->badge()
                            ->translateLabel(),
                        Infolists\Components\TextEntry::make('order.scan.table.name')
                            ->label(__('Table')),
                        Infolists\Components\RepeatableEntry::make('order.items')
                            ->label(__('Ordered items'))
                            ->getStateUsing(fn (Payment $record) => $record->order->items)
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('snapshot.name')
                                    ->formatStateUsing(fn (Item $record) => "{$record->amount}x {$record->snapshot->name}")
                                    ->hiddenLabel(),
                                Infolists\Components\TextEntry::make('snapshot.price')
                                    ->formatStateUsing(fn (Item $record, string $state) => Number::currency(
                                        $state * $record->amount,
                                        'IDR',
                                        config('app.locale')
                                    ))
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_id')
                    ->getStateUsing(fn (Payment $record) => $record->data->external_id)
                    ->label(__('Ref'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('data->external_id', 'LIKE', "%{$search}%")),
                Tables\Columns\TextColumn::make('payment_channel')
                    ->getStateUsing(fn (Payment $record) => $record->data->payment_channel)
                    ->label(__('Payment method'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('data->payment_channel', 'LIKE', "%{$search}%")),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(fn (Payment $record) => str($record->data->status)->title()->toString())
                    ->color(fn (Payment $record) => match ($record->data->status) {
                        'PENDING' => 'warning',
                        'SETTLED', 'PAID' => 'success',
                        default => 'gray',
                    })
                    ->label(__('Status')),
                Tables\Columns\TextColumn::make('amount')
                    ->getStateUsing(fn (Payment $record) => Number::currency($record->data->paid_amount, 'IDR', config('app.locale')))
                    ->label(__('Total')),
                Tables\Columns\TextColumn::make('paid_at')
                    ->getStateUsing(fn (Payment $record) => $record->data->paid_at)
                    ->dateTime()
                    ->label(__('Paid at')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->hidden(fn (Payment $record) => $record->loadMissing('order')->order->status === Status::Success)
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->action(function (Payment $record) {
                        $record->loadMissing('order');

                        DB::beginTransaction();

                        try {
                            $record->order->update([
                                'status' => Status::Success,
                                'serving' => Serving::Waiting,
                            ]);

                            $record->update([
                                'data' => array_merge((array) $record->data, [
                                    'status' => 'PAID',
                                    'paid_at' => now(),
                                ]),
                            ]);
                        } catch (\Throwable $th) {
                            DB::rollBack();

                            if (! app()->isProduction()) {
                                throw $th;
                            }

                            logger()->error($th->getMessage(), $th->getTrace());

                            Notification::make()
                                ->title(app()->isProduction() ? __('Payment confirmation failed') : $th->getMessage())
                                ->body(app()->isProduction() ? __('Please try again.') : $th->getTraceAsString())
                                ->danger()
                                ->send();

                            $this->halt();
                        }

                        DB::commit();
                        Notification::make()
                            ->title($record->order->number)
                            ->body(__('Payment confirmation success.'))
                            ->success()
                            ->send();
                    })
                    ->tooltip(__('Mark payment as confirmed'))
                    ->translateLabel(),
                Tables\Actions\Action::make('prioritize')
                    ->hidden(fn (Payment $record) => $record->priority)
                    ->icon('heroicon-m-exclamation-circle')
                    ->action(fn (Payment $record) => $record->update(['priority' => true]))
                    ->tooltip(__('Mark as priority'))
                    ->translateLabel(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->latest('data->status')
            ->latest('data->paid_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
