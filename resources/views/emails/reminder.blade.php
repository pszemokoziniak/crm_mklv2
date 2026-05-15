<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1f2937;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="padding:24px 32px;background-color:#ffffff;border-bottom:3px solid #4338ca;" align="left">
                            <img src="{{ url('/images/LogoMkl_extend.png') }}" alt="MKL CRM" width="180" style="display:block;border:0;outline:none;text-decoration:none;height:auto;max-width:180px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <div style="font-size:15px;line-height:1.6;color:#1f2937;">
                                {!! $body !!}
                            </div>
                            @if(!empty($url))
                                <div style="margin-top:24px;">
                                    <a href="{{ $url }}" style="display:inline-block;padding:10px 20px;background-color:#4338ca;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:500;">Przejdź do rekordu</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;background-color:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;">
                            Pozdrawiamy, Zespół MKL CRM
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
