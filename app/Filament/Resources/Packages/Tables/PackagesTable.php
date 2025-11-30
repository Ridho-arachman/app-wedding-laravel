<?php

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Storage;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make("image")
                    ->label("Gambar")
                    ->disk("public") // ← wajib, agar pakai public disk
                    ->visibility("public") // ← wajib, agar tidak generate signed URL
                    ->imageSize(40) // ✅ valid di v4.x (sesuai dokumentasi)
                    ->circular(),

                TextColumn::make("name")
                    ->label("Nama Paket")
                    ->searchable()
                    ->sortable(),

                TextColumn::make("description")
                    ->label("Deskripsi")
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make("price")
                    ->label("Harga")
                    ->money("IDR", locale: "id")
                    ->sortable(),

                IconColumn::make("is_active")
                    ->label("Status")
                    ->boolean()
                    ->sortable(),

                TextColumn::make("created_at")
                    ->label("Dibuat")
                    ->dateTime("d M Y")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\TernaryFilter::make("is_active")
                    ->label("Status Aktif")
                    ->boolean(),
            ])

            ->recordActions([EditAction::make()])

            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
