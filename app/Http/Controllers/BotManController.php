<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Support\Facades\Log;

class BotManController extends Controller
{
    public function handle(): void
    {
        $botman = app('botman');
        $botman->hears('.*(hi|hello|bonjour).*', function (BotMan $botman, string $message) {
            $this->askName($botman);
        });
        $botman->fallback(function (BotMan $botman) {
            $botman->reply('Sorry, I did not understand these commands.');
        });
        $botman->listen();
    }

    private function askName(BotMan $botman): void
    {
        $botman->ask('Hello! What is your token?', function (Answer $answer) use ($botman) {

            $token = $answer->getText();
            $response = FederaApi::whoAmI($token);
            $name = $response['name'];
            $botman->userStorage()->save([
                'user' => $response,
                'token' => $token,
            ]);
            $this->say("Nice to meet you {$name}!");
            $this->askForFiles('I am ready now. Upload your file!', function ($files) use ($botman) {
/*
                $token = $botman->userStorage()->get('token');
                Log::debug($token);

                $response = FederaApi::executeSqlQuery($token, [
                    'format' => 'arrays_with_header',
                    'invalidate_cache' => true,
                    'force_rebuild' => true,
                    'sql_query' => 'SELECT COUNT(*) FROM tmp_babatp',
                ]);
                Log::debug($response);
*/
                foreach ($files as $file) {
                    $url = $file->getUrl();
                    $payload = $file->getPayload();
                    Log::debug($url);
                }
            });
        });
    }
}