<?php

namespace App\Filament\Admin\Resources\Items\Pages;

use App\Filament\Admin\Resources\Items\ItemResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Filament\Actions\Action;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label("Hapus")
                ->icon("heroicon-o-trash")
                ->requiresConfirmation()
                ->modalHeading("Hapus Item")
                ->modalDescription(
                    "Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.",
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
            ->title("Item Diperbarui")
            ->body("Item  berhasil disimpan.")
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
            ->label("Perbarui Item")
            ->icon("heroicon-o-check")
            ->color("success");
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label("Kembali");
    }
}
