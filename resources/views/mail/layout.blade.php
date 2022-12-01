{{-- <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>@section('title') @show</title>
        <link rel="preconnect" href="https://fonts.gstatic.com">
    </head>

    <body>

        @yield('content')

    </body>
</html> --}}

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />
    <meta content="light dark" name="color-scheme" />
    <meta content="light dark" name="supported-color-schemes" />
    <title>@section('title') @show</title>

    <link
      href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i"
      rel="stylesheet"
    />
    <style type="text/css">
      @import url('https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i');

      :root {
        color-scheme: light dark;
        supported-color-schemes: light dark;
      }

      td,
      a,
      p,
      span {
        font-family: 'San Francisco', Segoe, Roboto, Arial, Helvetica, sans-serif !important;
        word-wrap: normal;
        word-break: normal;
        hyphens: none;
      }

      .bullet {
        line-height: 21px !important;
      }

      .btnBack:hover {
        background-color: #ea0c80;
      }

      .btn:hover {
        background-color: #e4ebf3;
      }

      @media only screen and (min-width: 600px) {
        .tableItem {
          display: inline-block !important;
          -moz-box-sizing: border-box !important;
          -webkit-box-sizing: border-box !important;
          box-sizing: border-box !important;
          width: 50% !important;
          padding-right: 22px !important;
          vertical-align: top !important;
        }

        .tableNoBackItem {
          display: inline-block !important;
          -moz-box-sizing: border-box !important;
          -webkit-box-sizing: border-box !important;
          box-sizing: border-box !important;
          width: 50% !important;
          padding-right: 12px !important;
          padding-left: 12px !important;
          vertical-align: top !important;
        }
      }

      @media only screen and (max-width: 375px) {
        .btn,
        .btnBack {
          max-width: 100% !important;
        }
      }

      @media (prefers-color-scheme: dark) {
        .bodyTable {
          background-color: #404041 !important;
        }

        .contentTable {
          background-color: #333333 !important;
        }

        .textColor1 {
          color: #ffffff !important;
        }

        .textColor2 {
          color: #adadad !important;
        }

        .textColor3 {
          color: #8d8d8e !important;
        }

        .link {
          color: #336fee !important;
        }

        .line {
          background-color: #6d6f71 !important;
        }

        .accentBlock {
          background-color: #3c4149 !important;
        }

        .alarmBlock {
          background-color: #48453e !important;
        }

        .accentBorder {
          background-color: #6d6f71 !important;
        }

        .promoAccentBlock {
          background-color: #404041 !important;
        }

        .blockBack {
          background-color: #404041 !important;
        }

        .statDown {
          color: #f54444 !important;
        }

        .statUp {
          color: #00b92d !important;
        }

        .btn {
          color: #428bf9 !important;
          background-color: #3c4149 !important;
        }

        .btnBack {
          color: #ffffff !important;
          background-color: #ea7bda !important;
        }
      }

      [data-ogsc] .bodyTable {
        background-color: #404041 !important;
      }

      [data-ogsc] .contentTable {
        background-color: #333333 !important;
      }

      [data-ogsc] .textColor1 {
        color: #ffffff !important;
      }

      [data-ogsc] .textColor2 {
        color: #adadad !important;
      }

      [data-ogsc] .textColor3 {
        color: #8d8d8e !important;
      }

      [data-ogsc] .link {
        color: #336fee !important;
      }

      [data-ogsc] .line {
        background-color: #6d6f71 !important;
      }

      [data-ogsc] .accentBlock {
        background-color: #3c4149 !important;
      }

      [data-ogsc] .alarmBlock {
        background-color: #48453e !important;
      }

      [data-ogsc] .accentBorder {
        background-color: #6d6f71 !important;
      }

      [data-ogsc] .promoAccentBlock {
        background-color: #404041 !important;
      }

      [data-ogsc] .blockBack {
        background-color: #404041 !important;
      }

      [data-ogsc] .statDown {
        color: #f54444 !important;
      }

      [data-ogsc] .statUp {
        color: #00b92d !important;
      }

      [data-ogsc] .btn {
        color: #428bf9 !important;
        background-color: #3c4149 !important;
      }

      [data-ogsc] .btnBack {
        color: #ffffff !important;
        background-color: #ea7bda !important;
      }
    </style>

    <!--[if mso]>
      <style type="text/css">
        td,
        a,
        p,
        span {
          font-family: Arial, sans-serif !important;
        }
      </style>
    <![endif]-->
  </head>

  <body style="margin: 0; padding: 0">

    @yield('content')

  </body>
</html>
