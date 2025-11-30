<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Informasi Utama")
                ->schema([
                    Forms\Components\TextInput::make("name")
                        ->label("Nama Item")
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make("code")
                        ->label("Kode Item")
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Textarea::make("description")
                        ->label("Deskripsi")
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make("Detail Item")
                ->schema([
                    Forms\Components\TextInput::make("stock")
                        ->label("Stok")
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    Forms\Components\Select::make("condition")
                        ->label("Kondisi")
                        ->options([
                            "baru" => "Baru",
                            "bekas" => "Bekas",
                            "perlu_perbaikan" => "Perlu Perbaikan",
                        ])
                        ->required(),

                    Forms\Components\Select::make("category")
                        ->label("Kategori")
                        ->options([
                            "meja" => "Meja",
                            "kursi" => "Kursi",
                            "sound" => "Sound",
                            "lighting" => "Lighting",
                        ])
                        ->required(),

                    Forms\Components\TextInput::make("buy_price")
                        ->label("Harga Beli")
                        ->numeric()
                        ->prefix("IDR")
                        ->minValue(0)
                        ->nullable(),

                    Forms\Components\DatePicker::make("acquired_at")
                        ->label("Tanggal Pembelian")
                        ->nullable(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
