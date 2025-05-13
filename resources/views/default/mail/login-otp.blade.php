<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{$settings->site_name}}</title>

    <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
            rel="stylesheet"
    />
</head>
<body
        style="
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #ffffff;
      font-size: 14px;
    "
>
<div
        style="
        max-width: 680px;
        margin: 0 auto;
        padding: 45px 30px 60px;
        background: #f4f7ff;
        background-image: url(https://archisketch-resources.s3.ap-northeast-2.amazonaws.com/vrstyler/1661497957196_595865/email-template-background-banner);
        background-repeat: no-repeat;
        background-size: 800px 452px;
        background-position: top center;
        font-size: 14px;
        color: #434343;
      "
>
    <header>
        <table style="width: 100%">
            <tbody>
            <tr style="height: 0">
                <td>
                    <img
                            alt=""
                            src="{{url(custom_theme_url($settings->logo_path))}}"
                            height="30px"
                    />
                </td>
                <td style="text-align: right">
                <span style="font-size: 16px; line-height: 30px; color: #ffffff"
                >{{ \Carbon\Carbon::now()->format('d F Y') }}</span
                >
                </td>
            </tr>
            </tbody>
        </table>
    </header>

    <main>
        <div
                style="
            margin: 0;
            margin-top: 70px;
            padding: 92px 30px 115px;
            background: #ffffff;
            border-radius: 30px;
            text-align: center;
          "
        >
            <div style="width: 100%; max-width: 489px; margin: 0 auto">
                <h1
                        style="
                margin: 0;
                font-size: 24px;
                font-weight: 500;
                color: #1f1f1f;
              "
                >
                    {{$user->name." ". $user->surname}}
                </h1>
                <p
                        style="
                margin: 0;
                margin-top: 60px;
                font-size: 40px;
                font-weight: 600;
                letter-spacing: 25px;
                color: #ba3d4f;
              "
                >
                    {{$otp}}
                </p>
            </div>
        </div>
    </main>

    <footer
            style="
          width: 100%;
          max-width: 490px;
          margin: 20px auto 0;
          text-align: center;
          border-top: 1px solid #e6ebf1;
        "
    >
        <p
                style="
            margin: 0;
            margin-top: 40px;
            font-size: 16px;
            font-weight: 600;
            color: #434343;
          "
        >
            {{$settings->site_name}}
        </p>
    </footer>
</div>
</body>
</html>
