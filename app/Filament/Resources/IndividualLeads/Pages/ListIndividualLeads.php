<?php

namespace App\Filament\Resources\IndividualLeads\Pages;

use App\Filament\Resources\IndividualLeads\IndividualLeadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIndividualLeads extends ListRecords
{
    protected static string $resource = IndividualLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
