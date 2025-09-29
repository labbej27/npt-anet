<?php

namespace App\Filament\Resources\WorkshopSessionResource\Pages;

use App\Filament\Resources\WorkshopSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkshopSession extends EditRecord
{
    protected static string $resource = WorkshopSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
