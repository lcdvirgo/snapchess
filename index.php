<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Snapchess</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function () {
            setTimeout(function () {
                    $('.span-ucla').show().addClass('animated fadeInUp');}, 800

            );
            setTimeout(function () {
                    $('.span-usc').show().addClass('animated fadeInUp');}, 1100
            );
            var newUniqueID = '<?php echo uniqid(); ?>';

            $('.school').click(function () {

                var school = $(this).data('school');

                sessionStorage['school'] = school;

                var uniqueID = sessionStorage['unique_id'];
                if(!uniqueID){
                    uniqueID = newUniqueID;
                    sessionStorage['unique_id'] = uniqueID;
                }

                location.href = 'game.php';

                // alert(school);

            });

        });

    </script>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
    <link href="assets/css/bootplus.css" rel="stylesheet">
    <link href="assets/css/bootplus-responsive.css" rel="stylesheet">
    <link href="http://daneden.github.io/animate.css/animate.min.css" rel="stylesheet">
    <style type="text/css">
        body {
            padding-top: 46px;
            padding-bottom: 40px;
            background: url(http://getcharacters.com/assets/img/bg.jpg);
            background-size: cover;
            background-repeat: no-repeat;
        }

        .hero-unit h1 {
            color: #FFF
        }

        .hero-unit p {
            color: #F5F5F5
        }

        .card.people .card-info {
            display: block !important;
            position: relative !important;
            top: 240px !important;
            /* display:none !important; */
            border-top: 1px solid rgba(0, 0, 0, .2);
        }

        .card.people, .card.people .card-top {
            width: 340px !important;
            height: 400px !important;
        }

        .card.people .card-top.green {
            background-color: #ffffff;
        }

        .btn {
            line-height: 0 !important;
            position: relative !important;
            color: #ffffff !important;
            padding: 1.8em 3em !important;
            font-weight: 700 !important;
            letter-spacing: 0.15em !important;
            text-transform: uppercase !important;

            box-shadow: 0px 1px rgba(0, 0, 0, 0.2), 0px 1px rgba(255, 255, 255, 0.2) inset !important;
            cursor: pointer !important;
            text-align: center !important;
            -webkit-transition: all 0.2s;
            -moz-transition: all 0.2s;
            -o-transition: all 0.2s;
            transition: all 0.2s;
        }

        #btn-ucla {
            background: #155f8a !important;
            z-index: 5;
        }

        button#btn-ucla:hover {
            background: #03292c !important;
        }

        #btn-usc {
            background: #E43321 !important;
            z-index: 5;
        }

        button#btn-usc:hover {
            background: #C0392B !important;
        }

        div.card.people:hover {
            -moz-box-shadow: 3px 3px 4px #aaa;
            -webkit-box-shadow: 3px 3px 4px #aaa;
            box-shadow: 3px 3px 4px #aaa;
            opacity: 1;
        }
        img {
            width: 380px;
            max-width: none;
        }

        .desc {
            font-size: 15px !important;
        }


        @keyframes "noiseSlide" {
            from {
                background-position: 0px 0px;
            }
            to {
                background-position: 471px 0px;
            }
        }

        @-moz-keyframes noiseSlide {
            from {
                background-position: 0px 0px;
            }
            to {
                background-position: 471px 0px;
            }
        }

        @-webkit-keyframes "noiseSlide" {
            from {
                background-position: 0px 0px;
            }
            to {
                background-position: 471px 0px;
            }
        }

        @-ms-keyframes "noiseSlide" {
            from {
                background-position: 0px 0px;
            }
            to {
                background-position: 471px 0px;
            }
        }

        @-o-keyframes "noiseSlide" {
            from {
                background-position: 0px 0px;
            }
            to {
                background-position: 471px 0px;
            }
        }

        #noise {
            background: url(http://getcharacters.com/assets/img/noise.png);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            -webkit-animation: noiseSlide 20s linear infinite;
            -ms-animation: noiseSlide 20s linear infinite;
            -o-animation: noiseSlide 20s linear infinite;
            animation: noiseSlide 20s linear infinite;
        }


    </style>

    <style>
        @media (min-width: 1200px) {
            div.container.marketing {
                width: 800px !important;
                height: 1500px !important;
            }

            .container,
            .navbar-static-top .container,
            .navbar-fixed-top .container,
            .navbar-fixed-bottom .container {
                width: 800px !important;
            }

            .card.people {
                width: 370px !important;
            }
        }
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="assets/ico/favicon.png">
</head>
<body>
<!-- UCLA -->

<div class="container marketing" id="school_selector">
    <div class="animated fadeInUp">
    <img src="assets/img/logo1.png" style="width:80px; margin-left: 30px;">
    <img src="assets/img/logo5.png" style="width:200px; margin-left: 10px;">
    </div>
    <br>
    <br>
    <div class="span4 animated fadeInUp span-ucla" style="display:none;">
        <div class="card people school" data-school="ucla" id="school_1">
            <div class="card-top green">
                <a href="#">
                    <img src="assets/img/bear3.jpg" alt=""/>
                </a>
            </div>
            <div class="card-info">
                <br>
                <a class="title" href="#">UCLA</a>

                <div class="desc">Join the alliance of UC schools.</div>
            </div>
            <div class="card-bottom">
                <button class="btn btn-block" id="btn-ucla">Go Bruins!</button>
            </div>
        </div>
    </div>

    <div class="span4 animated fadeInUp span-usc" style="display:none;">
        <!-- USC -->
        <div class="card people school" data-school="usc" id="school_2">
            <div class="card-top green">
                <a href="#">
                    <img src="assets/img/trojan3.jpg" alt=""/>
                </a>
            </div>
            <div class="card-info">
                <br>
                <a class="title" href="#">USC</a>

                <div class="desc">Stanford and others can join the dark side...</div>
            </div>
            <div class="card-bottom">
                <button class="btn btn-block" id="btn-usc">Go Trojans!</button>
            </div>
        </div>
    </div>
</div>


<div id="noise"></div>

</body>
</html>