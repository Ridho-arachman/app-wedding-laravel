<?php

namespace App\Filament\Admin\Resources\Packages\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\QueryException;

class PackagesTable
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
                    ->defaultImageUrl(url("storage/packages/menu.png")),

                TextColumn::make("code")
                    ->label("Kode Paket")
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage("Kode paket tersalin"),

                TextColumn::make("name")
                    ->label("Nama Paket")
                    ->searchable()
                    ->sortable(),

                TextColumn::make("price")
                    ->label("Harga")
                    ->money("IDR")
                    ->sortable(),

                TextColumn::make("items_count")
                    ->label("Jumlah Item")
                    ->counts("items")
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make("is_active")
                    ->label("Aktif")
                    ->boolean()
                    ->sortable(),

                TextColumn::make("created_at")
                    ->label("Dibuat")
                    ->dateTime("d M Y")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make("is_active")
                    ->label("Status Aktif")
                    ->placeholder("Semua")
                    ->trueLabel("Aktif")
                    ->falseLabel("Tidak Aktif"),
            ])
            ->recordActions([
                ActionsEditAction::make(),
                DeleteAction::make()
                    ->label("Hapus")
                    ->successNotification(null)
                    ->modalDescription(
                        "Apakah Anda yakin ingin menghapus paket ini? Tindakan ini tidak dapat dibatalkan.",
                    )
                    ->modalSubmitActionLabel("Hapus")
                    ->modalCancelActionLabel("Batal")
                    ->action(function ($record) {
                        try {
                            $record->delete();
                            Notification::make()
                                ->success()
                                ->title("Berhasil")
                                ->body(
                                    "paket {$record->name} berhasil dihapus.",
                                )
                                ->send();
                        } catch (QueryException $e) {
                            // Kode error 1451 = foreign key constraint violation
                            if ($e->errorInfo[1] == 1451) {
                                Notification::make()
                                    ->danger()
                                    ->title("Tidak Dapat Menghapus")
                                    ->body(
                                        "paket '{$record->name}' masih digunakan di pesanan. Hapus atau ubah pesanan terlebih dahulu.",
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
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label("Hapus Barang Terpilih")
                        ->successNotification(null)
                        ->modalDescription(
                            "Apakah Anda yakin ingin menghapus paket ini? Tindakan ini tidak dapat dibatalkan.",
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
                                        "paket berikut masih digunakan di pesanan: {$names}",
                                    )
                                    ->send();
                            } else {
                                Notification::make()
                                    ->success()
                                    ->title("Berhasil")
                                    ->body(
                                        "Semua paket yang dipilih berhasil dihapus.",
                                    )
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading("Belum ada paket")
            ->emptyStateDescription(
                'Buat paket pertama Anda dengan menekan tombol "Buat Paket" di pojok kanan atas.',
            )
            ->defaultSort("created_at", "desc");
    }
}
