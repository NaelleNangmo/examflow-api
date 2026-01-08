<?php
use Illuminate\Validation\ValidationException;

if ($exception instanceof ValidationException) {
    return response()->json([
        'success' => false,
        'message' => 'Erreur de validation',
        'errors'  => $exception->errors(),
    ], 422);
}
