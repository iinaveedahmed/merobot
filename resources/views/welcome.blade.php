<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato', sans-serif;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
            .sub {
                font-size: 40px;
                color: #555;
                font-weight: bold;
            }
            .cross {
                color: #8b224a;
                text-decoration: line-through;
            }
            .link {
                font-size: 40px;
                border: 1px solid #2e6da4;
                color: #2e6da4;
                font-weight: bold;
                padding: 10px 40px;
                text-decoration: none;
                margin-top: 20px;
                display: inline-block;
                background: #fafafa;
                border-radius: 3px  ;
            }
            .link:hover {
                background: #2e6da4;
                color: #fff;
            }
            .uri {
                color: #2e6da4;
                font-size: 30px;
                font-weight: bold;
                line-height: 1.8;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">meRobot v1.0 Beta</div>
                <div class="sub">An <span class="cross">Two</span> Multi-Robot path mapping and collision detection api service.</div>
                <div class="uri">http://dev.inaveed.com/merobot/public/api/</div>
                <a class="link" href="https://github.com/inaveedahmed/merobot/blob/master/README.md" rel="api">Documentation</a>

            </div>
        </div>
    </body>
</html>
