<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('runwrk:push-dispatch')->everyMinute()->withoutOverlapping(5);
Schedule::command('queue:prune-failed --hours=168')->daily();
