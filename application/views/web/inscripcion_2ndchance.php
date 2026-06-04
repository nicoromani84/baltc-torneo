<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>2nd Chance — BALTC Torneo</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<style>
body { background:#1a1a2e; min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:'Segoe UI',sans-serif; }
.box { background:rgba(255,255,255,0.05); border:1px solid rgba(165,208,81,0.3); border-radius:16px; padding:40px 32px; max-width:420px; width:100%; text-align:center; }
.icon { font-size:56px; margin-bottom:16px; }
h2  { color:#fff; font-weight:700; margin-bottom:8px; }
p   { color:rgba(255,255,255,0.65); font-size:15px; margin-bottom:0; }
.badge-cat { display:inline-block; background:rgba(165,208,81,0.15); color:#a5d051; border:1px solid rgba(165,208,81,0.4); border-radius:20px; padding:4px 16px; font-size:13px; font-weight:700; margin-bottom:20px; }
a.btn-menu { display:inline-block; margin-top:24px; background:#a5d051; color:#1a1a2e; font-weight:700; padding:12px 28px; border-radius:8px; text-decoration:none; font-size:14px; }
</style>
</head>
<body>
<div class="box">
<?php if($ok): ?>
    <div class="icon">🎾</div>
    <h2>¡Estás anotado!</h2>
    <div class="badge-cat"><?=htmlspecialchars($categoria)?></div>
    <p>Hola <strong style="color:#fff"><?=ucwords(strtolower($nombre))?></strong>, tu inscripción al <strong style="color:#a5d051">2nd Chance</strong> fue confirmada. Pronto se realizará el sorteo.</p>
    <a href="<?=base_url('menu')?>" class="btn-menu">Ir al torneo</a>
<?php elseif($ya_inscripto): ?>
    <div class="icon">✅</div>
    <h2>Ya estás anotado</h2>
    <div class="badge-cat"><?=htmlspecialchars($categoria)?></div>
    <p>Hola <strong style="color:#fff"><?=ucwords(strtolower($nombre))?></strong>, ya estabas inscripto en el 2nd Chance. ¡Nos vemos en la cancha!</p>
    <a href="<?=base_url('menu')?>" class="btn-menu">Ir al torneo</a>
<?php else: ?>
    <div class="icon">❌</div>
    <h2>Link inválido</h2>
    <p>Este link no es válido o ya expiró. Contactá a la secretaría del club.</p>
<?php endif; ?>
</div>
</body>
</html>
