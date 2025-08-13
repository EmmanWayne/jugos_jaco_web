<?php

namespace App\Services;

use App\Filament\Support\FilamentNotification;
use Throwable;
use Illuminate\Database\QueryException;

class ExceptionHandlerService
{
    /**
     * Maneja la excepción y muestra la notificación apropiada
     */
    public static function handle(Throwable $exception, string $context = 'registro'): void
    {
        $message = self::getErrorMessage($exception, $context);
        
        FilamentNotification::error(
            title: $message['title'],
            body: $message['body']
        );
    }

    /**
     * Devuelve el mensaje de error apropiado según el tipo de excepción
     */
    public static function getErrorMessage(Throwable $exception, string $context = 'registro'): array
    {
        if ($exception instanceof QueryException) {
            return self::getQueryExceptionMessage($exception, $context);
        }

        // Excepciones generales: mostrar el mensaje lanzado si existe
        $message = trim((string) $exception->getMessage());
        if ($message !== '') {
            return [
                'title' => 'Error',
                'body' => $message,
            ];
        }

        // Fallback genérico si no hay mensaje
        return [
            'title' => 'Error inesperado',
            'body' => "Ocurrió un error inesperado al procesar el {$context}. Por favor, inténtelo de nuevo."
        ];
    }

    /**
     * Obtiene el mensaje para excepciones de consulta SQL
     */
    private static function getQueryExceptionMessage(QueryException $exception, string $context): array
    {
        $errorCode = $exception->getCode();
        $errorMessage = $exception->getMessage();

        // Error de restricción única
        if (self::isUniqueConstraintError($errorCode, $errorMessage)) {
            return self::getUniqueConstraintMessage($errorMessage, $context);
        }

        // Error de clave foránea
        if (self::isForeignKeyConstraintError($errorCode, $errorMessage)) {
            return [
                'title' => 'Error de relación',
                'body' => "No se puede procesar el {$context} porque está relacionado con otros registros. Verifique las dependencias."
            ];
        }

        // Error de campo requerido/nulo
        if (self::isNotNullConstraintError($errorCode, $errorMessage)) {
            return [
                'title' => 'Campos requeridos',
                'body' => "Faltan campos obligatorios para crear el {$context}. Complete todos los campos requeridos."
            ];
        }

        // Error de base de datos no específico
        return [
            'title' => 'Error de base de datos',
            'body' => "Error en la base de datos al procesar el {$context}. Verifique los datos e inténtelo de nuevo."
        ];
    }

    /**
     * Obtiene el mensaje para errores de restricción única
     */
    private static function getUniqueConstraintMessage(string $errorMessage, string $context): array
    {
        $fieldMessage = "Ya existe un {$context} con estos datos.";
        
        return [
            'title' => 'Error de duplicación',
            'body' => $fieldMessage
        ];
    }

    /**
     * Verifica si el error es de restricción única
     */
    private static function isUniqueConstraintError(string $errorCode, string $errorMessage): bool
    {
        return $errorCode === '23000' || 
               str_contains($errorMessage, 'UNIQUE constraint failed') || 
               str_contains($errorMessage, 'Duplicate entry') ||
               str_contains($errorMessage, 'unique');
    }

    /**
     * Verifica si el error es de clave foránea
     */
    private static function isForeignKeyConstraintError(string $errorCode, string $errorMessage): bool
    {
        return str_contains($errorMessage, 'FOREIGN KEY constraint failed') ||
               str_contains($errorMessage, 'foreign key constraint') ||
               str_contains($errorMessage, 'Cannot delete or update a parent row');
    }

    /**
     * Verifica si el error es de campo no nulo
     */
    private static function isNotNullConstraintError(string $errorCode, string $errorMessage): bool
    {
        return str_contains($errorMessage, 'NOT NULL constraint failed') ||
               str_contains($errorMessage, 'cannot be null');
    }
}
