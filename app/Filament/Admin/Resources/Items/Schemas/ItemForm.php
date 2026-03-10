<?php

namespace App\Filament\Admin\Resources\Items\Schemas;

use App\Models\Item;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Informasi Dasar")->schema([
                Grid::make(2)->schema([
                    TextInput::make("code")
                        ->label("Kode Item")
                        ->default(function () {
                            $lastItem = Item::orderByRaw(
                                "CAST(SUBSTRING(code, 5) AS UNSIGNED) DESC",
                            )->first();
                            if ($lastItem) {
                                // Ambil angka setelah "BRG-"
                                $lastNumber = intval(
                                    substr($lastItem->code, 4),
                                );
                                $newNumber = $lastNumber + 1;
                            } else {
                                $newNumber = 1;
                            }
                            // Format dengan leading zero (3 digit)
                            return "BRG-" .
                                str_pad($newNumber, 3, "0", STR_PAD_LEFT);
                        })
                        ->disabled() // User tidak bisa mengedit
                        ->dehydrated(true) // Tetap dikirim ke database
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->placeholder("Otomatis digenerate"),
                    TextInput::make("name")
                        ->label("Nama Item")
                        ->required()
                        ->maxLength(255),
                ]),
                Textarea::make("description")
                    ->label("Deskripsi")
                    ->rows(3)
                    ->columnSpanFull(),
                Grid::make(2)->schema([
                    Select::make("category")
                        ->label("Kategori")
                        ->options([
                            "tenda" => "Tenda",
                            "rias" => "Rias",
                            "dekorasi" => "Dekorasi",
                            "kursi" => "Kursi",
                            "piring" => "Piring & Alat Makan",
                            "catering" => "Catering",
                            "lainnya" => "Lainnya",
                        ])
                        ->searchable()
                        ->native(false),
                    Select::make("unit")
                        ->label("Satuan")
                        ->options([
                            "buah" => "Buah",
                            "pcs" => "Pcs",
                            "set" => "Set",
                            "meter" => "Meter",
                            "lembar" => "Lembar",
                            "pack" => "Pack",
                            "dus" => "Dus",
                        ])
                        ->default("buah")
                        ->required(),
                ]),
            ]),

            Section::make("Stok dan Status")->schema([
                Grid::make(2)->schema([
                    TextInput::make("stock")
                        ->label("Stok Saat Ini")
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->required(),
                    TextInput::make("min_stock")
                        ->label("Stok Minimal")
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->required()
                        ->helperText(
                            "Peringatan akan muncul jika stok di bawah nilai ini",
                        ),
                ]),
                Grid::make(2)->schema([
                    FileUpload::make("image")
                        ->label("Gambar Item")
                        ->image()
                        ->directory("items")
                        ->visibility("public")
                        ->imageResizeMode("cover")
                        ->imageCropAspectRatio("1:1")
                        ->imageResizeTargetWidth(300)
                        ->imageResizeTargetHeight(300)
                        ->columnSpanFull(),
                    Toggle::make("is_active")
                        ->label("Aktif")
                        ->default(true)
                        ->inline(false),
                ]),
            ]),
        ]);
    }
}
