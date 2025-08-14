<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use App\Models\Product;
use Illuminate\Support\Number ;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    Section::make('Order Inpformation')->schema([
                        Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        select::make('payment_method')
                        ->options([
                            'stripe' => 'Stripe',
                            'cod' => 'Cash on Delivery',
                            'paypal' => 'PayPal',
                        ])
                        ->required(),

                        select::make('payment_status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->default('pending')
                        ->required(),

                        ToggleButtons::make('status')
                        ->inline()
                        ->default('new')
                        ->required()
                        ->options([
                            'new' => 'New',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered'=> 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->colors([
                            'new' => 'info',
                            'processing' => 'warning',
                            'shipped' => 'info',
                            'delivered'=> 'success',
                            'cancelled' => 'danger',
                        ])
                        ->icons([
                            'new' => 'heroicon-m-sparkles',
                            'processing' => 'heroicon-m-arrow-path',
                            'shipped' => 'heroicon-m-truck',
                            'delivered'=> 'heroicon-m-check-badge',
                            'cancelled' => 'heroicon-m-x-circle',
                        ]),

                        select::make('currency')
                        ->options([
                            'usd' => 'USD',
                            'lbp' => 'LBP',
                            'eur' => 'EUR',
                            'egp' => 'EGP',
                        ])
                        ->default('usd')
                        ->required(),

                        select::make('shipping_method')
                        ->options([
                            'fedex' => 'FedEx',
                            'ups' => 'UPS',
                            'dhl' => 'DHL',
                            'usps' => 'USPS',
                        ])
                        ->default('fedex')
                        ->required(),
                        textarea::make('notes')
                        ->columnSpanFull(),
                    ])->columns(2),
                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                select::make('product_id')
                                ->relationship('product','name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems() // the option use aone time only
                                ->reactive()
                                ->afterStateUpdated(function (string $state,  $set) {
                                    $set('unit_amount', Product::find($state)?->price ?? 0 );
                                })
                                ->afterStateUpdated(function (string $state,  $set) {
                                    $set('total_amount', Product::find($state)?->price  ?? 0 );
                                })
                                ->columnSpan(4),
                                TextInput::make('quantity')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1)
                                ->reactive()
                                ->afterStateUpdated(function (string $state,  $set ,$get ) {
                                    $set('total_amount', $state*$get('unit_amount') );
                                })
                                ->columnSpan(2),
                                TextInput::make('unit_amount')
                                ->numeric()
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(3),
                                TextInput::make('total_amount')
                                ->numeric()
                                ->required()
                                ->dehydrated()
                                ->columnSpan(3),


                        ])->columns(12),
                        Placeholder::make('grand_total_placeholder')
                            ->label('Grand Total')
                            ->content(function( $get , $set ){
                                $total = 0;
                                if(!$repeaters = $get('items')){
                                    return $total;
                                }
                                foreach($repeaters as $key =>$repeater){ // key is amethod return the index of the repeater
                                    $total += $get("items.{$key}.total_amount");
                                }

                                $set('grand_total', $total);
                                return Number::currency($total,'USD'); // fetch  the amount as a currency

                            }),
                            Hidden::make('grand_total')
                            ->default(0)
                    ]),
                ]) ->columnSpanFull(),
            ]);
    }
}
