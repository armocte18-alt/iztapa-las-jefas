<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario de Incidencias</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #334155; }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #065f46;
            padding-bottom: 10px;
        }

        .title { font-size: 18px; font-weight: bold; color: #065f46; margin: 0; }
        .subtitle { font-size: 13px; color: #64748b; margin-top: 5px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }

        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            border: 1px solid #e2e8f0;
            padding: 8px;
        }

        td { border: 1px solid #e2e8f0; padding: 8px; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SISTEMA ACUSES — REPORTE DE INCIDENCIAS</div>
        <div class="subtitle">
            Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
            al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Folio</th>
                <th style="width: 12%;">Cuenta</th>
                <th style="width: 18%;">Situación detectada</th>
                <th style="width: 18%;">Acción solicitada</th>
                <th style="width: 27%;">Comentarios</th>
                <th style="width: 15%;">Fecha y Hora</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidencias as $inc)
                <tr>
                    <td style="font-weight: bold; text-align: center; color: #b91c1c;">{{ $inc->folio }}</td>
                    <td style="text-align: center;">{{ $inc->acuse->cuenta ?? 'No Asignada' }}</td>
                    <td>{{ $inc->situacion }}</td>
                    <td>{{ $inc->accion }}</td>
                    <td>{{ $inc->comentarios ?: 'Sin comentarios adicionales.' }}</td>
                    <td style="font-size: 0.8rem; text-align: center; color: #64748b;">
                        <div>{{ $inc->created_at->format('d/m/Y') }}</div>
                        <div style="font-weight: bold; margin-top: 2px;">{{ $inc->created_at->format('g:i A') }}</div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
