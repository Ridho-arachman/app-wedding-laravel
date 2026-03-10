<?php

namespace App\Filament\Admin\Resources\Items\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use App\Models\Item;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\QueryException;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("row_number")
                    ->label("No")
                    ->getStateUsing(function ($record, $rowLoop) {
                        return $rowLoop->iteration;
                    }),

                ImageColumn::make("image")
                    ->label("Gambar")
                    ->square()
                    ->imageSize(40)
                    ->defaultImageUrl(url("storage/items/item.png")),

                TextColumn::make("code")
                    ->label("Kode")
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage("Kode tersalin"),

                TextColumn::make("name")
                    ->label("Nama")
                    ->searchable()
                    ->sortable(),

                TextColumn::make("category")->label("Kategori")->badge()->color(
                    fn(string $state): string => match ($state) {
                        "tenda" => "info",
                        "rias" => "success",
                        "dekorasi" => "warning",
                        "kursi" => "gray",
                        "piring" => "primary",
                        "catering" => "danger",
                        default => "secondary",
                    },
                ),

                TextColumn::make("stock")
                    ->label("Stok")
                    ->numeric()
                    ->sortable()
                    ->color(
                        fn(Item $record): ?string => $record->stock <
                        $record->min_stock
                            ? "danger"
                            : null,
                    )
                    ->description(
                        fn(Item $record): string => "Min: " .
                            $record->min_stock,
                    ),

                TextColumn::make("unit")
                    ->label("Satuan")
                    ->badge()
                    ->color("gray"),

                IconColumn::make("is_active")->label("Aktif")->boolean(),

                TextColumn::make("created_at")
                    ->label("Dibuat")
                    ->dateTime("d M Y")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make("category")
                    ->label("Kategori")
                    ->options([
                        "tenda" => "Tenda",
                        "rias" => "Rias",
                        "dekorasi" => "Dekorasi",
                        "kursi" => "Kursi",
                        "piring" => "Piring & Alat Makan",
                        "catering" => "Catering",
                        "lainnya" => "Lainnya",
                    ]),

                TernaryFilter::make("is_active")
                    ->label("Status Aktif")
                    ->placeholder("Semua")
                    ->trueLabel("Aktif")
                    ->falseLabel("Tidak Aktif"),

                \Filament\Tables\Filters\Filter::make("low_stock")
                    ->label("Stok Menipis")
                    ->query(
                        fn($query) => $query->whereColumn(
                            "stock",
                            "<",
                            "min_stock",
                        ),
                    )
                    ->toggle(),
            ])
            ->recordActions([
                ActionsEditAction::make(),
                DeleteAction::make()
                    ->label("Hapus")
                    ->successNotification(null)
                    ->modalDescription(
                        "Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.",
                    )
                    ->modalSubmitActionLabel("Hapus")
                    ->modalCancelActionLabel("Batal")
                    ->action(function ($record) {
                        try {
                            $record->delete();
                            Notification::make()
                                ->success()
                                ->title("Berhasil")
                                ->body("Item {$record->name} berhasil dihapus.")
                                ->send();
                        } catch (QueryException $e) {
                            // Kode error 1451 = foreign key constraint violation
                            if ($e->errorInfo[1] == 1451) {
                                Notification::make()
                                    ->danger()
                                    ->title("Tidak Dapat Menghapus")
                                    ->body(
                                        "Item '{$record->name}' masih digunakan di pesanan. Hapus atau ubah pesanan terlebih dahulu.",
                                    )
                                    ->send();
                            } else {
                                // Jika error lain, lempar ulang atau tampilkan pesan umum
                                Notification::make()
                                    ->danger()
                                    ->title("Gagal Menghapus")
                                    ->body(
                                        "Terjadi kesalahan: " .
                                            $e->getMessage(),
                                    )
                                    ->send();
                            }
                        }
                    }),
            ])
            ->toolbarActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make()
                        ->label("Hapus Barang Terpilih")
                        ->successNotification(null)
                        ->modalDescription(
                            "Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.",
                        )
                        ->modalSubmitActionLabel("Hapus")
                        ->modalCancelActionLabel("Batal")
                        ->action(function ($records) {
                            $failedRecords = [];
                            foreach ($records as $record) {
                                try {
                                    $record->delete();
                                } catch (QueryException $e) {
                                    if ($e->errorInfo[1] == 1451) {
                                        $failedRecords[] = $record->name;
                                    } else {
                                        throw $e;
                                    }
                                }
                            }
                            if (count($failedRecords) > 0) {
                                $names = implode(", ", $failedRecords);
                                Notification::make()
                                    ->danger()
                                    ->title("Sebagian Gagal Dihapus")
                                    ->body(
                                        "Item berikut masih digunakan di pesanan: {$names}",
                                    )
                                    ->send();
                            } else {
                                Notification::make()
                                    ->success()
                                    ->title("Berhasil")
                                    ->body(
                                        "Semua item yang dipilih berhasil dihapus.",
                                    )
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
