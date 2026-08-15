<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Partido - BALTC</title>
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<div style="max-width:600px;margin:0 auto;padding:20px 0;">

    <!-- Header -->
    <div style="background:#e9eaef;padding:20px 30px;border-radius:12px 12px 0 0;">
        <img src="https://www.baltc.net/torneo/static/img/logo.png" alt="BALTC" style="display:block; width:210px;">
    </div>

    <!-- Hero -->
    <div style="background:linear-gradient(135deg,#f0ad4e 0%,#ec971f 100%);padding:32px 30px;text-align:center;">
        <div style="font-size:48px;margin-bottom:10px;">⏰</div>
        <h1 style="margin:0;color:#fff;font-size:26px;font-weight:800;letter-spacing:-0.5px;">¡Recordatorio importante!</h1>
        <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;font-weight:600;">Tu partido vence en <?=$dias_restantes?> días</p>
    </div>

    <!-- Body -->
    <div style="background:#ffffff;padding:32px 30px;">

        <p style="margin:0 0 16px;font-size:16px;color:#333;line-height:1.5;">
            Hola <?=$nombre?>,
        </p>

        <p style="margin:0 0 16px;font-size:15px;color:#555;line-height:1.7;">
            Te recordamos que tu <strong>partido contra <?=$rival?></strong> debe jugarse <strong>antes del <?=$deadline?></strong>.
        </p>

        <div style="background:#fff8e1;border-left:4px solid #f0ad4e;border-radius:0 4px 4px 0;padding:16px;margin:20px 0;">
            <p style="margin:0;font-size:14px;color:#856404;line-height:1.7;">
                <strong>Categoría:</strong> <?=$categoria?><br>
                <strong>Ronda:</strong> <?=$ronda?><br>
                <strong>Rival:</strong> <?=$rival?><br>
                <strong>Deadline:</strong> <?=$deadline?>
            </p>
        </div>

        <p style="margin:0 0 16px;font-size:15px;color:#555;line-height:1.7;">
            <strong>¿Qué debes hacer?</strong>
        </p>

        <ul style="margin:0 0 20px;padding-left:20px;font-size:15px;color:#555;line-height:1.8;">
            <li>Si <strong>aún no jugaron</strong>, coordiná con tu rival para fijar fecha y hora</li>
            <li>Si <strong>ya jugaron</strong>, cargá el resultado en "Mi Partido" sin demora</li>
        </ul>

        <!-- CTA button -->
        <div style="text-align:center;margin:28px 0;">
            <a href="https://www.baltc.net/torneo/mipartido" style="display:inline-block;background:#f0ad4e;color:#fff;font-weight:700;font-size:15px;text-decoration:none;padding:14px 32px;border-radius:8px;">
                👉 Ver Mi Partido
            </a>
        </div>

        <p style="margin:0 0 16px;font-size:14px;color:#888;line-height:1.7;">
            Si ya coordinaron la fecha, ingresá en <strong>Mi Partido</strong> para confirmarla. Si ya jugaron, cargá el resultado para que se refleje en los standings.
        </p>

        <div style="text-align:center;padding:20px 0;border-top:1px solid #eee;margin-top:28px;">
            <p style="margin:0;font-size:13px;color:#1a1a2e;font-weight:700;">¡No dejes que se venza el plazo! 🎾</p>
        </div>

    </div>

    <!-- Footer -->
    <div style="background:#e9eaef;padding:16px 30px;text-align:center;border-radius:0 0 12px 12px;">
        <p style="margin:0;color:#888;font-size:12px;">
            Secretaría BALTC &nbsp;·&nbsp; <a href="https://www.baltc.net/torneo" style="color:#7a9e3a;">baltc.net/torneo</a>
        </p>
    </div>

</div>
</body>
</html>
