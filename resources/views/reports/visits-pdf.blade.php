<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Visitas</title>
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
    <h1>Reporte de Visitas</h1>
    <p>Generado el: {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Tipo</th>
                <th>Técnico</th>
                <th>Estado</th>
                <th>Programada</th>
                <th>Completada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
            <tr>
                <td>{{ $visit->ticket->ticket_number ?? 'N/A' }}</td>
                <td>{{ $visit->type === 'preventive' ? 'Preventiva' : 'Correctiva' }}</td>
                <td>{{ $visit->technician->name ?? 'N/A' }}</td>
                <td>{{ $visit->status }}</td>
                <td>{{ $visit->scheduled_at ? $visit->scheduled_at->format('d/m/Y H:i') : 'N/A' }}</td>
                <td>{{ $visit->completed_at ? $visit->completed_at->format('d/m/Y H:i') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión de Cuadrillas Técnicas</p>
    </div>
</body>
</html>
