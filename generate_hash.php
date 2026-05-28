<?php
$password = 'Qwerty_1617';
$hashed = password_hash($password, PASSWORD_BCRYPT);
echo "Hash: " . $hashed;
?>