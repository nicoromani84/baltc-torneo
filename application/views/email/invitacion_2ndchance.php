<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Invitación 2nd Chance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body style="padding:0;margin:0;background:#f4f6f9;">
<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;">
    <div style="background:#1a1a2e;padding:24px 28px;">
        <img src="https://www.baltc.net/torneo/static/img/logo.png" alt="BALTC" style="max-height:42px;display:block;">
    </div>
    <div style="background:#fff;padding:36px 28px;">
        <h1 style="margin:0 0 6px;font-size:24px;color:#1a1a2e;">¡Seguí en el torneo!</h1>
        <p style="color:#888;font-size:14px;margin:0 0 24px;">Torneo Interno de Dobles — 2nd Chance</p>

        <p style="color:#333;font-size:15px;line-height:1.6;margin-bottom:20px;">
            Hola <strong><?=ucwords(strtolower($nombre))?></strong>,
        </p>
        <p style="color:#333;font-size:15px;line-height:1.6;margin-bottom:20px;">
            Tu participación en <strong><?=$categoria?></strong> terminó en la <?=$ronda_inicial?>, pero el torneo te da una segunda oportunidad.
            Se abrió el cuadro de <strong><?=$categoria?> 2nd Chance</strong> y podés sumarte.
        </p>

        <div style="text-align:center;margin:32px 0;">
            <a href="<?=$link_inscripcion?>"
               style="display:inline-block;background:#a5d051;color:#1a1a2e;font-weight:700;font-size:16px;padding:14px 36px;border-radius:8px;text-decoration:none;letter-spacing:0.3px;">
                ¡Me anoto al 2nd Chance!
            </a>
        </div>

        <p style="color:#888;font-size:12px;line-height:1.6;margin-top:24px;">
            Si no querés participar, no tengas en cuenta este mail. El link de inscripción es personal y válido por 24 horas.
        </p>
        <p style="color:#555;font-size:14px;margin-top:24px;">
            Muchas gracias,<br><strong>Secretaría BALTC</strong>
        </p>
    </div>
    <div style="background:#f4f6f9;padding:14px 28px;text-align:center;">
        <p style="color:#aaa;font-size:11px;margin:0;">
            <a href="https://www.baltc.net/torneo" style="color:#a5d051;text-decoration:none;">baltc.net/torneo</a>
        </p>
    </div>
</div>
</body>
</html>
