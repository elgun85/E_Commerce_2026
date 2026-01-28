<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Customer Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->unique()
                            ->email()
                            ->required(),
                        DateTimePicker::make('email_verified_at'),

                        DatePicker::make('date_of_birth')
                            ->native()
                            ->displayFormat('M d, Y'),
                        TextInput::make('phone')
                            ->tel()
                            ->default(null),
                        Select::make('gender')
                            ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'])
                            ->native(false)
                            ->default(null),
                        Toggle::make('is_active')
                            ->required(),
                    ]),

                Section::make('Password Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)  //əgər password yazılıbsa hash et
                            ->dehydrated(fn($state) => filled($state))   // “password yalnız dolu olarsa DB-yə göndərilsin
                            ->required(fn(string $operation) => $operation === 'create') //yalnız yaradılarkən tələb olunsun
                            ->revealable()
                           ,

                        TextInput::make('password_confirmation')
                            ->password()
                            ->same('password') //“password” sahəsi ilə eyni olmalıdır
                            ->revealable() //göz simvolu ilə şifrəni göstərmək imkanı
                            ->dehydrated(false) //DB-yə göndərilməsin
                            ->required(fn(string $operation) => $operation === 'create'), //yalnız yaradılarkən tələb olunsun
                    ]),



            ]);
    }
}
