<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\OperatorResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Laravel\Pennant\Feature;

class OperatorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $tenantOwnershipRelationshipName = 'employer';

    protected static ?string $tenantRelationshipName = 'employees';

    public static function canViewAny(): bool
    {
        return Feature::for(Filament::getTenant())->active('feature_payment');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Backoffice');
    }

    public static function getNavigationLabel(): string
    {
        return __('Operators');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Operator');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Operator');
    }

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
                    ->tooltip(fn (User $record): ?string => !$record->require_reset ? null : 'Please notify the user to reset their password as soon as possible for security reasons.')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->label(__('Time'))
                    ->description(fn (User $record) => $record->updated_at->format('M j, Y H:i:s'))
                    ->toggleable()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOperators::route('/'),
        ];
    }
}
