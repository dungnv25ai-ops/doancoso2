<?php
require 'mucluc.php';need(['qtv','ql']);
if($_SERVER['REQUEST_METHOD']=='POST'){
    $a=$_POST['act']??'';$id=e(trim($_POST['id']??''));$ten=e(trim($_POST['ten']??''));$gt=e($_POST['gt']??'Nam');
    $sdt=nn($_POST['sdt']??'');$em=nn($_POST['em']??'');
    if($a=='them'){
        if($id==''||$ten=='')flash('Vui lòng nhập đầy đủ Mã SV và Họ Tên!','err');
        elseif(one("SELECT 1 FROM sinhvien WHERE masv='$id'"))flash('Mã SV đã tồn tại!','err');
        else{
            q("INSERT IGNORE INTO taikhoan VALUES ('$id','1','$ten','sv')");
            if(q("INSERT INTO sinhvien (masv,ten,gioitinh,sdt,email) VALUES ('$id','$ten','$gt',$sdt,$em)"))flash('Thêm sinh viên thành công!');
        }
    }elseif($a=='sua'){
        if($ten=='')flash('Họ Tên không được để trống!','err');
        else{
            q("UPDATE taikhoan SET ten='$ten' WHERE tendangnhap='$id'");
            if(q("UPDATE sinhvien SET ten='$ten',gioitinh='$gt',sdt=$sdt,email=$em WHERE masv='$id'"))flash('Lưu hồ sơ thành công!');
        }
    }elseif($a=='xoa'){
        if(q("DELETE FROM taikhoan WHERE tendangnhap='$id'"))flash('Xóa sinh viên thành công!');
    }
    go('sinhvien.php');
}
$kw=trim($_GET['q']??'');
$w=$kw!==''?" WHERE masv LIKE '%".e($kw)."%' OR ten LIKE '%".e($kw)."%'":'';
$s=isset($_GET['sel'])?one("SELECT * FROM sinhvien WHERE masv='".e($_GET['sel'])."'"):null;
$data=[];
foreach(rows("SELECT * FROM sinhvien$w ORDER BY masv DESC") as $x)$data[]=[$x['masv'],$x['ten'],nv($x['gioitinh']),nv($x['sdt']),nv($x['email']),['raw','<a href="?sel='.urlencode($x['masv']).'">Chọn</a>']];
head('Sinh Viên');
searchbar('Nhập Mã SV hoặc Tên để tìm kiếm...');
echo '<form method="post" class="card"><h3>Thông tin Sinh viên</h3><div class="grid">'
.fld('Mã SV',inp('id',$s['masv']??'','text',$s?'readonly':''))
.fld('Họ Tên',inp('ten',$s['ten']??''))
.fld('Giới tính',sel('gt',['Nam'=>'Nam','Nữ'=>'Nữ','Khác'=>'Khác'],$s['gioitinh']??'Nam'))
.fld('SĐT',inp('sdt',$s['sdt']??''))
.fld('Email',inp('em',$s['email']??''))
.'</div><div class="acts">'
.($s?btn('Lưu Hồ Sơ','b-blue','sua').btn('Xóa','b-red','xoa').'<a class="btn b-gray" href="sinhvien.php">Làm mới</a>':btn('Thêm mới','b-green','them'))
.'</div></form>';
tbl(['Mã SV','Họ Tên','Giới tính','SĐT','Email',''],$data);
foot();
