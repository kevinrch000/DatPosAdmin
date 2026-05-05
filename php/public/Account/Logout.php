<?php
require_once __DIR__ . '/../../src/Auth.php';

$base = Auth::base_path();
Auth::logout();
header('Location: ' . $base . '/Account/Login.php');
exit;
