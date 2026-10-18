<?php
require 'mucluc.php';need(['qtv','ql']);
if($_SERVER['REQUEST_METHOD']=='POST'){
    $a=$_POST['act']??'';$t=e(trim($_POST['ten']??''));
    $hp=(float)str_replace(',','',$_POST['hp']??0);$st=(int)($_POST['st']??0);$sb=(int)($_POST['sb']??0);
    if($a=='them'){
        if($t=='')flash('Vui lòng nhập tên khóa học!','err');
        elseif(q("INSERT INTO khoahoc (tenkh,hocphi,sotuanhoc,sobuoihoc) VALUES ('$t',$hp,$st,$sb)"))flash('Thêm khóa học thành công!');
    }elseif($a=='sua'){
        if(q("UPDATE khoahoc SET hocphi=$hp,sotuanhoc=$st,sobuoihoc=$sb WHERE tenkh='$t'"))flash('Cập nhật khóa học thành công!');
    }elseif($a=='xoa'){
        if(q("DELETE FROM khoahoc WHERE tenkh='$t'"))flash('Xóa khóa học thành công!');
    }
    go('khoahoc.php');
}
$kw=trim($_GET['q']??'');
$w=$kw!==''?" WHERE tenkh LIKE '%".e($kw)."%'":'';
$s=isset($_GET['sel'])?one("SELECT * FROM khoahoc WHERE tenkh='".e($_GET['sel'])."'"):null;
$data=[];
foreach(rows("SELECT * FROM khoahoc$w") as $x)$data[]=[$x['tenkh'],vnd($x['hocphi']),$x['sotuanhoc'],$x['sobuoihoc'],['raw','<a href="?sel='.urlencode($x['tenkh']).'">Chọn</a>']];
head('Khóa Học');
searchbar('Nhập tên khóa học...');
echo '<form method="post" class="card"><h3>Thông tin Khóa Học</h3><div class="grid">'
.fld('Tên Khóa Học',inp('ten',$s['tenkh']??'','text',$s?'readonly':''))
.fld('Học Phí (VNĐ)',inp('hp',isset($s)?(float)$s['hocphi']:''))
.fld('Số Tuần Học',inp('st',$s['sotuanhoc']??''))
.fld('Số Buổi Học',inp('sb',$s['sobuoihoc']??''))
.'</div><div class="acts">'
.($s?btn('Cập nhật','b-blue','sua').btn('Xóa','b-red','xoa').'<a class="btn b-gray" href="khoahoc.php">Làm mới</a>':btn('Thêm mới','b-green','them'))
.'</div></form>';
tbl(['Tên Khóa Học','Học Phí','Số Tuần Học','Số Buổi Học',''],$data);
foot();
