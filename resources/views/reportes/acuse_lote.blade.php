<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page { margin: 0.5cm; }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }

        .acuse-container {
            width: 100%;
            height: {{ $setup['height'] }};
            border: 1px solid #0f172a;
            border-radius: 4px;
            position: relative;
            box-sizing: border-box;
            overflow: hidden;
            background-color: #fff;
            margin-bottom: 25px;
        }

        .linea-corte {
            width: 100%;
            border-top: 1.5px dashed #94a3b8;
            height: 1px;
            margin-top: -15px;
            margin-bottom: 10px;
            text-align: center;
            position: relative;
        }

        .tijeras {
            position: absolute;
            top: -10px;
            left: 50%;
            margin-left: -15px;
            background-color: #fff;
            padding: 0 5px;
            color: #94a3b8;
            font-size: 14px;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .franja-superior {
            background: #065f46;
            color: #fff;
            text-align: center;
            font-size: 8px;
            padding: 4px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content { padding: 10px; position: relative; }

        .folio-box {
            border: 2px solid #065f46;
            border-radius: 4px;
            width: 110px;
            height: 55px;
            text-align: center;
            float: left;
            margin-right: 15px;
        }

        .folio-label {
            font-size: 7px;
            color: #fff;
            background: #065f46;
            display: block;
            padding: 2px;
        }

        .folio-val { font-size: 22px; font-weight: bold; display: block; padding-top: 5px; color: #065f46; }

        .info-header { float: right; text-align: right; font-size: 10px; line-height: 1.3; color: #475569; }

        .beneficiario-row {
            background: #f1f5f9;
            border-left: 5px solid #065f46;
            border-radius: 0 4px 4px 0;
            margin-top: 60px;
            padding: 6px 15px;
            width: 60%;
        }

        .bene-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .bene-name { font-size: 12px; font-weight: bold; color: #0f172a; }

        .barcode-area { position: absolute; right: 15px; top: 95px; text-align: center; }

        .qr-area { position: absolute; left: 15px; bottom: 8px; text-align: center; }

        .footer-firma { position: absolute; bottom: 8px; left: 0; width: 100%; text-align: center; }

        .linea-firma {
            border-top: 1.5px solid #0f172a;
            width: 280px;
            margin: 0 auto;
            font-size: 8px;
            font-weight: bold;
            padding-top: 2px;
            text-transform: uppercase;
        }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @php $itemCount = 0; @endphp
    @foreach($acuses as $acuse)
        @for($i = 1; $i <= $setup['copias']; $i++)
            @php $itemCount++; @endphp

            <div class="acuse-container">
                @if(!empty($logoPath) && file_exists($logoPath))
                    <img src="{{ $logoPath }}" style="width:100%; height:50px; display:block; border-bottom:1px solid #0f172a;">
                @endif
                <div class="franja-superior">RECIBO OFICIAL DE ENTREGA DE TARJETA FINABIEN | IZTAPA'LAS JEFAS 2026</div>

                <div class="content">
                    <div class="folio-box">
                        <span class="folio-label">NÚMERO DE FOLIO</span>
                        @if(trim((string) $acuse->nss_issste) === '- H')
                            <span class="folio-val">{{ $acuse->cuarta_linea }}{{ $acuse->nss_issste }}</span>
                        @else
                            <span class="folio-val">{{ $acuse->cuarta_linea }}</span>
                        @endif
                    </div>

                    <div class="info-header">
                        CUENTA: <b style="font-size:16px; color:#0f172a;">{{ $acuse->cuenta }}</b><br>
                        FECHA: <b>{{ $acuse->nss_issemym ? $acuse->nss_issemym->locale('es')->translatedFormat('d \d\e F Y') : '' }}</b><br>
                        @if($setup['copias'] > 1)
                            <span style="color:#b91c1c; font-size:9px;">(Fecha de Convocatoria)</span>
                        @endif
                    </div>

                    <div class="beneficiario-row">
                        <div class="bene-label">CURP (verifica que sea la tuya)</div>
                        <div class="bene-name" style="font-size:22px; letter-spacing: 1px;">{{ $acuse->curp }}</div>

                        <div class="bene-label" style="margin-top:4px;">Beneficiaria</div>
                        <div class="bene-name" style="font-size:13px;">{{ $acuse->nombre_completo }}</div>
                    </div>

                    <div class="barcode-area">
                        @if(!empty($acuse->barcode))
                            <img src="data:image/png;base64,{{ $acuse->barcode }}" style="width:180px; height:40px;">
                            <div style="font-size:7px; font-family:monospace;">{{ $acuse->cuarta_linea }}</div>
                        @endif
                    </div>
                </div>

                @if(!empty($acuse->qr))
                    <div class="qr-area">
                        <img src="data:image/png;base64,{{ $acuse->qr }}" style="width:45px; height:45px;">
                    </div>
                @endif

                <div class="footer-firma">
                    <div style="font-size: 7px; margin-bottom: 1px;">{{ $acuse->nombre_completo }}</div>
                    <div class="linea-firma">FIRMA DE CONFORMIDAD DE RECEPCIÓN DE TARJETA</div>
                    <div style="font-size: 8px;">RECIBÍ TARJETA EN SOBRE CERRADO</div>
                    <div style="font-size: 6px; color:#64748b;">Este documento es personal e intransferible</div>
                </div>
            </div>

            @if($itemCount % 4 != 0)
                <div class="linea-corte"><span class="tijeras">✂ Corta Aquí ✂</span></div>
            @endif

            @if($itemCount % 4 == 0 && !$loop->last)
                <div class="page-break"></div>
            @endif
        @endfor
    @endforeach
</body>
</html>
