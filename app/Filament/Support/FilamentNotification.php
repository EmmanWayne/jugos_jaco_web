<?php

namespace App\Filament\Support;

use Filament\Notifications\Notification;

class FilamentNotification
{
    /**
     * Mostrar una notificación de éxito.
     *
     * @param string $title El título de la notificación
     * @param string|null $body Mensaje de cuerpo opcional
     * @return void
     */
    public static function success(string $title, ?string $body = null): void
    {
        $notification = Notification::make()
            ->title($title)
            ->success();

        if ($body) {
            $notification->body($body);
        }

        $notification->send();
    }

    /**
     * Mostrar una notificación de error.
     *
     * @param string $title El título de la notificación
     * @param string|null $body Mensaje de cuerpo opcional
     * @return void
     */
    public static function error(string $title, ?string $body = null): void
    {
        $notification = Notification::make()
            ->title($title)
            ->danger();

        if ($body) {
            $notification->body($body);
        }

        $notification->send();
    }

    /**
     * Mostrar una notificación de advertencia.
     *
     * @param string $title El título de la notificación
     * @param string|null $body Mensaje de cuerpo opcional
     * @return void
     */
    public static function warning(string $title, ?string $body = null): void
    {
        $notification = Notification::make()
            ->title($title)
            ->warning();

        if ($body) {
            $notification->body($body);
        }

        $notification->send();
    }

    /**
     * Mostrar una notificación informativa.
     *
     * @param string $title El título de la notificación
     * @param string|null $body Mensaje de cuerpo opcional
     * @return void
     */
    public static function info(string $title, ?string $body = null): void
    {
        $notification = Notification::make()
            ->title($title)
            ->info();

        if ($body) {
            $notification->body($body);
        }

        $notification->send();
    }
    
    /**
     * Crear una notificación personalizada que puede utilizarse con cancelación de acciones.
     * 
     * @param string $title El título de la notificación
     * @param string|null $body Mensaje de cuerpo opcional
     * @param string $type El tipo de notificación (success, error, warning, info)
     * @return Notification
     */
    public static function create(string $title, ?string $body = null, string $type = 'info'): Notification
    {
        $notification = Notification::make()
            ->title($title);
            
        if ($body) {
            $notification->body($body);
        }
        
        switch ($type) {
            case 'success':
                $notification->success();
                break;
            case 'error':
                $notification->danger();
                break;
            case 'warning':
                $notification->warning();
                break;
            case 'info':
            default:
                $notification->info();
                break;
        }
        
        return $notification;
    }
}
