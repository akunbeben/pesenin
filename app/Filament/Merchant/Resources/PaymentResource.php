<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Traits\Orders\Serving;
use App\Traits\Orders\Status;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Laravel\Pennant\Feature;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function shouldRegisterNavigation(): bool
    {
        return Feature::for(Filament::getTenant())->active('feature_payment');
    }

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
        ];
    }
}
