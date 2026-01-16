<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log de la requête
        $requestData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
            'request_body' => $this->sanitizeRequest($request->all()),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'timestamp' => now()->toIso8601String(),
        ];

        Log::channel('api')->info('API Request', $requestData);

        // Exécuter la requête
        $response = $next($request);

        // Calculer le temps d'exécution
        $executionTime = round((microtime(true) - $startTime) * 1000, 2); // en millisecondes

        // Log de la réponse
        $responseData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'response_body' => $this->sanitizeResponse($response->getContent()),
            'execution_time_ms' => $executionTime,
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
            'timestamp' => now()->toIso8601String(),
        ];

        $logLevel = $response->getStatusCode() >= 400 ? 'error' : 'info';
        Log::channel('api')->{$logLevel}('API Response', $responseData);

        // Enregistrer dans la table activity_logs
        if ($request->user()) {
            \App\Models\ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => $request->method() . ' ' . $request->path(),
                'resource_type' => null,
                'resource_id' => null,
                'old_values' => null,
                'new_values' => $requestData,
                'description' => "API Request: {$request->method()} {$request->path()} - Status: {$response->getStatusCode()}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'performed_at' => now(),
            ]);
        }

        return $response;
    }

    private function sanitizeRequest(array $data): array
    {
        // Masquer les mots de passe et tokens sensibles
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***HIDDEN***';
            }
        }

        return $data;
    }

    private function sanitizeHeaders(array $headers): array
    {
        // Masquer les headers sensibles
        $sensitiveHeaders = ['authorization', 'cookie', 'x-api-key'];
        
        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = ['***HIDDEN***'];
            }
        }

        return $headers;
    }

    private function sanitizeResponse(?string $content): mixed
    {
        if (!$content) {
            return null;
        }

        $decoded = json_decode($content, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            // Limiter la taille de la réponse pour éviter des logs trop volumineux
            if (is_array($decoded) && isset($decoded['data']) && is_array($decoded['data'])) {
                if (count($decoded['data']) > 10) {
                    $decoded['data'] = array_slice($decoded['data'], 0, 10);
                    $decoded['data_truncated'] = true;
                }
            }
            return $decoded;
        }

        // Si ce n'est pas du JSON, limiter la longueur
        return strlen($content) > 1000 ? substr($content, 0, 1000) . '...' : $content;
    }
}
