<?php

namespace App\Filament\Central\Resources;

use App\Filament\Central\Resources\UserResource\Pages;
use App\Filament\Central\Resources\UserResource\RelationManagers;
use App\Models\Merchant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->autofocus()
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->autofocus()
                    ->columnSpanFull()
                    ->email()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->getStateUsing(fn (User $record) => $record->getFilamentAvatarUrl()),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->description(fn (User $record): string => $record->email),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->label(__('Email verification'))
                    ->icon(fn (?\Carbon\Carbon $state): string => match ((bool) $state) {
                        true => 'heroicon-m-check-circle',
                        false => 'heroicon-m-x-circle',
                    })
                    ->color(fn (?\Carbon\Carbon $state): string => match ((bool) $state) {
                        true => 'success',
                        false => 'warning',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('require_reset')
                    ->searchable()
                    ->toggleable()
                    ->label(__('Action required'))
                    ->icon(fn (bool $state): string => match ($state) {
                        false => 'heroicon-m-check-circle',
                        true => 'heroicon-m-exclamation-triangle',
                    })
                    ->color(fn (bool $state): string => match ($state) {
                        false => 'success',
                        true => 'warning',
                    })
                    ->tooltip(fn (User $record): ?string => ! $record->require_reset ? null : 'Please notify the user to reset their password as soon as possible for security reasons.')
                    ->alignCenter(),
                Tables\Columns\ToggleColumn::make('paid')
                    ->afterStateUpdated(function (User $record, bool $state) {
                        $record->loadMissing('merchants.setting');

                        Feature::for($record)->activate('can-have-payment', $state);

                        match ($state) {
                            false => $record->merchants
                                ->where('setting.payment', true)
                                ->each(function (Merchant $merchant) use ($state) {
                                    $merchant->update(['was_paid' => $merchant->setting->payment]);
                                    $merchant->setting->update(['payment' => $state]);
                                    Feature::for($merchant)->activate('feature_payment', $state);
                                }),
                            true => $record->merchants
                                ->each(function (Merchant $merchant) use ($state) {
                                    $merchant->setting->update(['payment' => (bool) $merchant->was_paid]);

                                    if ($merchant->was_paid) {
                                        Feature::for($merchant)->activate('feature_payment', $state);
                                    }
                                })
                        };
                    })
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->toggleable()
                    ->label(__('Time'))
                    ->description(fn (User $record) => $record->updated_at->format('M j, Y H:i:s'))
                    ->toggleable()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('require_reset')
                    ->label(__('Action'))
                    ->trueLabel('No action reqiured')
                    ->falseLabel('Action required'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        return [
                            ...$data,
                            'password' => Str::random(),
                            'email_verified_at' => now()->toDateTimeString(),
                            'require_reset' => true,
                        ];
                    })
                    ->after(function (User $record) {
                        $record->sendPasswordResetNotification(Password::getRepository()->create($record));
                    })
                    ->createAnother(false)
                    ->icon('heroicon-m-plus')
                    ->modalWidth('xl'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('User details'))
                    ->description(
                        fn (User $record): ?string => ! $record->require_reset
                            ? 'No action required at this time.'
                            : 'Please notify the user to reset their password as soon as possible for security reasons.'
                    )
                    ->collapsible()
                    ->columns(2)
                    ->iconSize(\Filament\Support\Enums\IconSize::Large)
                    ->icon(fn (User $record): string => match ($record->require_reset) {
                        false => 'heroicon-m-check-circle',
                        true => 'heroicon-m-exclamation-triangle',
                    })
                    ->iconColor(fn (User $record): string => match ($record->require_reset) {
                        false => 'success',
                        true => 'warning',
                    })
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('Name')),
                        Infolists\Components\TextEntry::make('email')
                            ->label(__('Email')),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime()
                            ->label(__('Created at')),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime()
                            ->label(__('Last updated')),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('employee_of', null);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MerchantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
