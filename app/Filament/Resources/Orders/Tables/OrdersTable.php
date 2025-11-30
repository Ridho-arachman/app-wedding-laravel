<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Actions\ViewAction as ActionsViewAction;
use Filament\Schemas\Components\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\BulkActions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("order_code")
                    ->label("Kode")
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(
                        fn(Order $record): ?string => $record->customer_name,
                    ),

                Tables\Columns\TextColumn::make("package.name")
                    ->label("Paket")
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make("total_price")
                    ->label("Total")
                    ->money("IDR", divideBy: 1)
                    ->sortable(),

                Tables\Columns\TextColumn::make("event_date")
                    ->label("Acara")
                    ->date("d M Y")
                    ->sortable(),

                // 🔥 H- dengan warna dinamis
                Tables\Columns\TextColumn::make("days_to_event")
                    ->label("H-")
                    ->formatStateUsing(
                        fn(int $state): string => $state < 0
                            ? "Selesai"
                            : "H-{$state}",
                    )
                    ->color(
                        fn(int $state): string => match (true) {
                            $state < 0 => "gray",
                            $state <= 1 => "danger",
                            $state <= 3 => "warning",
                            $state <= 7 => "info",
                            default => "success",
                        },
                    )
                    ->sortable(),

                // 🔥 Status pembayaran dengan badge
                Tables\Columns\TextColumn::make("status")
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            "draft" => "gray",
                            "dp_pending", "full_pending" => "warning",
                            "dp_paid" => "info",
                            "full_paid" => "success",
                            "completed" => "purple",
                            "cancelled" => "danger",
                            default => "gray",
                        },
                    )
                    ->sortable(),

                // 🔥 VA Bank & Nomor (jika ada)
                Tables\Columns\TextColumn::make("va_bank")
                    ->label("Bank VA")
                    ->badge()
                    ->color("primary")
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make("va_number")
                    ->label("Nomor VA")
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("status")
                    ->options([
                        "draft" => "Draft",
                        "dp_pending" => "Menunggu DP",
                        "dp_paid" => "DP Dibayar",
                        "full_pending" => "Menunggu Lunas",
                        "full_paid" => "Lunas",
                        "completed" => "Selesai",
                        "cancelled" => "Dibatalkan",
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make("upcoming")
                    ->query(
                        fn($query) => $query->whereDate(
                            "event_date",
                            ">=",
                            now(),
                        ),
                    )
                    ->label("Acara Mendatang"),

                Tables\Filters\Filter::make("needs_payment")
                    ->query(
                        fn($query) => $query
                            ->where("status", "dp_paid")
                            ->whereDate("event_date", "<=", now()->addDays(7)),
                    )
                    ->label("Butuh Pelunasan (H≤7)"),
            ])
            ->actions([
                ActionsViewAction::make(),

                ActionsAction::make("generate_va_dp")
                    ->label("🔄 VA DP")
                    ->icon("heroicon-o-credit-card")
                    ->color("info")
                    ->visible(
                        fn(Order $record): bool => $record->status === "draft",
                    )
                    ->action(fn(Order $record) => $record->generateVaForDp()),

                ActionsAction::make("check_payment")
                    ->label("🔍 Cek")
                    ->icon("heroicon-o-arrow-path")
                    ->color("warning")
                    ->visible(
                        fn(Order $record): bool => in_array($record->status, [
                            "dp_pending",
                            "full_pending",
                        ]),
                    )
                    ->action(
                        fn(Order $record) => $record->checkAndSyncStatus(),
                    ),

                ActionsAction::make("download_invoice")
                    ->label("🖨️ PDF")
                    ->icon("heroicon-o-arrow-down-tray")
                    ->color("gray")
                    ->url(fn(Order $record) => route("invoice.pdf", $record))
                    ->openUrlInNewTab(),

                ActionsEditAction::make(),
                ActionsDeleteAction::make(),
            ])
            ->bulkActions([ActionsDeleteBulkAction::make()]);
    }
}
