<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BotManController extends Controller
{
    public function handle(): void
    {
        $botman = app('botman');
        $botman->receivesFiles(function (BotMan $botman, $files) {

        });
        $botman->hears('.*(hi|hello|bonjour).*', function (BotMan $botman, string $message) {
            if (Str::lower($message) === 'hi' || Str::lower($message) === 'hello') {
                $this->askNameEn($botman);
            } else if (Str::lower($message) === 'bonjour') {
                $this->askNameFr($botman);
            }
        });
        $botman->fallback(function (BotMan $botman) {
            $botman->reply('Sorry, I did not understand these commands.');
        });
        $botman->listen();
    }

    private function askNameEn(BotMan $botman): void
    {
        $botman->ask('Hello! What is your name?', function (Answer $answer) use ($botman) {
            $name = $answer->getText();
            $this->say("Nice to meet you {$name}!");
            $this->askForFiles('I am ready now. Upload your file!', function ($files) {
                foreach ($files as $file) {
                    $url = $file->getUrl();
                    $payload = $file->getPayload();
                    Log::debug($url);
                }
            });
        });
    }

    private function askNameFr(BotMan $botman): void
    {
        $botman->ask('Bonjour! Quel est ton nom?', function (Answer $answer) use ($botman) {
            $name = $answer->getText();
            $this->say("Enchanté, {$name}!");
            $this->askForFiles('Je suis prêt maintenant. Téléverse ton fichier!', function ($files) {
                foreach ($files as $file) {
                    $url = $file->getUrl();
                    $payload = $file->getPayload();
                    Log::debug($url);
                }
            });
        });
    }
}