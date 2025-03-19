<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:command-tasks')->dailyAt('08:00');