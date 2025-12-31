<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>File Manager</title>

    <!-- jQuery (required) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- jQuery UI (required for elFinder) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

    <!-- elFinder CSS -->
    <link rel="stylesheet" href="../vendor/studio-42/elfinder/css/elfinder.min.css">
    <link rel="stylesheet" href="../vendor/studio-42/elfinder/css/theme.css">

    <!-- elFinder JS -->
    <script src="../vendor/studio-42/elfinder/js/elfinder.min.js"></script>

    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        #elfinder { height: 100%; }
    </style>
</head>
<body>
    <div id="elfinder"></div>

    <script>
        $(function() {
            $('#elfinder').elfinder({
                url: 'connector.php',
                lang: 'en',
                height: '100%'
            });
        });
    </script>
</body>
</html>
