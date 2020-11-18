<!DOCTYPE html>
<html lang="en">
<head>
  <title>NEMS GPIO Extender Receiver</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      text-align: center;
      background: #000;
      color: #666;
    }
    #gpioe {
      padding: 2em;
      font-size: 2em;
      color: #aaa;
    }
    a {
      color: #777;
    }
  </style>
</head>
<body>
  <h2>NEMS GPIO Extender Receiver</h2>
  <div id="gpioe"></div>
  <p>&copy; 2019-<?= date('Y') ?> Robbie Ferguson // Category5 TV Network</p>
  <p><a href="https://nemslinux.com/" target="_blank">nemslinux.com</a> | <a href="https://docs.nemslinux.com/en/latest/nems-tools/gpioextender.html" target="_blank">Documentation</a></p>
  <script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/jquery/jquery.min.js"></script>
  <script>
    $('#gpioe').load('log.php');
    setInterval(function() {
      $('#gpioe').load('log.php');
    }, 10000);
  </script>
</body>
</html>
