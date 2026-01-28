<?php

namespace App\Filament\Resources\Coupens\Pages;

use App\Filament\Resources\Coupens\CoupenResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupen extends CreateRecord
{
    protected static string $resource = CoupenResource::class;
        protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
