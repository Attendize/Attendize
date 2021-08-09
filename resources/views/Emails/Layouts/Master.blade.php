<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        @section('subject')
            Attendize.com Email
        @show
    </title>
</head>

<body style="background-color: #f7f7f7;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;width: 100% !important;height: 100%">

<!-- body -->
<table class="body-wrap" style="background-color: #f7f7f7;margin: 0;padding: 20px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;width: 100%">
    <tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6">
        <td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6"></td>
        <td class="container" bgcolor="#FFFFFF" style="margin: 0 auto !important;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;border: 1px solid #f0f0f0;display: block !important;max-width: 600px !important;clear: both !important">

            <!-- content -->
            <div class="content" style="color: #888;margin: 0 auto;padding: 20px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;max-width: 600px;display: block">
                <table style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;width: 100%">
                    <tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6">
                        <td style="text-align: center;padding: 10px;margin: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6">
                            <img style="max-width: 110px;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6" src="{{url(asset(isset($email_logo) ? $email_logo : 'assets/images/logo-dark.png'))}}" />
                        </td>
                    </tr>
                    <tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6">
                        <td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6">
                            @yield('message_content')
                        </td>
                    </tr>
                </table>
            </div>
            <!-- /content -->

        </td>
        <td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6"></td>
    </tr>
</table>
<!-- /body -->

<!-- footer -->
<table class="footer-wrap" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;width: 100%;clear: both !important">
    <tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6">
        <td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6"></td>
        <td class="container" style="margin: 0 auto !important;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;display: block !important;max-width: 600px !important;clear: both !important">

            <!-- content -->
            <div class="content" style="margin: 0 auto;padding: 20px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;max-width: 600px;display: block">
                <table style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;width: 100%">
                    <tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6">
                        <td align="center" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6">
                            {{--Attendize is provided free of charge on the condition the below hyperlink is left in place.--}}
                            {{--See https://www.attendize.com/license.html for more information.--}}
                            <p style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 14px;line-height: 1.6;color: #666;margin-bottom: 10px;font-weight: normal">Powered By <a href="https://www.attendize.com/?email_footer_powered_by" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;color: #999">Attendize.com</a>.
                            </p>
                            @yield('footer')
                        </td>
                    </tr>
                </table>
            </div><!-- /content -->

        </td>
        <td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6"></td>
    </tr>
</table>
<!-- /footer -->

</body>
</html>
