<?php

namespace App\Filament\Central\Resources\UserResource\RelationManagers;

use App\Models\Merchant;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MerchantsRelationManager extends RelationManager
{
    protected static string $relationship = 'merchants';

    public function form(Form $form): Form
    {
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading(__('Owned merchants'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Merchant $record) => $record->address)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Time'))
                    ->description(fn (Merchant $record) => $record->updated_at->format('M j, Y H:i:s'))
                    ->toggleable()
                    ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }
}
