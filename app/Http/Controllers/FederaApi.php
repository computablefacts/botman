<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FederaApi
{
    public static function whoAmI(string $apiToken): array
    {
        $endpoint = FederaApi::endpoint();
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiToken}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get("{$endpoint}/api/v2/public/whoami");
        if ($response->failed()) {
            Log::error($response->body());
            return [];
        }
        return $response['data'] ?? [];
    }

    public static function executeProblogQuery(string $apiToken, array $params): array
    {
        return FederaApi::executeRequest($apiToken, 'execute-problog-query', $params);
    }

    public static function executeSqlQuery(string $apiToken, array $params): array
    {
        return FederaApi::executeRequest($apiToken, 'execute-sql-query', $params);
    }

    public static function getObjects(string $apiToken, array $params): array
    {
        return FederaApi::executeRequest($apiToken, 'get-objects', $params);
    }

    public static function getFlattenedObjects(string $apiToken, array $params): array
    {
        return FederaApi::executeRequest($apiToken, 'get-flattened-objects', $params);
    }

    public static function findObjects(string $apiToken, array $params): array
    {
        return FederaApi::executeRequest($apiToken, 'find-objects', $params);
    }

    public static function findTerms(string $apiToken, array $params): array
    {
        return FederaApi::executeRequest($apiToken, 'find-terms', $params);
    }

    private static function executeRequest(string $apiToken, string $method, array $params): array
    {
        $endpoint = FederaApi::endpoint();
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiToken}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post("{$endpoint}/api/v2/public/json-rpc", [
            'jsonrpc' => '2.0',
            'id' => Carbon::now()->toISOString(),
            'method' => $method,
            'params' => $params,
        ]);
        if ($response->failed()) {
            Log::error($response->body());
            return [];
        }
        if (!isset($response['result'])) {
            Log::warning($response);
            return [];
        }
        return $response['result'] ?? [];
    }

    private static function endpoint(): string
    {
        return Config::get('federa.federa_endpoint');
    }
}