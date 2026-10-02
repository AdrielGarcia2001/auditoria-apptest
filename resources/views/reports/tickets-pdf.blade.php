<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Tickets</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 10px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <h1>Reporte de Tickets</h1>
    <p>Generado el: {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Título</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Activo</th>
                <th>Asignado</th>
                <th>Fecha límite</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->ticket_number }}</td>
                <td>{{ $ticket->title }}</td>
                <td>{{ $ticket->status }}</td>
                <td>{{ $ticket->priority }}</td>
                <td>{{ $ticket->asset->name ?? 'N/A' }}</td>
                <td>{{ $ticket->assignee->name ?? 'Sin asignar' }}</td>
                <td>{{ $ticket->due_date ? $ticket->due_date->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión de Cuadrillas Técnicas</p>
    </div>
</body>
</html>
