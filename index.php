<?php
// Instalasi:
// composer require pragmarx/google2fa
//
// Di dalam contoh ini, secret disimpan di session.
// Seharusnya di database. Tiap user punya satu secret unik.
//
// Untuk menampilkan QR code, saya memakai Google APIs.

session_start();

include("vendor/autoload.php");
use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();

if (!isset($_SESSION['secret'])) {           // Bila belum ada session dengan key 'secret'...
  $secret = $google2fa->generateSecretKey(); // Buat kode baru. Misalnya: E5JPO5BEQRSC6FJI
  $_SESSION['secret'] = $secret;             // Simpan di session.
}
else {                                       // Sudah ada di session.
  $secret = $_SESSION['secret'];             // Gunakan kode yang tersimpan di session.
};

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "  <title>Tes QR Code</title>\n";
echo "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
echo "</head>\n";

echo "<body style=\"font-family:'Gill Sans',sans-serif\">\n";
echo "<div style=\"width:100%; max-width:600px; margin:auto\">\n";

echo "<h1>Demo Google 2FA</h1>\n";

echo "<p>Kode rahasia anda adalah <b>$secret</b>.<br>\n";
echo "Kode rahasia ini bersifat unik.<br>\n";
echo "Web server memberi setiap user satu kode rahasia yang berbeda-beda, ";
echo "kemudian disimpan di database. Tapi karena ini hanya Demo, maka ";
echo "Kode rahasia ini disimpan di Session dan berlaku selama peramban belum ditutup.</p>\n";

echo "<p>Sila ketik kode rahasia di atas ke aplikasi authenticator, misalnya Google Authenticator ";
echo "atau Twilio Authy. Alternatif lain adalah memindai (scan) QR Code di bawah ini.<br>\n";

$chl = 'otpauth://totp/username@example.com?secret=' . $secret . '&issuer=Mawan';
echo "<img src=\"https://chart.googleapis.com/chart?cht=qr&chs=128x128&chl=" . urlencode($chl) . "&choe=UTF-8\"></p>\n";

echo "<p>Setelah selesai dan aplikasi Authenticator memunculkan angka, mari kita coba.</p>\n";

echo "<form method=\"post\" action=\"./\">\n";
echo "Ketik token: <input type=\"text\" name=\"token\">\n";
echo "<input type=\"submit\" value=\"Cek\">\n";
echo "</form>\n";

if (isset($_POST['token'])) {
$token = $_POST['token'];
$valid = $google2fa->verifyKey($secret, $token);
if($valid) {
  echo "<p>Token cocok.</p>\n";
}
else {
  echo "<p>Token tidak cocok.</p>\n";
};
};

echo "</div>\n";
echo "</body>\n";
echo "</html>\n";
