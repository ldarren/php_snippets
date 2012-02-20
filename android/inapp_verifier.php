#! /usr/bin/php -q
<?php

$data = '{"nonce":-123456,"orders":[{"notificationId":"4321","orderId":"224455","packageName":"com.github.php_snippets","productId":"com.github.php_snippets.cash_1","purchaseTime":1329717062000,"purchaseState":0}]}';

$signature = 'zgd75L3W3ijPxcW6LAAAAFQCuZK9umGc0WFDkFMTzsLb+gIKNXwAAAIEAs42Ueefbyo5copjU4ysLs69XMSNWp9V0bVRSXIIxf0tcWLBRVWuW2Lqk+9aRaXFFGunMm2PUZx4vEl7V+4UZplFni8hXElbhz67WvA1MKDcWms6dS+7LNywajHP2+m9e7ujGq34N63ukEKqgTtnQ8iOMjhYTIEoxac6f1biA5XgAAACAeQt9zvyXX2i+1AcmoTK/BAjGngPr8cVOYo63NKq8zVuFeRjBDceSdU19p7TgskDZ6E2ZOiE3S+W/WwYJtBRQektDJG+Cvi1HTkguMc0Gof0Z3Lblf2eowZL5evQvvmhBI8KGxrkJ/mjp54Ol/st93HUYofH64HW2SykLdbrSXbo==)';

// key must be in this format
$public_key_str = '-----BEGIN PUBLIC KEY-----
AAAAB3NzaC1kc3MAAACBALfbTtnVYM9gHQL/E73f9Cbay0kCeHYqbcDT18RSc92
Z9TkL3wng4MYNEblXLcZM3aY5vaoTtn4+LX7DEaHadgH12oe82FHf1cp+uKwwmb
Q+VObIoZBVX+gwp8y+cxNM8QhkEd6UMjd3h2p1EqpY5vzLrWV
-----END PUBLIC KEY-----';

$key = openssl_get_publickey(trim($public_key_str));
if(!$key){die("Can't get public key\n");}
$signature = base64_decode( $signature );
$ok = openssl_verify($data, $signature, $key);
var_dump($ok);
