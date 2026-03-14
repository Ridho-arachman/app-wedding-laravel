<?php

namespace App\Filament\Admin\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
// use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id")
                    ->label("ID")
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("order.order_number")
                    ->label("No. Order")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("type")->badge()->color(
                    fn(string $state): string => match ($state) {
                        "dp" => "warning",
                        "installment" => "info",
                        "final" => "success",
                    },
                ),
                TextColumn::make("amount")->money("IDR")->sortable(),
                TextColumn::make("payment_date")->date("d M Y")->sortable(),
                TextColumn::make("method")->badge()->color(
                    fn(string $state): string => match ($state) {
                        "cash" => "success",
                        "transfer" => "warning",
                        "midtrans" => "info",
                    },
                ),
                IconColumn::make("proof")
                    ->label("Bukti")
                    ->icon(
                        fn($state): string => $state
                            ? "heroicon-o-document"
                            : "heroicon-o-x-mark",
                    )
                    ->color(fn($state): string => $state ? "success" : "danger")
                    ->url(
                        fn($record) => $record->proof
                            ? asset("storage/" . $record->proof)
                            : null,
                    )
                    ->openUrlInNewTab(),
                TextColumn::make("midtrans_order_id")
                    ->label("Midtrans Order ID")
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("midtrans_status")
                    ->label("Status Midtrans")
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            "pending" => "warning",
                            "settlement" => "success",
                            "expire", "deny" => "danger",
                            default => "secondary",
                        },
                    ),
                TextColumn::make("notes")
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("created_at")
                    ->dateTime("d M Y H:i")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("updated_at")
                    ->dateTime("d M Y H:i")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make("method")->options([
                    "cash" => "Tunai",
                    "transfer" => "Transfer",
                    "midtrans" => "Midtrans",
                ]),
                SelectFilter::make("type")->options([
                    "dp" => "DP",
                    "installment" => "Cicilan",
                    "final" => "Lunas",
                ]),
                SelectFilter::make("midtrans_status")
                    ->label("Status Midtrans")
                    ->options([
                        "pending" => "Pending",
                        "settlement" => "Settlement",
                        "expire" => "Expire",
                        "deny" => "Deny",
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading("Detail Pembayaran")
                    ->modalWidth("lg")
                    ->infolist([
                        Section::make("Informasi Pembayaran")->schema([
                            TextEntry::make("order.order_number")->label(
                                "No. Order",
                            ),
                            TextEntry::make("type")->badge(),
                            TextEntry::make("amount")->money("IDR"),
                            TextEntry::make("payment_date")->date("d M Y"),
                            TextEntry::make("method")->badge(),
                            TextEntry::make("proof")
                                ->url(
                                    fn($record) => $record->proof
                                        ? asset("storage/" . $record->proof)
                                        : null,
                                )
                                ->openUrlInNewTab()
                                ->visible(
                                    fn($record) => $record->method === "cash" &&
                                        $record->proof,
                                ),
                            TextEntry::make("midtrans_order_id")->label(
                                "Midtrans Order ID",
                            ),
                            TextEntry::make("midtrans_status")->badge(),
                            TextEntry::make("notes"),
                        ]),
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
