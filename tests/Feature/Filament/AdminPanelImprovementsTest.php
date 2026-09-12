<?php

declare(strict_types=1);

use App\Domain\Catalogue\Enums\CourseStatus;
use App\Domain\Catalogue\Models\Course;
use App\Domain\Leads\Enums\LeadStatus;
use App\Domain\Leads\Models\CorporateInquiry;
use App\Domain\Leads\Services\CorporateInquiryService;
use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Scheduling\Services\ScheduleStateService;
use App\Filament\Resources\CorporateInquiries\CorporateInquiryResource;
use App\Filament\Resources\CorporateInquiries\RelationManagers\NotesRelationManager;
use App\Filament\Resources\CorporateInquiries\RelationManagers\StatusChangesRelationManager;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Courses\RelationManagers\DeliveryModesRelationManager;
use App\Filament\Resources\Courses\RelationManagers\FaqsRelationManager;
use App\Filament\Resources\Courses\RelationManagers\ModulesRelationManager;
use App\Filament\Resources\Courses\RelationManagers\TrainersRelationManager;
use App\Filament\Support\SeoFormSection;
use App\Models\User;
use App\Policies\CorporateInquiryPolicy;
use App\Policies\CoursePolicy;
use App\Policies\CourseSchedulePolicy;
use App\Policies\TestimonialPolicy;
use App\Policies\UserPolicy;
use Database\Seeders\RoleSeeder;
use Filament\Schemas\Components\Section;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->admin = User::factory()->create([
        'email' => 'panel-admin@example.test',
        'is_active' => true,
    ]);
    $this->admin->assignRole('super-admin');
});

it('exposes SeoFormSection for morph SEO editors', function () {
    expect(class_exists(SeoFormSection::class))->toBeTrue()
        ->and(method_exists(SeoFormSection::class, 'make'))->toBeTrue();

    $section = SeoFormSection::make();
    expect($section)->toBeInstanceOf(Section::class);
});

it('wires course and inquiry relation managers', function () {
    expect(CourseResource::getRelations())->toBe([
        ModulesRelationManager::class,
        FaqsRelationManager::class,
        DeliveryModesRelationManager::class,
        TrainersRelationManager::class,
    ]);

    expect(CorporateInquiryResource::getRelations())->toBe([
        NotesRelationManager::class,
        StatusChangesRelationManager::class,
    ]);
});

it('does not register FilamentInfoWidget on the admin panel', function () {
    $source = File::get(app_path('Providers/Filament/AdminPanelProvider.php'));

    expect($source)->not->toContain('FilamentInfoWidget')
        ->and($source)->toContain('AccountWidget::class');

    // Class still exists in Filament; we just must not reference it.
    expect(class_exists(FilamentInfoWidget::class))->toBeTrue();
});

it('publishes a scheduled session via ScheduleStateService', function () {
    $schedule = CourseSchedule::factory()->create([
        'status' => ScheduleStatus::Scheduled,
    ]);

    $updated = app(ScheduleStateService::class)->publish($schedule);

    expect($updated->status)->toBe(ScheduleStatus::Open);
});

it('assigns a corporate inquiry via CorporateInquiryService', function () {
    $inquiry = CorporateInquiry::create([
        'company_name' => 'Assign Me BV',
        'contact_name' => 'Lead Contact',
        'email' => 'lead@assign.example',
        'participants' => 6,
        'status' => LeadStatus::New,
    ]);

    $assignee = User::factory()->create(['is_active' => true]);
    $assignee->assignRole('sales-manager');

    $updated = app(CorporateInquiryService::class)->assign($inquiry, $assignee, $this->admin);

    expect($updated->assigned_to)->toBe($assignee->id)
        ->and($updated->assignee->is($assignee))->toBeTrue();
});

it('authorises publish, assign, cancel, verify and assignRoles for super-admin', function () {
    expect((new CoursePolicy)->publish($this->admin))->toBeTrue()
        ->and((new CorporateInquiryPolicy)->assign($this->admin))->toBeTrue()
        ->and((new CourseSchedulePolicy)->cancel($this->admin))->toBeTrue()
        ->and((new TestimonialPolicy)->verify($this->admin))->toBeTrue()
        ->and((new UserPolicy)->assignRoles($this->admin))->toBeTrue();
});

it('mirrors restore and forceDelete to the delete permission', function () {
    expect((new CoursePolicy)->restore($this->admin))->toBeTrue()
        ->and((new CoursePolicy)->forceDelete($this->admin))->toBeTrue()
        ->and((new CorporateInquiryPolicy)->restore($this->admin))->toBeTrue()
        ->and((new CourseSchedulePolicy)->forceDelete($this->admin))->toBeTrue()
        ->and((new TestimonialPolicy)->restore($this->admin))->toBeTrue();
});

it('can publish a draft course by updating status and published_at', function () {
    $course = Course::factory()->draft()->create();

    $course->update([
        'status' => CourseStatus::Published,
        'published_at' => $course->published_at ?? now(),
    ]);

    expect($course->fresh()->status)->toBe(CourseStatus::Published)
        ->and($course->fresh()->published_at)->not->toBeNull();
});
