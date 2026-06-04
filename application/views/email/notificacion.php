<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?=htmlspecialchars($asunto)?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
    @media screen {
      @font-face {
        font-family: 'Lato';
        font-style: normal;
        font-weight: 400;
        src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
      }
      body { font-family: "Lato", "Lucida Grande", "Lucida Sans Unicode", Tahoma, Sans-Serif; }
    }
    </style>
</head>
<body style="padding:0;margin:0;">
    <div style="border:1px solid #e9eaef; border-radius:4px; max-width:600px; margin:0 auto;">
        <div style="padding:20px; background:#e9eaef;">
            <img src="https://www.baltc.net/torneo/static/img/logo.png" alt="Logo" style="display:block; width:210px;" />
        </div>
        <div style="padding:30px 20px;">
            <p style="margin:0 0 20px 0; font-size:15px;">Hola <strong><?=ucwords(strtolower($nombre))?></strong>,</p>
            <div style="color:#333; font-size:15px; line-height:1.7;">
                <?=$cuerpo?>
            </div>
            <p style="color:#666; font-size:14px; margin-top:30px; border-top:1px solid #e9eaef; padding-top:20px;">
                Muchas gracias,<br><strong>Secretaría BALTC</strong>
            </p>
            <p style="color:#aaa; font-size:12px;">
                Seguí el torneo en <a href="https://www.baltc.net/torneo" style="color:#7a9e3a;">baltc.net/torneo</a>
            </p>
        </div>
    </div>
</body>
</html>
