<?php
require 'mucluc.php';need();
$r=role();$me=uid();$ie=e($me);$ten=$_SESSION['ten'];
$gvcn=$r=='sv'?substr($me,0,strpos($me,'v')+1):'';
if($_SERVER['REQUEST_METHOD']=='POST'&&$r!='sv'){
    $a=$_POST['act']??'';
    if($a=='dang'){
        $nd=trim($_POST['nd']??'');$lo=$_POST['loai']??'Chung';$ok=true;
        if($nd==='')flash('Vui lòng nhập nội dung thông báo!','err');
        else{
            if($r!='gv')$lo='Chung';
            elseif($lo=='Lớp học'){
                $lp=trim($_POST['lop']??'');
                if($lp!==''&&one("SELECT 1 FROM lophoc WHERE tenlop='".e($lp)."' AND magv='$ie'"))$lo='Lớp '.$lp;
                else{flash('Vui lòng chọn lớp học của bạn!','err');$ok=false;}
            }elseif($lo=='Lớp sinh hoạt')$lo='Lớp sinh hoạt '.$me;
            else $lo='Chung';
            if($ok){
                $ng=($r=='qtv'||$r=='ql')?"Quản lý / $ten":$ten;
                if(q("INSERT INTO thongbao (ngaydang,loaitb,nguoidang,nd) VALUES (CURDATE(),'".e($lo)."','".e($ng)."','".e($nd)."')"))flash('Đã đăng thông báo thành công!');
            }
        }
    }elseif($a=='xoa'){
        $id=(int)($_POST['id']??0);
        $x=one("SELECT nguoidang FROM thongbao WHERE stt=$id");
        if(!$x)flash('Thông báo không tồn tại!','err');
        elseif($r=='gv'&&$x['nguoidang']!=$ten)flash('Bạn chỉ được xóa thông báo do chính mình đăng!','err');
        elseif(q("DELETE FROM thongbao WHERE stt=$id"))flash('Đã xóa thông báo!');
    }
    go('thongbao.php');
}
$loc=$_GET['loc']??'all';
$optSV=['all'=>'Tất cả thông báo','chung'=>'Thông báo Chung','sh'=>'Lớp sinh hoạt'];
if($r=='sv')foreach(rows("SELECT l.tenlop FROM lophoc l JOIN dangky_lop dk ON l.id_lop=dk.id_lop WHERE dk.masv='$ie' AND dk.trangthai=1") as $x)$optSV['Lớp '.$x['tenlop']]='Lớp '.$x['tenlop'];
if($r=='qtv'||$r=='ql')$sql="SELECT * FROM thongbao WHERE loaitb='Chung' ORDER BY stt DESC";
elseif($r=='gv')$sql="SELECT * FROM thongbao WHERE loaitb='Chung' OR loaitb='Lớp sinh hoạt $ie' OR loaitb IN (SELECT CONCAT('Lớp ',tenlop) FROM lophoc WHERE magv='$ie') ORDER BY stt DESC";
else{
    $g=e($gvcn);
    if($loc=='chung')$sql="SELECT * FROM thongbao WHERE loaitb='Chung' ORDER BY stt DESC";
    elseif($loc=='sh')$sql="SELECT * FROM thongbao WHERE loaitb='Lớp sinh hoạt $g' ORDER BY stt DESC";
    elseif(isset($optSV[$loc])&&$loc!='all')$sql="SELECT * FROM thongbao WHERE loaitb='".e($loc)."' ORDER BY stt DESC";
    else $sql="SELECT * FROM thongbao WHERE loaitb='Chung' OR loaitb='Lớp sinh hoạt $g' OR loaitb IN (SELECT CONCAT('Lớp ',l.tenlop) FROM lophoc l JOIN dangky_lop dk ON l.id_lop=dk.id_lop WHERE dk.masv='$ie' AND dk.trangthai=1) ORDER BY stt DESC";
}
$data=[];
foreach(rows($sql) as $x){
    $lt=$x['loaitb'];
    if(strpos($lt,'Lớp sinh hoạt')===0)$dt=['c-purple','Lớp sinh hoạt'];
    elseif($lt=='Chung')$dt=['c-blue','Chung'];
    else $dt=['c-green',$lt];
    $row=[$x['ngaydang'],$dt,$x['nguoidang'],$x['nd']];
    if($r!='sv')$row[]=($r!='gv'||$x['nguoidang']==$ten)?['raw','<form method="post"><input type="hidden" name="id" value="'.$x['stt'].'">'.btn('Xóa','b-red sm','xoa').'</form>']:'';
    $data[]=$row;
}
head('Thông Báo');
if($r=='sv')echo '<form method="get" class="card filter">'.fld('Bộ lọc',sel('loc',$optSV,$loc)).'<button class="btn b-blue">Lọc</button></form>';
else{
    $lops=[''=>'-- Chọn lớp --'];
    if($r=='gv')foreach(rows("SELECT tenlop FROM lophoc WHERE magv='$ie' ORDER BY tenlop") as $x)$lops[$x['tenlop']]=$x['tenlop'];
    $dtuong=$r=='gv'?['Chung'=>'Chung','Lớp học'=>'Lớp học','Lớp sinh hoạt'=>'Lớp sinh hoạt']:['Chung'=>'Chung'];
    echo '<form method="post" class="card"><h3>Đăng Thông Báo Mới</h3><div class="grid">'
    .fld('Đối tượng',sel('loai',$dtuong))
    .($r=='gv'?fld('Lớp (khi chọn "Lớp học")',sel('lop',$lops)):'')
    .fld('Nội dung','<textarea name="nd" placeholder="Nhập nội dung thông báo..."></textarea>','w3')
    .'</div><div class="acts">'.btn('Đăng Thông Báo','b-green','dang').'</div></form>';
}
tbl(['Ngày Đăng','Đối Tượng','Người Đăng','Nội Dung']+($r!='sv'?[4=>'']:[]),$data);
foot();
