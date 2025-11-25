<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Registrasi Ulang</title>
    <style>
        /* Reset CSS sederhana agar tampilan konsisten */
        body { margin: 0; padding: 0; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        img { border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        a img { border: none; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; color: #374151;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin-bottom: 20px;">
                    <tr>
                        <td align="center" style="padding-bottom: 10px;">
                            <h1 style="font-size: 24px; font-weight: 700; color: #004f3b; margin: 0;">SIMANTAP</h1>
                            <p style="margin: 5px 0 0 0; font-size: 14px; color: #6b7280;">Sistem Informasi Manajemen Tenaga Pegawai</p>
                        </td>
                    </tr>
                </table>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px; background-color: #ffffff; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
                    
                    <tr>
                        <td style="padding: 40px 40px;">
                            
                            <h2 style="margin: 0 0 20px 0; font-size: 20px; font-weight: 600; color: #111827; text-align: center;">
                                Verifikasi Registrasi
                            </h2>

                            <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 24px; color: #374151;">
                                Halo, <strong>{{ $record->name }}</strong>.
                            </p>
                            <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 24px; color: #374151;">
                                Data pra-registrasi Anda dengan NIK <strong>{{ $record->nik }}</strong> telah kami terima. Langkah selanjutnya adalah melakukan verifikasi dan melengkapi berkas.
                            </p>

                            <div style="text-align: center; margin-bottom: 24px;">
                                <a href="{{ url('/re-regist?token=' . $record->token) }}" 
                                   style="display: inline-block; background-color: #004f3b; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; padding: 10px 24px; border-radius: 12px; transition: background-color 0.3s;">
                                    Verifikasi Sekarang
                                </a>
                            </div>

                            <p style="margin: 0 0 0 0; font-size: 14px; line-height: 24px; color: #374151;">
                                Klik tombol di atas untuk melanjutkan proses registrasi ulang di sistem SIMANTAP.
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 5px 0; font-size: 12px; color: #6b7280;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #004f3b; word-break: break-all;">
                                <a href="{{ url('/re-regist?token=' . $record->token) }}" style="color: #004f3b; text-decoration: none;">
                                    {{ url('/register-ulang?token=' . $record->token) }}
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin-top: 24px;">
                    <tr>
                        <td align="center" style="color: #9ca3af; font-size: 12px;">
                            &copy; {{ date('Y') }} SIMANTAP. All rights reserved.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>