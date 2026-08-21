<?php

header('Content-Type: text/plain');

$file = __DIR__ . '/../vendor/symfony/http-foundation/Request.php';

if (!file_exists($file)) {
    echo "File tidak ditemukan: $file\n";
    exit;
}

$lines = file($file);
echo "Total baris di file: " . count($lines) . "\n";
echo "Ukuran file: " . filesize($file) . " bytes\n\n";
echo "--- Isi baris 105 sampai 125 ---\n";

for ($i = 105; $i <= 125; $i++) {
    $lineContent = $lines[$i - 1] ?? '[TIDAK ADA / FILE TERPOTONG DI SINI]';
    echo str_pad($i, 4) . "| " . $lineContent;
}