<?php

namespace App\Filament\Central\Resources;

use App\Filament\Central\Resources\UserResource\Pages;
use App\Filament\Central\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                    ->tooltip(fn (User $record): ?string => ! $record->require_reset ? null : "{$record->email}'s password need to be reset.")
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
                //
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
                            ? 'No action required.'
                            : "{$record->email}'s password need to be reset."
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
