<?php

namespace App\Filament\Admin\Resources\Packages\Pages;

use App\Filament\Admin\Resources\Packages\PackageResource;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPackage extends EditRecord
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label("Hapus")
                ->icon("heroicon-o-trash")
                ->requiresConfirmation()
                ->modalHeading("Hapus Paket")
                ->modalDescription(
                    "Apakah Anda yakin ingin menghapus Paket ini? Tindakan ini tidak dapat dibatalkan.",
                )
                ->modalSubmitActionLabel("Ya, hapus"),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        // Kembali ke halaman index setelah update
        return $this->getResource()::getUrl("index");
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title("Paket Diperbarui")
            ->body("Paket  berhasil disimpan.")
            ->send();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title("Gagal Memperbarui")
                ->body($e->getMessage())
                ->send();

            throw $e;
        }
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label("Perbarui Paket")
            ->icon("heroicon-o-check")
            ->color("success");
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label("Kembali");
    }
}
