<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
$db = new mysqli('localhost', 'root', '', 'ql');
if ($db->connect_errno) die('Không kết nối được cơ sở dữ liệu: ' . $db->connect_error);
$db->set_charset('utf8mb4');
function e($s){global $db;return $db->real_escape_string((string)$s);}
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function q($sql){global $db;$r=$db->query($sql);if($r===false)flash($db->error,'err');return $r;}
function rows($sql){$r=q($sql);return $r instanceof mysqli_result?$r->fetch_all(MYSQLI_ASSOC):[];}
function one($sql){$r=rows($sql);return $r?$r[0]:null;}
function flash($m,$t='ok'){$_SESSION['flash']=[$m,$t];}
function go($u){header("Location: $u");exit;}
function role(){return $_SESSION['role']??'';}
function uid(){return $_SESSION['id']??'';}
function need($roles=null){if(!isset($_SESSION['id']))go('dangnhap.php');if($roles&&!in_array(role(),$roles))go('trangchu.php');}
function vnd($n){return number_format((float)$n,0,'.',',');}
function nn($v){$v=trim((string)$v);return $v===''?'NULL':"'".e($v)."'";}
function nv($v,$d='-'){return $v===null||$v===''?$d:$v;}
