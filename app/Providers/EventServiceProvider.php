<?php

namespace App\Providers;

use App\Events\QuizCreated;
use App\Events\Announcement;
use App\Events\MaterialCreated;
use App\Events\AssignmentCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendNotificationListener;
use App\Listeners\CreateAnnouncementListener;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        QuizCreated::class => [
            CreateAnnouncementListener::class,
        ],
        AssignmentCreated::class => [
            CreateAnnouncementListener::class,
        ],
        MaterialCreated::class => [
            CreateAnnouncementListener::class,
        ],
        Announcement::class => [
            SendNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
