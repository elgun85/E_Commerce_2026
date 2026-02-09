<?php

namespace App\Filament\Resources\Products\Schemas;

use Dom\Text;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\SelectColumn;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Product Details')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->columns(2)
                            ->icon(Heroicon::InformationCircle)
                            ->schema([
                                Section::make('General Information')

                                    ->schema([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('slug')
                                            ->unique(ignoreRecord: true)
                                            ->visible(fn(string $operation): bool => $operation === 'edit')
                                            ->required(),

                                        Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required(),
                                                TextInput::make('slug')
                                                    ->unique(ignoreRecord: true)
                                                    ->readOnly()
                                                    ->required()->visibleOn('edit'),
                                            ]),
                                        Select::make('brand_id')
                                            ->relationship('brand', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->default(null)
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required(),

                                                TextInput::make('slug')
                                                    ->visibleOn('Edit')
                                                    ->readOnly()
                                                    ->unique(ignoreRecord: true)
                                                    ->required(),
                                            ]),
                                    ]),

                                Section::make('Description Information')
                                    ->schema([
                                        Textarea::make('short_description')
                                            ->default(null)
                                            ->columnSpanFull(),
                                        RichEditor::make('description')
                                            ->default(null)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Price & Inventory')
                            ->columns(2)
                            ->icon(Heroicon::CurrencyDollar)
                            ->schema([
                                Section::make('Pricing and Stock Information')
                                    ->schema([

                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->unique(ignoreRecord: true)
                                            ->default(fn() => 'SKU-' . strtoupper(Str::random(8)))
                                            ->helperText('Stock Keeping Unit')
                                            ->required(),

                                        TextInput::make('price')
                                            ->required()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->helperText('Selling Price')
                                            ->numeric()
                                            ->prefix('$'),
                                        TextInput::make('compare_price')
                                            ->numeric()
                                            ->minValue(0)
                                            ->helperText('Original Price to show discount')
                                            ->prefix('$'),
                                        TextInput::make('cost_price')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->helperText('Cost from supplier(for profit calculation)')
                                            ->prefix('$'),
                                    ]),

                                Section::make('Inventory Information')
                                    ->schema([
                                        Toggle::make('manage_stock')
                                            ->default(true)
                                            ->helperText('Enable stock management at product level')
                                            ->live(),


                                        TextInput::make('stock_quantity')
                                            ->label('Stock Quantity')
                                            ->required(fn(callable $get) => $get('manage_stock'))
                                            ->disabled(fn(callable $get) => ! $get('manage_stock'))
                                            ->numeric()
                                            ->default(0),


                                        TextInput::make('low_stock_threshold')
                                            ->label('Low Stock Threshold')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->helperText('Notify when stock is below this quantity'),

                                        Select::make('stock_status')
                                            ->options(
                                                [
                                                    'in_stock' => 'In stock',
                                                    'Out of stock' => 'Out of stock',
                                                    'on_backorder' => 'On backorder',

                                                ]
                                            )
                                            ->default('in_stock')
                                            ->native(false)
                                            // ->grouped()
                                            ->required(),

                                        TextInput::make('weight')
                                            ->label('Weight (kg)')
                                            ->minValue(0)
                                            ->helperText('User for shipping calculations')
                                            ->numeric()
                                            ->default(null),





                                    ]),
                            ]),



                        Tab::make('Images')
                            ->columnSpanFull()
                            ->icon(Heroicon::Photo)
                            ->schema([
                                Section::make('Product Images')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->image()
                                            ->disk('public')
                                            ->directory('products')
                                            ->maxSize(5120)
                                            ->multiple()
                                            ->imageEditor()
                                            ->reorderable()
                                            ->columnSpanFull()
                                            ->helperText('Upload product images')
                                            ->saveRelationshipsUsing(function ($component, $state, $record) {
                                                $record->images()->delete();
                                                foreach ($state as $index => $imagePath) {
                                                    $record->images()->create([
                                                        'image_path' => $imagePath,
                                                        'is_primary' => $index === 0,
                                                        'sort_order' => $index,
                                                    ]);
                                                }
                                            })
                                            ->dehydrated(false),


                                    ]),

                            ]),

                        Tab::make('Product Variants')
                            ->icon(Heroicon::Squares2x2)
                            ->schema([
                                Section::make('Product Variants')
                                    ->schema([
                                        Toggle::make('has_variants')
                                            ->live()
                                            ->required(),
                                    ]),

                                Section::make('Variants List')
                                    ->schema([

                                        Repeater::make('variants')
                                            ->relationship('variants')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Variant Name')
                                                    ->required()
                                                    ->placeholder('e.g., Red - Large'),

                                                KeyValue::make('options'),

                                                TextInput::make('sku')
                                                    ->label('SKU')
                                                    ->unique(ignoreRecord: true)
                                                    ->helperText('Stock keeping Unit -  unique identifier')
                                                    ->default(fn() => 'VAR-' . strtoupper(Str::random(8)))
                                                    ->required()
                                                    ->columnSpan(2),

                                                TextInput::make('price')
                                                    ->required()
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->minValue(0)
                                                    ->step(0.01),

                                                TextInput::make('compare_price')
                                                    ->label('Compare Price')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->minValue(0)
                                                    ->step(0.01),

                                                TextInput::make('stock_quantity')
                                                    ->label('Stock')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->minValue(0)
                                                    ->required(),

                                                Select::make('stock_status')
                                                    ->options([
                                                        'in_stock' => 'In Stock',
                                                        'out_of_stock' => 'Out of Stock',
                                                        'on_backorder' => 'On Backorder',
                                                    ])
                                                    ->default('in_stock')
                                                    ->required()
                                                    ->native(false),

                                                Toggle::make('is_active')
                                                    ->label('Active')
                                                    ->default(true),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                            ->addActionLabel('Add Variant'),

                                    ])
                                    ->visible(fn(callable $get) => $get('has_variants'))
                                    ->columnSpanFull()
                            ]),

                        Tab::make('Settings')
                            ->columns(2)
                            ->icon(Heroicon::Cog6Tooth)
                            ->schema([
                                Section::make('Product Status')
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->required(),
                                        Toggle::make('is_featured')
                                            ->required(),
                                    ]),

                                Section::make('Statistics')
                                    ->columns(2)
                                    ->schema([
                                        Placeholder::make('views_count')
                                            ->content(fn($record) => $record?->views_count ?? 0),

                                        Placeholder::make('created_at')
                                            ->label('Created')
                                            ->content(fn($record) => $record?->created_at?->diffForHumans() ?? '-')


                                    ]),

                            ]),


                        Tab::make('SEO')
                            ->icon(Heroicon::MagnifyingGlass)
                            ->schema([
                                Section::make('Search Engine Optimazation')
                                    ->schema([

                                        TextInput::make('meta_title')
                                            ->default(null),
                                        Textarea::make('meta_description')
                                            ->default(null)
                                            ->columnSpanFull(),

                                    ]),

                            ]),
                    ])
                    // ->columns(2)
                    ->columnSpanFull(),




            ]);
    }
}
