<?php

namespace App\Filament\Resources\Items\Tables;

use App\Models\Item;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("code")
                    ->label("Kode")
                    ->searchable()
                    ->sortable()
                    ->description(fn(Item $record): string => $record->name),

                Tables\Columns\TextColumn::make("category")
                    ->label("Kategori")
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            "meja" => "info",
                            "kursi" => "success",
                            "sound" => "warning",
                            "lighting" => "purple",
                            "dekorasi" => "pink",
                            default => "gray",
                        },
                    ),

                Tables\Columns\TextColumn::make("stock")
                    ->label("Stok")
                    ->color(
                        fn(int $state): string => $state < 5
                            ? "danger"
                            : "success",
                    )
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make("condition")
                    ->label("Kondisi")
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            "baru" => "success",
                            "bekas" => "warning",
                            "perlu_perbaikan" => "danger",
                            default => "gray",
                        },
                    ),

                Tables\Columns\TextColumn::make("buy_price")
                    ->label("Harga Beli")
                    ->money("IDR", divideBy: 1)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("category")->options([
                    "meja" => "Meja",
                    "kursi" => "Kursi",
                    "sound" => "Sound System",
                    "lighting" => "Lighting",
                    "dekorasi" => "Dekorasi",
                ]),

                Tables\Filters\SelectFilter::make("condition")->options([
                    "baru" => "Baru",
                    "bekas" => "Bekas",
                    "perlu_perbaikan" => "Perlu Perbaikan",
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }
}
