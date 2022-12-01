@extends('mail.layout')

@section('title', 'welcome')

@section('content')

<table
      bgcolor="#f5f6f7"
      border="0"
      cellpadding="0"
      cellspacing="0"
      class="bodyTable"
      style="
        border-collapse: collapse;
        -webkit-text-size-adjust: none;
        -ms-text-size-adjust: none;
        -moz-text-size-adjust: none;
        word-wrap: normal;
        word-break: normal;
        hyphens: none;
      "
      width="100%"
    >
      <tr>
        <td align="center" style="padding-bottom: 48px">
          <!--[if gte mso 9]>
          <table border="0"
                 cellpadding="0"
                 cellspacing="0"
                 width="600">
                    <tr>
                        <td>
          <![endif]-->
          <table
            bgcolor="#ffffff"
            border="0"
            cellpadding="0"
            cellspacing="0"
            class="contentTable"
            style="
              width: 100%;
              max-width: 600px;
              -webkit-box-sizing: border-box;
              -moz-box-sizing: border-box;
              box-sizing: border-box;
            "
            width="100%"
          >
            <tr>
              <td align="center" style="padding: 0 20px">
                <!--[if gte mso 9]>
                <table border="0"
                       cellpadding="0"
                       cellspacing="0"
                       width="536">
                                <tr>
                                    <td>
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 536px" width="100%">
                  <!-- start: header -->
                  <tr>
                    <td style="padding: 20px 0 16px">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <!-- start: image -->
                        <tr>
                          <td align="center" style="padding-bottom: 32px">
                            <img
                              alt=""
                              border="0"
                              src="{{ $message->embed(asset('images/email/titul.png')) }}"
                              style="display: block; width: 100%; max-width: 536px"
                              width="536"
                            />
                          </td>
                        </tr>
                        <!-- end: image -->

                        <!-- start: header title -->
                        <tr>
                          <td
                            align="center"
                            class="textColor1"
                            style="
                              font-family: San Francisco,  Segoe,  Roboto, Arial, Helvetica,  sans-serif;
                              color: #333333;
                              font-weight: 600;
                              font-size: 26px;
                              line-height: 36px;
                              letter-spacing: 0;
                            "
                          >
                            BEST DATE
                          </td>
                        </tr>
                        <!-- end: header title -->
                      </table>
                    </td>
                  </tr>
                  <!-- end: header -->

                  <tr>
                    <td>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <!-- start: accent text with border -->
                        <tr>
                          <td>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tr>
                                <td style="padding: 12px 0 12px">
                                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                      <td
                                        bgcolor="#494a4c"
                                        class="accentBorder"
                                        height="1"
                                        style="background: url({{ $message->embed(asset('images/email/line.png')) }}); font-size: 1px; line-height: 1px"
                                      ></td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                              <tr>
                                <td style="padding: 16px 0 16px">
                                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                      <td
                                        align="center"
                                        class="textColor1"
                                        style="
                                          font-family: 'San Francisco',  Segoe,  Roboto, Arial, Helvetica,  sans-serif;
                                          font-size: 20px;
                                          line-height: 28px;
                                          color: #333333;
                                          font-weight: bold;
                                        "
                                      >
                                        {{ __('Welcome to ***!')}}
                                      </td>
                                    </tr>
                                    <tr>
                                      <td
                                        align="center"
                                        class="textColor1"
                                        style="
                                          font-family: 'San Francisco',  Segoe,  Roboto, Arial, Helvetica,  sans-serif;
                                          font-size: 20px;
                                          line-height: 28px;
                                          color: #333333;
                                          font-weight: bold;
                                        "
                                      >
                                        {{ __('Thank you for registering and we wish you to find your soul mate!')}}
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <!-- end: accent text with border -->
                      </table>
                    </td>
                  </tr>

                  @include('mail.footer')

                </table>
                <!--[if gte mso 9]>
                </td>
                </tr>
                </table>
                <![endif]-->
              </td>
            </tr>
          </table>
          <!--[if gte mso 9]>
          </td>
          </tr>
          </table>
          <![endif]-->
        </td>
      </tr>
    </table>

@endsection
