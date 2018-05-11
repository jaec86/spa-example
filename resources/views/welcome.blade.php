<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
        <title>SPA</title>
    </head>
    <body class="font-raleway bg-grey-lighter text-grey-darkest">
        <div id="app">
            <router-view></router-view>
            <fs-toaster></fs-toaster>
        </div>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
