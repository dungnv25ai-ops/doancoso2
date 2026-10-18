<?php
require 'mucluc.php';need(['qtv','ql']);
if($_SERVER['REQUEST_METHOD']=='POST'){
    $a=$_POST['act']??'';$id=e(trim($_POST['id']??''));$ten=e(trim($_POST['ten']??''));$gt=e($_POST['gt']??'Nam');
    $sdt=nn($_POST['sdt']??'');$em=nn($_POST['em']??'');$md=nn($_POST['md']??'');
    $kn=(int)($_POST['kn']??0);$lg=(float)str_replace(',','',$_POST['lg']??0);
    if($a=='them'){
        if($id==''||$ten=='')flash('Vui lòng nhập đầy đủ Mã GV và Họ Tên!','err');
        elseif(one("SELECT 1 FROM giaovien WHERE magv='$id'"))flash('Mã GV đã tồn tại!','err');
        else{
            q("INSERT IGNORE INTO taikhoan VALUES ('$id','1','$ten','gv')");
            if(q("INSERT INTO giaovien (magv,tengv,gioitinh,sdt,email,monday,kinhnghiem,luong) VALUES ('$id','$ten','$gt',$sdt,$em,$md,$kn,$lg)"))flash('Thêm giáo viên thành công!');
        }
    }elseif($a=='sua'){
        if($ten=='')flash('Họ Tên không được để trống!','err');
        else{
            q("UPDATE taikhoan SET ten='$ten' WHERE tendangnhap='$id'");
            if(q("UPDATE giaovien SET tengv='$ten',gioitinh='$gt',sdt=$sdt,email=$em,monday=$md,kinhnghiem=$kn,luong=$lg WHERE magv='$id'"))flash('Cập nhật hồ sơ giáo viên thành công!');
        }
    }elseif($a=='xoa'){
        if(q("DELETE FROM taikhoan WHERE tendangnhap='$id'"))flash('Xóa giáo viên thành công!');
    }
    go('giaovien.php');
}
$kw=trim($_GET['q']??'');
$w=$kw!==''?" WHERE magv LIKE '%".e($kw)."%' OR tengv LIKE '%".e($kw)."%'":'';
$s=isset($_GET['sel'])?one("SELECT * FROM giaovien WHERE magv='".e($_GET['sel'])."'"):null;
$data=[];
foreach(rows("SELECT * FROM giaovien$w ORDER BY magv") as $x)$data[]=[$x['magv'],$x['tengv'],nv($x['gioitinh']),nv($x['sdt']),nv($x['email']),nv($x['monday']),nv($x['kinhnghiem'],'0'),vnd($x['luong']),['raw','<a href="?sel='.urlencode($x['magv']).'">Chọn</a>']];
head('Giáo Viên');
searchbar('Nhập Mã GV hoặc Tên để tìm kiếm...');
echo '<form method="post" class="card"><h3>Thông tin Giáo viên</h3><div class="grid">'
.fld('Mã GV',inp('id',$s['magv']??'','text',$s?'readonly':''))
.fld('Họ Tên',inp('ten',$s['tengv']??''))
.fld('Giới tính',sel('gt',['Nam'=>'Nam','Nữ'=>'Nữ','Khác'=>'Khác'],$s['gioitinh']??'Nam'))
.fld('SĐT Liên hệ',inp('sdt',$s['sdt']??''))
.fld('Môn Dạy (cách nhau dấu phẩy)',inp('md',$s['monday']??''))
.fld('Email',inp('em',$s['email']??''))
.fld('Kinh nghiệm (năm)',inp('kn',$s['kinhnghiem']??''))
.fld('Lương',inp('lg',isset($s)?(float)$s['luong']:''))
.'</div><div class="acts">'
.($s?btn('Cập nhật','b-blue','sua').btn('Xóa','b-red','xoa').'<a class="btn b-gray" href="giaovien.php">Làm mới</a>':btn('Thêm mới','b-green','them'))
.'</div></form>';
tbl(['Mã GV','Họ Tên','Giới tính','SĐT','Email','Môn Dạy','Kinh nghiệm','Lương',''],$data);
foot();
