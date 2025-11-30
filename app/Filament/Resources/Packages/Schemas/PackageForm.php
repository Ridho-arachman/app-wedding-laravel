<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Informasi Paket")
                ->schema([
                    Forms\Components\TextInput::make("name")
                        ->label("Nama Paket")
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make("description")
                        ->label("Deskripsi")
                        ->nullable()
                        ->maxLength(65535)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make("price")
                        ->label("Harga (dalam satuan angka, misal: 150000)")
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->suffix("IDR"),

                    Forms\Components\FileUpload::make("image")
                        ->label("Gambar Paket")
                        ->nullable()
                        ->disk("public") // pastikan sudah diatur di config/filesystems.php
                        ->directory("packages/images")
                        ->image()
                        ->imageEditor()
                        ->maxSize(2048), // max 2MB

                    Forms\Components\Toggle::make("is_active")
                        ->label("Aktif")
                        ->default(true)
                        ->inline(false),
                ])
                ->columnSpanFull(),
        ]);
    }
}
