<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al Torneo - BALTC</title>
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<div style="max-width:600px;margin:0 auto;padding:20px 0;">

    <!-- Header -->
    <div style="background:#e9eaef;padding:20px 30px;border-radius:12px 12px 0 0;">
        <img src="https://www.baltc.net/torneo/static/img/logo.png" alt="BALTC" style="display:block; width:210px;">
    </div>

    <!-- Hero -->
    <div style="background:linear-gradient(135deg,#a5d051 0%,#7ab02e 100%);padding:32px 30px;text-align:center;">
        <div style="font-size:48px;margin-bottom:10px;">🎾</div>
        <h1 style="margin:0;color:#1a1a2e;font-size:26px;font-weight:800;letter-spacing:-0.5px;">¡Bienvenido al torneo!</h1>
        <p style="margin:8px 0 0;color:rgba(26,26,46,0.7);font-size:14px;font-weight:600;">Ya están los cuadros listos</p>
    </div>

    <!-- Body -->
    <div style="background:#ffffff;padding:32px 30px;">

        <p style="margin:0 0 16px;font-size:16px;color:#333;line-height:1.5;">
            Hola <strong><?=ucwords(strtolower($nombre))?></strong>,
        </p>

        <p style="margin:0 0 16px;font-size:15px;color:#555;line-height:1.7;">
            ¡Gracias por inscribirte en el <strong style="color:#1a1a2e;">Torneo Interno de Singles del Open BALTC</strong>! 🎾
        </p>

        <p style="margin:0 0 20px;font-size:15px;color:#555;line-height:1.7;">
            Ya cerraron las inscripciones y se sortearon los cuadros. Podés verlos en:<br>
            <a href="https://www.baltc.net/torneo" style="color:#7a9e3a;font-weight:700;">www.baltc.net/torneo</a> (sección DRAWS)
        </p>

        <p style="margin:0 0 28px;font-size:15px;color:#555;line-height:1.7;">
            También ingresando en <strong>MIS PARTIDOS</strong> vas a poder ver con quién te toca jugar y coordinar el encuentro.<br>
            Si no tenés el contacto de tu rival, podés solicitarlo en Secretaría o a la organización.
        </p>

        <!-- CTA button -->
        <div style="text-align:center;margin:28px 0;">
            <a href="https://www.baltc.net/torneo" style="display:inline-block;background:#a5d051;color:#1a1a2e;font-weight:700;font-size:15px;text-decoration:none;padding:14px 32px;border-radius:8px;">
                👉 Ver Draws
            </a>
        </div>

        <!-- Canchas -->
        <div style="background:#f8f9fa;border-radius:10px;padding:22px;margin-bottom:14px;">
            <h3 style="margin:0 0 12px;font-size:15px;color:#1a1a2e;font-weight:700;">Días de juego y canchas</h3>
            <p style="margin:0 0 10px;font-size:14px;color:#555;line-height:1.7;">
                Coordiná con tu rival y jueguen el día y horario que prefieran dentro de la semana (lunes a domingo).
            </p>
            <p style="margin:0 0 10px;font-size:14px;color:#c0392b;font-weight:700;line-height:1.7;">
                ⏰ Importante: tenés que jugar el partido antes de la fecha límite informada por mail.
            </p>
            <p style="margin:0 0 12px;font-size:14px;color:#555;line-height:1.7;">
                Si van a jugar después de las 18:00, recomendamos reservar previamente para asegurarse cancha, especialmente durante la semana.
            </p>
            <p style="margin:0 0 6px;font-size:14px;color:#333;font-weight:700;">Prioridades:</p>
            <p style="margin:0 0 4px;font-size:14px;color:#555;line-height:1.7;">
                &nbsp;&nbsp;• <strong>Cancha 9:</strong> prioridad para singles todos los días (excepto horarios de escuela)
            </p>
            <p style="margin:0 0 4px;font-size:14px;color:#555;line-height:1.7;">
                &nbsp;&nbsp;• <strong>Cancha 14:</strong> prioridad adicional para torneo
            </p>
            <p style="margin:0 0 4px;font-size:14px;color:#555;line-height:1.7;">
                &nbsp;&nbsp;&nbsp;&nbsp;– Sábados desde las 12:00
            </p>
            <p style="margin:0;font-size:14px;color:#555;line-height:1.7;">
                &nbsp;&nbsp;&nbsp;&nbsp;– Domingos y feriados durante todo el día
            </p>
        </div>

        <!-- Pelotas -->
        <div style="background:#f8f9fa;border-radius:10px;padding:22px;margin-bottom:14px;">
            <h3 style="margin:0 0 12px;font-size:15px;color:#1a1a2e;font-weight:700;">Pelotas</h3>
            <p style="margin:0;font-size:14px;color:#555;line-height:1.7;">
                Podés retirar pelotas en Secretaría (en horario de atención) y devolverlas al finalizar el partido.<br>
                Si Secretaría está cerrada, por favor devolvelas al día siguiente.
            </p>
        </div>

        <!-- Resultados -->
        <div style="background:#fff8e1;border-left:4px solid #f0ad4e;border-radius:0 10px 10px 0;padding:22px;margin-bottom:14px;">
            <h3 style="margin:0 0 12px;font-size:15px;color:#1a1a2e;font-weight:700;">Resultados</h3>
            <p style="margin:0 0 12px;font-size:14px;color:#555;line-height:1.7;">
                Al finalizar el partido, el ganador debe cargar el resultado en <strong>MIS PARTIDOS</strong>.
            </p>
            <p style="margin:0;font-size:14px;color:#555;line-height:1.7;">
                Para seguir el avance del torneo y próximos cruces, ingresá en <strong>PARTIDOS Y RESULTADOS</strong>.<br>
                También podés consultar el reglamento completo en la web.
            </p>
        </div>

        <!-- Contacto -->
        <div style="background:#f0f4ff;border-left:4px solid #4a90d9;border-radius:0 10px 10px 0;padding:22px;margin-bottom:28px;">
            <h3 style="margin:0 0 12px;font-size:15px;color:#1a1a2e;font-weight:700;">Ante cualquier duda</h3>
            <p style="margin:0 0 6px;font-size:14px;color:#444;">Juliana Piumatti: <strong>+54 9 11 3621 0003</strong></p>
            <p style="margin:0 0 6px;font-size:14px;color:#444;">Mailen Auroux: <strong>+54 9 11 6820 8130</strong></p>
            <p style="margin:0;font-size:14px;color:#444;">Bianca Cacciola: <strong>+54 9 11 5578 5858</strong></p>
        </div>

        <div style="text-align:center;padding:10px 0;">
            <p style="margin:0;font-size:17px;color:#1a1a2e;font-weight:700;">¡Gracias por participar y buen partido! 🎾</p>
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
