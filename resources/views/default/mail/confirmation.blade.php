<html>

<head>
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <style type="text/css">
        body {
            margin: 0 !important;
        }

        img {
            max-width: 100%;
            height: auto;
            vertical-align: middle;
        }

        hr {
            border: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .wrap .m-0 {
            margin: 0;
        }

        .wrap {
            max-width: 730px;
            margin: 0 auto;
            padding: 0 75px 30px;
            border-left: 1px solid #EEEEEE;
            border-right: 1px solid #EEEEEE;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            font-size: 16px;
            line-height: 21px;
            color: #000000;
        }

        .wrap b {
            color: #000;
        }

        .wrap figure {
            margin: 0;
        }

        .wrap p {
            margin-bottom: 1.25em;
        }

        .wrap h1,
        .wrap h2,
        .wrap h3,
        .wrap h4,
        .wrap h5,
        .wrap h6 {
            margin-top: 0;
            margin-bottom: 1em;
            line-height: 1.25em;
            font-weight: 700;
            color: #000;
        }

        .wrap h1 {
            font-size: 24px;
        }

        .wrap h2 {
            font-size: 19px;
            margin-bottom: 1.25em;
        }

        .wrap h3 {
            font-size: 18px;
        }

        .wrap h4 {
            font-size: 16px;
        }

        .wrap ul {
            padding: 0;
            list-style-position: inside;
        }

        .wrap a {
            color: #000;
            transition: all 0.3s;
        }

        .brand-logo {
            display: inline-block;
            width: 81px;
            height: 19px;
            margin: 0 auto;
            background: url(./logo.png);
            background-size: cover;
            background-position: center;
        }

        .btn {
            display: inline-block;
            padding: 0.7em 2em;
            position: relative;
            background-color: #330582;
            font-size: 17px;
            text-align: center;
            text-decoration: none;
            color: #fff !important;
            box-sizing: border-box;
            white-space: nowrap;
        }

        .btn .off-badge {
            width: 68px;
            height: 68px;
            top: -34px;
            right: -34px;
        }

        .btn:hover {
            background-color: #4b0cb8;
        }

        .btn-lg {
            padding-top: 1em;
            padding-bottom: 1em;
            font-size: 18px;
        }

        .btn-block {
            width: 100%;
        }

        .btn-circle {
            border-radius: 5em;
        }

        .btn-round {
            border-radius: 4px;
        }

        .btn-accent {
            background-color: #E19D7E;
        }

        .btn-only-icon {
            width: 37px;
            height: 37px;
            padding: 0;
            box-sizing: border-box;
        }

        .btn-only-icon img {
            margin: 14px auto;
        }

        .img-round-shadow {
            border-radius: 15px;
            box-shadow: 0 33px 60px rgba(0, 0, 0, 0.08);
        }

        .border-box {
            margin-top: 35px;
            margin-bottom: 35px;
            border: 1px dashed #dddddd;
            padding: 30px 35px;
            border-radius: 5px;
        }

        .border-box-2 {
            display: block;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px dashed #dddddd;
            transition: all 0.3s;
        }

        .border-box-2 .txt {
            display: inline-block;
            margin-bottom: 0;
            padding: 13px 0 13px 30px;
        }

        .border-box-2 .btn-wrap {
            float: right;
            padding: 7px 15px 7px 22px;
            border-left: 1px solid rgba(112, 112, 112, 0.12);
        }

        .border-box-2:hover {
            background-color: #E19D7E;
            border-color: #E19D7E;
            color: #fff;
        }

        .border-box-2:hover u {
            color: inherit
        }

        .border-box-2:hover .btn-only-icon {
            background-color: #000;
        }

        .inline-list {
            padding: 0;
            margin: 0 0 30px;
            list-style: none;
        }

        .inline-list li {
            display: inline-block;
        }

        .inline-list li+li {
            margin-left: 50px;
        }

        .inline-list a {
            text-decoration: none;
        }

        .inline-list-pipe {
            padding: 0;
            margin: 0 0 25px;
            list-style: none;
            color: #000;
        }

        .inline-list-pipe li {
            display: inline-block;
        }

        .inline-list-pipe a {
            text-decoration: none;
        }

        .inline-list-pipe .pipe {
            color: rgba(0, 0, 0, 0.1);
        }

        .inline-list-pipe li+li {
            margin-left: 15px;
        }

        .social-icons {
            padding: 0;
            margin: 0 0 35px;
            list-style: none;
        }

        .social-icons li {
            display: inline-block;
        }

        .social-icons li+li {
            margin-left: 14px;
        }

        .social-icons a {
            display: inline-block;
            width: 51px;
            height: 51px;
            border-radius: 30px;
            background-color: #D3D7DD;
            padding: 15px;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        .social-icons a:hover {
            background-color: #000;
        }

        .top-logo {
            text-align: center;
            padding: 35px 10px;
        }

        .content-head {
            position: relative;
            background-color: #252329;
        }

        .content-head {
            width: 100%;
            height: auto;
        }

        .content {
            max-width: 570px;
            margin: 0 auto;
            border-radius: 6px;
        }

        .content-contents {
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.07);
        }

        .content-indent {
            padding: 45px;
        }

        .content-indent-sm {
            padding: 30px;
        }

        .content-dark {
            padding: 30px 35px 50px;
            background-color: #212121;
        }

        .content-dark h2 {
            color: #fff;
        }

        .content-contents u {
            color: #000;
        }

        .foot {
            padding: 50px 0 80px;
            font-size: 14px;
            text-align: center;
            color: rgba(0, 0, 0, 0.4)
        }

        .foot a {
            color: #000;
        }

        .foot a:hover {
            color: #E19D7E;
        }

        .need-help-p {
            font-weight: 18;
            font-weight: 600;
            text-align: center;
        }
    </style>

    <style type="text/css">
        @media only screen and (max-width:480px) {
            .wrap {
                padding-left: 10px !important;
                padding-right: 10px !important;
                border: none !important;
            }

            .wrap h2 {
                font-size: 28px !important;
            }

            .content {
                max-width: none !important;
            }

            .content-contents {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }

            .inline-list li+li {
                margin-left: 20px !important;
            }

            .content-indent {
                padding: 30px 20px 30px !important;
            }
        }

        @media (prefers-color-scheme: dark) {
            .wrap {
                background: #010101 !important;
                border-color: rgba(255, 255, 255, 0.2) !important;
                color: #7c7c7c !important;
            }

            .brand-logo {
                background-image: url(./logo-light.png) !important;
            }

            .wrap h1,
            .wrap h2,
            .wrap h3,
            .wrap h4,
            .wrap h5,
            .wrap h6,
            .wrap a,
            .wrap b,
            .inline-list-pipe,
            .content-contents u,
            .foot a {
                color: #fff !important;
            }

            .foot {
                color: rgba(255, 255, 255, 0.5) !important;
            }

            .content-contents {
                background-color: rgba(255, 255, 255, 0.05);
            }

            .content,
            .border-box,
            .border-box-2,
            .border-box-2 .btn-wrap {
                border-color: rgba(255, 255, 255, 0.45) !important;
            }

            .inline-list-pipe .pipe {
                color: rgba(255, 255, 255, 0.45) !important;
            }
        }
    </style>

</head>

<body>

    <div class="wrap">

        <div class="top-logo">
            <figure class="brand-logo">
            </figure>
        </div>

        <div class="content">

            <div class="content-head">
                <img src="{{ $settings->site_url }}/images/mail/confirmation-head.jpg" alt="Confirmation" width="1144"
                    height="564">
            </div>

            <div class="content-contents">

                <div class="content-indent">

                    @php
                        $template->content = str_replace(['{site_name}', '{site_url}', '{reset_url}', '{affiliate_url}', '{user_name}', '{user_activation_url}'], [$settings->site_name, $settings->site_url, $settings->site_url . '/forgot-password/retrieve/' . $user->password_reset_code, $settings->site_url . '/register?aff=' . $user->affiliate_code, $user->name, $settings->site_url . '/confirm/email/' . $user->email_confirmation_code], $template->content);
                    @endphp

                    {!! $template->content !!}

                </div>

            </div>

        </div>

    </div>
    <div class="foot">
        <p class="m-0">You are signed up for this email as {{ $user->email }}.</p>
    </div>
    </div>

</body>

</html>
