<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTestimonial extends EditRecord
{
    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label('Verify')
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->authorize('verify')
                ->visible(fn (): bool => ! $this->getRecord()->is_verified)
                ->requiresConfirmation()
                ->action(function (): void {
                    $testimonial = $this->getRecord();

                    if (blank($testimonial->consent_reference) && ! $testimonial->is_illustrative) {
                        Notification::make()
                            ->title('Cannot verify')
                            ->body('A consent reference is required unless the quote is marked illustrative.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $testimonial->update([
                        'is_verified' => true,
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                        'status' => $testimonial->status === 'draft' ? 'published' : $testimonial->status,
                    ]);

                    Notification::make()
                        ->title('Testimonial verified')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }
}
