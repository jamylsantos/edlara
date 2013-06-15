<!doctype html>
<html>
    <head>
        <title>Edulara</title>        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @stylesheets('bootstrap')
        @stylesheets('grans')
    </head>
    <body>
        <div class="container-fluid" id='top-heading'>
            <div class="row-fluid" >
                <div id="clouds">
                    <div class="cloud x1"></div>
                    <div class="cloud x2"></div>
                    <div class="cloud x3"></div>
                    <div class="cloud x4"></div>
                    <div class="cloud x5"></div>
                </div>
                <span class="brand-name" id='top-header'>
                    EdLara
                </span>
            </div>

        </div>

        {{-- Bootstrap JS Compiled --}}
        @javascripts('bootstrap')
        @javascripts('grans')
        <script type="text/javascript">
            // $("#top-header").fitText(1.0, { minFontSize: '24px', maxFontSize: '480px' });
        </script>
    </body>
</html>