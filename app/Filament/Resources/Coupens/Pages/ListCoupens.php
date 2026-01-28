<?php

namespace App\Filament\Resources\Coupens\Pages;

use App\Filament\Resources\Coupens\CoupenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoupens extends ListRecords
{
    protected static string $resource = CoupenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
