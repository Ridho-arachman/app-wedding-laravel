<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Detail Menu")
                ->schema([
                    Forms\Components\TextInput::make("name")
                        ->label("Nama Menu")
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make("description")
                        ->label("Deskripsi")
                        ->maxLength(65535)
                        ->columnSpanFull(),

                    Forms\Components\Select::make("category")
                        ->label("Kategori")
                        ->options([
                            "food" => "Makanan",
                            "drink" => "Minuman",
                            "dessert" => "Dessert",
                        ])
                        ->default("food")
                        ->required(),

                    Forms\Components\TextInput::make("price")
                        ->label("Harga")
                        ->numeric()
                        ->prefix("Rp")
                        ->required()
                        ->minValue(0),

                    Forms\Components\FileUpload::make("image")
                        ->label("Foto")
                        ->image()
                        ->directory("menus")
                        ->visibility("public")
                        ->preserveFilenames()
                        ->maxSize(2048), // maks 2MB

                    Forms\Components\Toggle::make("is_active")
                        ->label("Aktif")
                        ->default(true)
                        ->inline(false),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
