<?php
require_once __DIR__ . '/config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function user(): ?array {
    static $checked=false; static $current=null;
    if($checked)return $current;
    $checked=true;$session=$_SESSION['user']??null;if(!$session||empty($session['id']))return null;
    global $db;
    try{$q=$db->prepare("SELECT id,name,email,phone,role,gioi_tinh,ngay_sinh,trang_thai_tai_khoan FROM nguoi_dung WHERE id=? AND trang_thai_tai_khoan IN ('active','deletion_pending')");$q->bind_param('i',$session['id']);$q->execute();$row=$q->get_result()->fetch_assoc();$q->close();if(!$row){unset($_SESSION['user']);return null;}$_SESSION['user']=array_merge($session,$row);$current=$_SESSION['user'];return $current;}catch(Throwable $error){$current=$session;return $current;}
}
function go(string $url): never { header('Location: '.$url); exit; }
function require_login(): void { if (!user()) go('index.php?page=dangnhap'); }
function money(float|int|string $amount): string { return number_format((float)$amount, 0, ',', '.') . '₫'; }
function flash(?string $message = null): string { if ($message !== null) { $_SESSION['flash']=$message; return ''; } $value=$_SESSION['flash']??''; unset($_SESSION['flash']); return $value; }
function image_url(string $name): string {
    $name=trim($name);
    if (filter_var($name,FILTER_VALIDATE_URL)) {
        $scheme=strtolower((string)parse_url($name,PHP_URL_SCHEME));
        if (in_array($scheme,['http','https'],true)) return $name;
    }
    if (preg_match('/^photo-[A-Za-z0-9_-]+$/',$name)) return 'https://images.unsplash.com/'.$name.'?auto=format&fit=crop&w=900&q=80';
    return 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=80';
}
require_once __DIR__ . '/config/auth.php';
