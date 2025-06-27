<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    //=====================================================================
    //to add extra details to the notification after create done
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title(__('Department updated successfully.'))
            ->success()
            ->body(__('The department has been updated successfully.'));
    }
    //=====================================================================
}
