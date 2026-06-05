<?php
// Mengatur header respons agar berupa teks biasa atau HTML
header('Content-Type: text/html; charset=utf-8');

// Memeriksa apakah parameter 'nm' ada dan bernilai 'klptdh'
if (isset($_GET['nm']) && $_GET['nm'] === 'klptdh') {
    // Memberikan respons yang diharapkan oleh script scanner untuk menandai sukses
    echo "KLPTDH_OK";
} else {
    // Respons standar jika diakses tanpa parameter yang sesuai
    echo "Welcome to the test server.";
}
?>
