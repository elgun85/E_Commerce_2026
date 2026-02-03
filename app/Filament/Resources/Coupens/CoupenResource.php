<?php

namespace App\Filament\Resources\Coupens;

use App\Filament\Resources\Coupens\Pages\CreateCoupen;
use App\Filament\Resources\Coupens\Pages\EditCoupen;
use App\Filament\Resources\Coupens\Pages\ListCoupens;
use App\Filament\Resources\Coupens\Schemas\CoupenForm;
use App\Filament\Resources\Coupens\Tables\CoupensTable;
use App\Models\Coupon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CoupenResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::Ticket;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return CoupenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoupensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupens::route('/'),
            'create' => CreateCoupen::route('/create'),
            'edit' => EditCoupen::route('/{record}/edit'),
        ];
    }
}
