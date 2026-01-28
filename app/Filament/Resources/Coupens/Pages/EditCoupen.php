<?php

namespace App\Filament\Resources\Coupens\Pages;

use App\Filament\Resources\Coupens\CoupenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoupen extends EditRecord
{
    protected static string $resource = CoupenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

        protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
