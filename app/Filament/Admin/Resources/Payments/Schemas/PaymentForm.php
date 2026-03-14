<?php

namespace App\Filament\Admin\Resources\Payments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->required(),
                Select::make('type')
                    ->options(['dp' => 'Dp', 'installment' => 'Installment', 'final' => 'Final'])
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('payment_date'),
                Select::make('method')
                    ->options(['cash' => 'Cash', 'transfer' => 'Transfer', 'midtrans' => 'Midtrans'])
                    ->required(),
                TextInput::make('proof'),
                TextInput::make('midtrans_order_id'),
                TextInput::make('midtrans_status'),
                TextInput::make('midtrans_response'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
