<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'welcome',
                'name' => 'Bienvenida - Cliente nuevo',
                'category' => 'welcome',
                'channel' => 'any',
                'body' => "Hola {client_name}! 👋\n\nBienvenido/a al servicio de {service_name}.\n\nTus datos de acceso son:\n{credentials}\n\nTu pago mensual es de {amount}, cada dia {due_day} del mes.\n\nCualquier duda me escribes. Saludos!",
            ],
            [
                'slug' => 'reminder_3_days',
                'name' => 'Recordatorio - 3 dias antes',
                'category' => 'reminder',
                'channel' => 'any',
                'body' => "Hola {client_name}! 📋\n\nTe recuerdo que tu pago de {service_name} por {amount} vence el {due_date}.\n\nGracias por tu puntualidad! 🙏",
            ],
            [
                'slug' => 'reminder_due',
                'name' => 'Recordatorio - Dia de pago',
                'category' => 'reminder',
                'channel' => 'any',
                'body' => "Hola {client_name}! 📅\n\nHoy es dia de pago de {service_name}.\nMonto: {amount}\n\nPuedes realizar tu pago por transferencia o QR. Gracias!",
            ],
            [
                'slug' => 'overdue_3_days',
                'name' => 'Mora - 3 dias',
                'category' => 'overdue',
                'channel' => 'any',
                'body' => "Hola {client_name} 🔔\n\nTu pago de {service_name} por {amount} esta pendiente desde el {due_date}.\n\nPor favor realiza el pago a la brevedad para mantener tu acceso activo.\n\nGracias!",
            ],
            [
                'slug' => 'overdue_7_days',
                'name' => 'Mora - 7 dias',
                'category' => 'overdue',
                'channel' => 'any',
                'body' => "Hola {client_name} ⚠️\n\nTu pago de {service_name} por {amount} tiene 7 dias de retraso (vencio el {due_date}).\n\nSi no se recibe el pago pronto, el acceso podria ser suspendido.\n\nPor favor comunicate conmigo. Gracias.",
            ],
            [
                'slug' => 'credentials_change',
                'name' => 'Cambio de credenciales',
                'category' => 'update',
                'channel' => 'any',
                'body' => "Hola {client_name} 🔑\n\nSe actualizaron las credenciales de {service_name}.\n\nNuevos datos:\n{credentials}\n\nDisculpa las molestias!",
            ],
            [
                'slug' => 'payment_confirmed',
                'name' => 'Pago confirmado',
                'category' => 'update',
                'channel' => 'any',
                'body' => "Hola {client_name} ✅\n\nTu pago de {service_name} por {amount} del mes de {month} fue recibido.\n\nGracias por tu puntualidad!",
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::firstOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
