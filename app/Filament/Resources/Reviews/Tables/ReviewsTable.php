<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Customers\CustomerResource;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->url(fn($record) => ProductResource::getUrl('edit', [$record->product]))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->url(fn($record) => CustomerResource::getUrl('edit', [$record->customer]))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                /*                 TextColumn::make('order.name')
                    ->url(fn($record) => OrderResource::getUrl('edit', [$record->order]))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(), */
                TextColumn::make('rating')
                    ->formatStateUsing(fn($state) => str_repeat('★', $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('title')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('comment')
                    ->limit(100)
                    ->wrap()
                    ->searchable(),
                IconColumn::make('is_verified_purchase')
                    ->boolean(),
                IconColumn::make('is_approved')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'Desc')
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Approval Status')
                    ->boolean()
                    ->trueLabel('Approved only')
                    ->falseLabel('Pending only')
                    ->native(false),

                TernaryFilter::make('is_verified_purchase')
                    ->label('Verified Purchase')
                    ->boolean()
                    ->trueLabel('Verified only')
                    ->falseLabel('Unverified only')
                    ->native(false),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn($record) => $record->update(['is_approved' => true]))
                    ->visible(fn($record) => !$record->is_approved)
                    ->requiresConfirmation(),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn($record) => $record->update(['is_approved' => false]))
                    ->visible(fn($record) => $record->is_approved)
                    ->requiresConfirmation(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
