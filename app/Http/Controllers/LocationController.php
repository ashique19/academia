<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use Illuminate\Contracts\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('pages.locations', [
            'countries' => Country::active()
                ->withUpcomingTraining()
                ->with(['cities' => fn ($q) => $q->active()->hasUpcomingSessions()
                    ->withUpcomingSessionCount()->orderBy('name')])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function country(Country $country): View
    {
        $cities = $country->cities()->active()->hasUpcomingSessions()
            ->withUpcomingSessionCount()->orderBy('name')->get();

        abort_if($cities->isEmpty(), 404);

        return view('pages.country', compact('country', 'cities'));
    }

    /**
     * A city page renders only where a real, scheduled, bookable session
     * exists. See City::hasUpcomingSessions() — this is the doorway-page gate.
     */
    public function city(Country $country, City $city): View
    {
        abort_unless($city->country_id === $country->id && $city->is_active, 404);
        abort_unless($city->hasUpcomingSessions(), 404);

        return view('pages.city', [
            'city'     => $city->load('country', 'venues'),
            'sessions' => $city->schedules()->upcoming()->bookable()
                ->withListRelations()->orderBy('starts_at')->take(30)->get(),
            'courses'  => Course::published()->inCity($city->slug)
                ->withCardRelations()->orderBy('title')->get(),
            'nearby'   => $city->nearby(),
        ]);
    }
}
