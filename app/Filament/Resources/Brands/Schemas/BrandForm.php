<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),

                        TextInput::make('website')
                          //->url()
                            ->default(null)
                          ->placeholder('https://example.com')
                            ,
                        TextInput::make('slug')
                            ->visibleOn('Edit')
                            ->readOnly()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Textarea::make('description')
                            ->rows(3)
                            ->default(null),
                        FileUpload::make('logo')
                            ->image()
                            ->disk('public')
                            ->directory('brands')
                            ->maxSize(2024)
                            ->imageEditor()
                            ->default(null),

                    ])->columnSpanFull(),


                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->required(),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columnSpanFull(),
            ]);
    }
}
