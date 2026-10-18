<?php
require_once 'db.php';
if(isset($_SESSION['id']))go('trangchu.php');
$msg='';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $u=e(trim($_POST['u']??''));$p=e($_POST['p']??'');
    if($u===''||$p==='')$msg='Vui lòng nhập đầy đủ thông tin!';
    else{
        $r=one("SELECT * FROM taikhoan WHERE tendangnhap='$u' AND matkhau='$p'");
        if($r){
            $_SESSION['id']=$r['tendangnhap'];$_SESSION['ten']=$r['ten'];$_SESSION['role']=$r['vaitro'];
            go('trangchu.php');
        }
        $msg='Sai tài khoản hoặc mật khẩu.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Đăng nhập - Hệ thống Quản lý Đào tạo</title><link rel="stylesheet" href="style.css"></head>
<body class="lg">
<div class="lg-l">
<span class="lo">EDU</span>
<h1>EduManager</h1>
<p>Hệ thống quản lý đào tạo<br>chuyên nghiệp &amp; toàn diện</p>
<ul><li>Quản lý sinh viên &amp; giáo viên</li><li>Theo dõi điểm &amp; học phí</li><li>Lịch học &amp; thông báo</li></ul>
</div>
<div class="lg-r">
<form method="post">
<h2>Chào mừng trở lại</h2>
<p>Vui lòng đăng nhập để tiếp tục</p>
<label class="f"><span>Tên đăng nhập (ID)</span><input name="u" value="<?=h($_POST['u']??'')?>" autofocus></label>
<label class="f"><span>Mật khẩu</span><input type="password" name="p"></label>
<div class="msg"><?=h($msg)?></div>
<button class="btn">Đăng nhập</button>
</form>
</div>
</body></html>
