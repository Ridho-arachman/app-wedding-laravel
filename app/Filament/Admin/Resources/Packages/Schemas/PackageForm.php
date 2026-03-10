<?php

namespace App\Filament\Admin\Resources\Packages\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\Item;
use App\Models\Package;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Informasi Paket")->schema([
                Grid::make(2)->schema([
                    TextInput::make("code")
                        ->label("Kode Item")
                        ->default(function () {
                            $lastItem = Package::orderByRaw(
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
                            return "PKG-" .
                                str_pad($newNumber, 3, "0", STR_PAD_LEFT);
                        })
                        ->disabled() // User tidak bisa mengedit
                        ->dehydrated(true) // Tetap dikirim ke database
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->placeholder("Otomatis digenerate")
                        ->helperText(
                            "Contoh: PKG-001. Akan otomatis dibuat jika dikosongkan.",
                        ),
                    TextInput::make("name")
                        ->label("Nama Paket")
                        ->required()
                        ->maxLength(255),
                ]),
                Textarea::make("description")
                    ->label("Deskripsi")
                    ->rows(3)
                    ->columnSpanFull(),
                Grid::make(2)->schema([
                    TextInput::make("price")
                        ->label("Harga Paket")
                        ->required()
                        ->numeric()
                        ->prefix("Rp")
                        ->minValue(0)
                        ->default(0),
                    Toggle::make("is_active")
                        ->label("Aktif")
                        ->default(true)
                        ->inline(false),
                ]),
                FileUpload::make("image")
                    ->label("Foto Paket")
                    ->image()
                    ->directory("packages")
                    ->visibility("public")
                    ->imageEditor()
                    ->columnSpanFull(),
            ]),

            Section::make("Item dalam Paket")->schema([
                Repeater::make("packageItems")
                    ->relationship()
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make("item_code")
                                ->label("Barang")
                                ->options(Item::pluck("name", "code"))
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(
                                    fn($state, $set) => $set(
                                        "unit",
                                        Item::find($state)?->unit,
                                    ),
                                ),
                            TextInput::make("quantity")
                                ->label("Jumlah")
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1),
                            TextInput::make("unit")
                                ->label("Satuan")
                                ->placeholder("Otomatis dari barang")
                                ->helperText(
                                    "Kosongkan jika menggunakan satuan default",
                                ),
                        ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->addActionLabel("+ Tambah Item")
                    ->reorderable()
                    ->collapsible()
                    ->cloneable()
                    ->itemLabel(
                        fn(array $state): ?string => $state["item_code"] ??
                            "Item baru",
                    ),
            ]),
        ]);
    }
}
