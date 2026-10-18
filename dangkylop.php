<?php
require 'mucluc.php';need(['sv']);
$me=uid();$ie=e($me);
if($_SERVER['REQUEST_METHOD']=='POST'){
    $id=(int)($_POST['id']??0);
    $lop=one("SELECT * FROM lophoc WHERE id_lop=$id");
    if(!$lop)flash('Lớp không tồn tại!','err');
    elseif(one("SELECT 1 FROM dangky_lop WHERE masv='$ie' AND id_lop=$id"))flash('Bạn đã đăng ký lớp này rồi!','err');
    else{
        $t=(int)$lop['tudo'];$mon=e($lop['monhoc']);
        q("INSERT INTO dangky_lop (masv,id_lop,trangthai) VALUES ('$ie',$id,$t)");
        if($t==1){
            if(!one("SELECT 1 FROM diem WHERE masv='$ie' AND monhoc='$mon'"))q("INSERT INTO diem (masv,monhoc) VALUES ('$ie','$mon')");
            if(!one("SELECT 1 FROM sv_monhoc WHERE masv='$ie' AND tenkh='$mon'"))q("INSERT INTO sv_monhoc (masv,tenkh,danop) VALUES ('$ie','$mon',0)");
            flash('Đăng ký thành công! Lịch học và Bảng điểm đã được cập nhật.');
        }else flash('Gửi yêu cầu thành công! Vui lòng chờ Giáo viên duyệt.');
    }
    go('dangkylop.php');
}
$data=[];
$sql="SELECT l.*,g.tengv,d.trangthai dk,(SELECT GROUP_CONCAT(CONCAT(thu,' (Ca ',ca,' - ',phong,')') ORDER BY id_lich SEPARATOR ' | ') FROM lophoc_lich WHERE id_lop=l.id_lop) lich FROM lophoc l LEFT JOIN giaovien g ON l.magv=g.magv LEFT JOIN dangky_lop d ON d.id_lop=l.id_lop AND d.masv='$ie' ORDER BY l.id_lop DESC";
foreach(rows($sql) as $x){
    if($x['dk']===null)$act=['raw','<form method="post"><input type="hidden" name="id" value="'.$x['id_lop'].'">'.btn('Đăng Ký','b-green sm','dk').'</form>'];
    else $act=$x['dk']==1?['c-green','Đã đăng ký']:['c-amber','Chờ duyệt'];
    $data[]=[$x['tenlop'],$x['monhoc'],nv($x['tengv']),$x['ngaybatdau']?$x['ngaybatdau'].' đến '.$x['ngayketthuc']:'Chưa có',nv($x['lich'],''),$x['tudo']==1?'Vào tự do':'Cần duyệt',$act];
}
head('ĐĂNG KÝ VÀO LỚP HỌC');
tbl(['Tên Lớp','Môn Học','Giáo Viên','Bắt Đầu - Kết Thúc','Lịch Học','Hình Thức',''],$data);
foot();
