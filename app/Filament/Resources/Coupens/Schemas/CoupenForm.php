<?php

namespace App\Filament\Resources\Coupens\Schemas;

use Dom\Text;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;

class CoupenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupen Details')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')->label('Coupen Code')
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(callable $set, $state): mixed => $set('code', strtoupper($state)))
                            ->required(),

                        Select::make('type')->label('Coupen Type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed',
                            ])
                            ->live()
                            ->default('percentage')
                            ->required(),
                        TextInput::make('value')->label('Coupen Value')
                            ->numeric()
                            ->minValue(0)
                            ->prefix(fn(callable $get) => $get('type') === 'fixed' ? '$' : null)  //eger type fixed olarsa $ prefix elave et
                            ->suffix(fn(callable $get) => $get('type') === 'percentage' ? '%' : null) //eger type percentage olarsa % suffix elave et
                            ->required(),

                        Toggle::make('is_active')->label('Active')->required()

                    ]),

                Section::make('Condotions & Limits')
                    ->schema([
                        TextInput::make('minimum_order_value')
                            ->prefix('$')
                            ->minValue(0)
                            ->numeric()
                            ->default(null),
                        TextInput::make('maximum_discount')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->visible(fn(callable $get) => $get('type') === 'percentage')
                            ->default(null),
                        TextInput::make('usage_limit')
                            ->minValue(1)
                            ->numeric()
                            ->default(null),
                        TextInput::make('usage_limit_per_customer')
                        ->label('Usage')
                            ->numeric()
                            ->minValue(1)
                            ->default(null),
                    ]),

                Section::make('Validity Period')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->native(false)
                            ->helperText('When the coupon becomes active '),
                        DateTimePicker::make('expires_at')
                            ->native(false),
                    ])
            ]);
    }
}
