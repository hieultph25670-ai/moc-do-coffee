<?php
require_once __DIR__ . '/config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function user(): ?array { return $_SESSION['user'] ?? null; }
function go(string $url): never { header('Location: '.$url); exit; }
function require_login(): void { if (!user()) go('index.php?page=dangnhap'); }
function money(float|int|string $amount): string { return number_format((float)$amount, 0, ',', '.') . '₫'; }
function flash(?string $message = null): string { if ($message !== null) { $_SESSION['flash']=$message; return ''; } $value=$_SESSION['flash']??''; unset($_SESSION['flash']); return $value; }
function image_url(string $name): string { return 'https://images.unsplash.com/'.$name.'?auto=format&fit=crop&w=900&q=80'; }
require_once __DIR__ . '/config/auth.php';
