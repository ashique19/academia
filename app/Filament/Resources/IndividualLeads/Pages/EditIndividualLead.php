<?php

namespace App\Filament\Resources\IndividualLeads\Pages;

use App\Filament\Resources\IndividualLeads\IndividualLeadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditIndividualLead extends EditRecord
{
    protected static string $resource = IndividualLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
