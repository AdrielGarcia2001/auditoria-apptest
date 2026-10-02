<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl = 'https://graph.facebook.com/v18.0';

    protected string $phoneNumberId;

    protected string $accessToken;

    public function __construct()
    {
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->accessToken = config('services.whatsapp.access_token');
    }

    public function sendTemplateMessage(string $to, string $templateName, string $language = 'es', array $components = []): bool
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => $language,
                        ],
                        'components' => $components,
                    ],
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp API error: '.$response->body());

            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: '.$e->getMessage());

            return false;
        }
    }

    public function sendTextMessage(string $to, string $message): bool
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp API error: '.$response->body());

            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: '.$e->getMessage());

            return false;
        }
    }

    public function sendTicketExpiringAlert(string $to, string $ticketNumber, string $title, string $dueDate): bool
    {
        $message = "⚠️ *Ticket por vencer*\n\n";
        $message .= "Ticket: {$ticketNumber}\n";
        $message .= "Título: {$title}\n";
        $message .= "Fecha límite: {$dueDate}\n\n";
        $message .= 'Por favor, tome las medidas necesarias.';

        return $this->sendTextMessage($to, $message);
    }

    public function sendVisitAssignedNotification(string $to, string $ticketNumber, string $title, string $scheduledAt, string $location = ''): bool
    {
        $message = "📋 *Nueva visita asignada*\n\n";
        $message .= "Ticket: {$ticketNumber}\n";
        $message .= "Título: {$title}\n";
        $message .= "Fecha programada: {$scheduledAt}\n";
        if ($location) {
            $message .= "Ubicación: {$location}\n";
        }
        $message .= "\nPor favor, confirme su disponibilidad.";

        return $this->sendTextMessage($to, $message);
    }

    public function sendTicketStatusChanged(string $to, string $ticketNumber, string $oldStatus, string $newStatus): bool
    {
        $message = "🔄 *Estado de ticket actualizado*\n\n";
        $message .= "Ticket: {$ticketNumber}\n";
        $message .= "Estado anterior: {$oldStatus}\n";
        $message .= "Nuevo estado: {$newStatus}\n\n";
        $message .= 'Consulte el sistema para más detalles.';

        return $this->sendTextMessage($to, $message);
    }

    public function sendVisitCompletedNotification(string $to, string $ticketNumber, string $visitType, string $completedAt, string $technicianName = ''): bool
    {
        $typeLabel = $visitType === 'preventive' ? 'Preventiva' : 'Correctiva';

        $message = "✅ *Visita completada*\n\n";
        $message .= "Ticket: {$ticketNumber}\n";
        $message .= "Tipo: {$typeLabel}\n";
        $message .= "Completada: {$completedAt}\n";
        if ($technicianName) {
            $message .= "Técnico: {$technicianName}\n";
        }
        $message .= "\nEl trabajo ha sido finalizado exitosamente.";

        return $this->sendTextMessage($to, $message);
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 10) {
            $phone = '52'.$phone;
        }

        return $phone;
    }
}
