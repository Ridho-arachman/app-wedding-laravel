<?php

namespace App\Filament\Admin\Resources\Packages\Pages;

use App\Filament\Admin\Resources\Packages\PackageResource;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePackage extends CreateRecord
{
    protected static string $resource = PackageResource::class;
    protected static ?string $title = "Tambah Paket Baru";
    protected function getRedirectUrl(): string
    {
        // Kembali ke halaman index (daftar Menu)
        return $this->getResource()::getUrl("index");
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title("Paket Ditambahkan")
            ->body("Paket berhasil dibuat.")
            ->send();
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title("Gagal Menyimpan")
                ->body($e->getMessage())
                ->send();

            // Lempar ulang exception agar proses berhenti dan tidak redirect
            throw $e;
        }
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label("Buat Paket Baru")
            ->icon("heroicon-o-plus")
            ->color("success");
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label("Kembali");
    }

    protected function getFormActions(): array
    {
        return [$this->getCreateFormAction(), $this->getCancelFormAction()];
    }
}
