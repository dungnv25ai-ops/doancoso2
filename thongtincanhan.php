<?php
require 'mucluc.php';need();
$id=uid();
if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_FILES['anh'])&&$_FILES['anh']['error']==0){
        $ext=strtolower(pathinfo($_FILES['anh']['name'],PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png'])){
            if(!is_dir('uploads'))mkdir('uploads');
            foreach(glob("uploads/$id.*") as $f)unlink($f);
            move_uploaded_file($_FILES['anh']['tmp_name'],"uploads/$id.$ext");
            flash('Đã đổi ảnh đại diện');
        }else flash('Chỉ nhận ảnh JPG, PNG','err');
    }
    go('thongtincanhan.php');
}
$av=glob("uploads/$id.*");
$p=preg_split('/\s+/',trim($_SESSION['ten']));
$ini=mb_strtoupper(count($p)==1?mb_substr($p[0],0,1):mb_substr($p[0],0,1).mb_substr(end($p),0,1));
head('HỒ SƠ CÁ NHÂN');
echo '<div class="card"><div class="big"><form method="post" enctype="multipart/form-data" style="text-align:center">';
echo $av?'<img class="avatar" src="'.h($av[0]).'?t='.filemtime($av[0]).'" alt="">':'<div class="avatar">'.h($ini).'</div>';
echo '<div style="margin-top:10px"><input type="file" name="anh" accept=".jpg,.jpeg,.png" style="width:190px;font-size:12px"></div><button class="btn b-blue sm" style="margin-top:8px">Đổi ảnh</button></form>';
echo '<div style="flex:1;display:grid;gap:14px">'
.fld('Họ và tên',inp('x',$_SESSION['ten'],'text','readonly'))
.fld('Vai trò',inp('x',roleName(),'text','readonly'))
.fld('Mã định danh (ID)',inp('x',$id,'text','readonly'))
.'</div></div></div>';
foot();
