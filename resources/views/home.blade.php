<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Home page</title>
        <link rel="stylesheet" href="{{URL::asset('css/app.css')}}">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="wrap">
                    <div class="box">
                        <div class="text">
                            <h5 id="host_name"></h5>This is an api service for posting and getting <span>ads.</span>
                            To use this service you should check
                            <span>
                                <a href="#" class="documentation_link">
                                    documentation!
                                </a>
                            </span>
                        </div>
                        <div class="button_box">
                            <a href="#" class="btn btn-primary documentation_link">Documentation</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="{{URL::asset('js/jquery.min.js')}}"></script>
        <script type="text/javascript" src="{{URL::asset('js/app.js')}}"></script>
    </body>
</html>
