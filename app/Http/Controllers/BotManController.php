<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;

class BotManController extends Controller
{
    public function handle(): void
    {
        $botman = app('botman');
        $botman->hears('{message}', function (BotMan $botman, string $message) {
            if ($message == 'hi') {
                $this->askName($botman);
            } else {
                $botman->reply("write 'hi' for testing...");
            }
        });
        $botman->listen();
    }

    public function askName(BotMan $botman): void
    {
        $botman->ask('Hello! What is your Name?', function (Answer $answer) use ($botman) {
            $name = $answer->getText();
            $this->say('Nice to meet you ' . $name);
        });
    }
}