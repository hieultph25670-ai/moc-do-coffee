<?php
require_once __DIR__.'/config/auth.php';
function user(): ?array {
    static $loaded=false;static $current=null;
    if($loaded)return $current;$loaded=true;
    $session=$_SESSION['user']??null;if(!$session||empty($session['id']))return null;
    global $conn;
    try{$q=$conn->prepare('SELECT id,fullname,email,phone,role,gender,birth_date,created_at FROM users WHERE id=?');$q->bind_param('i',$session['id']);$q->execute();$current=$q->get_result()->fetch_assoc();$q->close();if(!$current){unset($_SESSION['user']);return null;}$_SESSION['user']=$current;return $current;}catch(Throwable $e){return $session;}
}
function money(float|int|string $amount): string {return number_format((float)$amount,0,',','.').'₫';}
function go(string $url): never {header('Location: '.$url);exit;}
function flash(?string $message=null): string {if($message!==null){$_SESSION['flash']=$message;return '';} $value=(string)($_SESSION['flash']??'');unset($_SESSION['flash']);return $value;}
function csrf_field(): string {return '<input type="hidden" name="csrf" value="'.h(csrf_token()).'">';}
function require_login(): void {if(!user())go('index.php?page=login');}
function category_slug(string $name): string {$value=mb_strtolower(trim($name),'UTF-8');$value=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$value)?:$value;$value=preg_replace('/[^a-z0-9]+/','-',$value);return trim($value,'-');}
function image_url(?string $url): string {$url=trim((string)$url);if(filter_var($url,FILTER_VALIDATE_URL)&&in_array(strtolower((string)parse_url($url,PHP_URL_SCHEME)),['http','https'],true))return $url;return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=900&q=85';}
