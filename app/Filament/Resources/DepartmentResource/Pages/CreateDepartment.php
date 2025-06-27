<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;

    //=====================================================================
    //to change alert title after create done
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('Department created successfully.');
    }
    //=====================================================================
    //to add extra details to the notification after create done
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title($this->getCreatedNotificationTitle())
            ->success()
            ->body(__('The department has been created successfully.'));
    }
    //=====================================================================

}
