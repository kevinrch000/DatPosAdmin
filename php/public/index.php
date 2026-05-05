<?php
require_once __DIR__ . '/../src/Auth.php';

$base = Auth::base_path();
header('Location: ' . $base . '/Account/Login.php');
exit;
