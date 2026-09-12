<?php

namespace App\Filament\Resources\CorporateInquiries\Pages;

use App\Domain\Leads\Enums\LeadStatus;
use App\Domain\Leads\Services\CorporateInquiryService;
use App\Filament\Resources\CorporateInquiries\CorporateInquiryResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class EditCorporateInquiry extends EditRecord
{
    protected static string $resource = CorporateInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('assign')
                ->label('Assign')
                ->icon('heroicon-o-user-plus')
                ->authorize('assign')
                ->form([
                    Select::make('assigned_to')
                        ->label('Staff member')
                        ->options(fn (): array => User::query()->staff()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data, CorporateInquiryService $service): void {
                    $assignee = User::query()->findOrFail($data['assigned_to']);
                    $service->assign($this->getRecord(), $assignee, Auth::user());

                    Notification::make()
                        ->title('Inquiry assigned')
                        ->success()
                        ->send();
                }),

            Action::make('transition')
                ->label('Change status')
                ->icon('heroicon-o-arrow-path')
                ->authorize('update')
                ->form([
                    Select::make('status')
                        ->label('New status')
                        ->options(LeadStatus::class)
                        ->required()
                        ->live(),
                    Textarea::make('note')
                        ->rows(3),
                    TextInput::make('won_value_cents')
                        ->label('Won value (cents)')
                        ->numeric()
                        ->visible(fn (callable $get): bool => $get('status') === LeadStatus::Won->value || $get('status') === LeadStatus::Won),
                    TextInput::make('lost_reason')
                        ->visible(fn (callable $get): bool => $get('status') === LeadStatus::Lost->value || $get('status') === LeadStatus::Lost),
                ])
                ->action(function (array $data, CorporateInquiryService $service): void {
                    $to = $data['status'] instanceof LeadStatus
                        ? $data['status']
                        : LeadStatus::from((string) $data['status']);

                    try {
                        $service->transitionTo(
                            $this->getRecord(),
                            $to,
                            Auth::user(),
                            $data['note'] ?? null,
                            isset($data['won_value_cents']) ? (int) $data['won_value_cents'] : null,
                            $data['lost_reason'] ?? null,
                        );
                    } catch (InvalidArgumentException $exception) {
                        Notification::make()
                            ->title('Transition failed')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Status updated')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
