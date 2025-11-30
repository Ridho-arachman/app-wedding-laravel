<?php

namespace App\Filament\Resources\Menus\Tables;

use App\Models\Menu;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")
                    ->label("Foto")
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make("name")
                    ->label("Nama")
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                Tables\Columns\TextColumn::make("category")
                    ->label("Kategori")
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            "food" => "success",
                            "drink" => "info",
                            "dessert" => "warning",
                            default => "gray",
                        },
                    ),

                Tables\Columns\TextColumn::make("price")
                    ->label("Harga")
                    ->money("IDR", divideBy: 1)
                    ->weight(FontWeight::Bold),

                Tables\Columns\IconColumn::make("is_active")
                    ->label("Status")
                    ->boolean()
                    ->trueIcon("heroicon-o-check-circle")
                    ->falseIcon("heroicon-o-x-circle")
                    ->trueColor("success")
                    ->falseColor("danger"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("category")
                    ->label("Kategori")
                    ->options([
                        "food" => "Makanan",
                        "drink" => "Minuman",
                        "dessert" => "Dessert",
                    ]),

                Tables\Filters\TernaryFilter::make("is_active")
                    ->label("Status")
                    ->trueLabel("Aktif")
                    ->falseLabel("Non-Aktif")
                    ->placeholder("Semua"),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }
}
