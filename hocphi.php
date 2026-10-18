<?php
require 'mucluc.php';need();
$r=role();$ie=e(uid());
function lopOk($id){global $r,$ie;return one("SELECT id_lop,tenlop,monhoc FROM lophoc WHERE id_lop=".(int)$id.($r=='gv'?" AND magv='$ie'":''));}
if($_SERVER['REQUEST_METHOD']=='POST'&&$r!='sv'){
    $lop=lopOk($_POST['lop']??0);$sv=e($_POST['sv']??'');
    $back='hocphi.php?lop='.(int)($_POST['lop']??0).'&sv='.urlencode($_POST['sv']??'');
    if(!$lop||!one("SELECT 1 FROM dangky_lop WHERE id_lop={$lop['id_lop']} AND masv='$sv' AND trangthai=1"))flash('Dữ liệu không hợp lệ!','err');
    else{
        $dn=str_replace(',','',trim($_POST['dn']??''));if($dn==='')$dn=0;
        if(!is_numeric($dn))flash('Lỗi định dạng số!','err');
        else{
            $tt=e($_POST['tt']??'Chưa nộp');$mon=e($lop['monhoc']);
            if(q("UPDATE sv_monhoc SET danop=".(float)$dn.",trangthai_hp='$tt' WHERE masv='$sv' AND tenkh='$mon'"))flash('Cập nhật học phí thành công!');
        }
    }
    go($back);
}
$kw=trim($_GET['q']??'');
$w="WHERE dk.trangthai=1";
if($r=='gv')$w.=" AND l.magv='$ie'";
elseif($r=='sv')$w.=" AND sm.masv='$ie'";
if($kw!=='')$w.=" AND (s.masv LIKE '%".e($kw)."%' OR s.ten LIKE '%".e($kw)."%')";
$data=[];
foreach(rows("SELECT sm.id_dk,s.masv,s.ten,l.id_lop,l.tenlop,l.monhoc,k.hocphi,sm.danop,sm.trangthai_hp FROM sv_monhoc sm JOIN sinhvien s ON sm.masv=s.masv JOIN khoahoc k ON sm.tenkh=k.tenkh JOIN dangky_lop dk ON dk.masv=s.masv JOIN lophoc l ON dk.id_lop=l.id_lop AND l.monhoc=sm.tenkh $w ORDER BY l.tenlop,s.masv") as $x){
    $no=$x['hocphi']-$x['danop'];
    $ns=$no>0?['c-red','Nợ: '.vnd($no)]:($no<0?['c-blue','Thừa: '.vnd(-$no)]:['c-gray','0']);
    $t=$x['trangthai_hp'];
    $ts=[$t=='Đã nộp đủ'?'c-green':($t=='Nộp thiếu'?'c-amber':($t=='Nộp thừa'?'c-blue':'c-red')),$t];
    $row=[$x['ten'],$x['tenlop'],$x['monhoc'],vnd($x['hocphi']),vnd($x['danop']),$ns,$ts];
    if($r!='sv')$row[]=['raw','<a href="?lop='.$x['id_lop'].'&sv='.urlencode($x['masv']).'">Chọn</a>'];
    $data[]=$row;
}
head($r=='sv'?'Xem Học Phí':'Quản Lý Học Phí');
if($r!='sv'){
    $lops=rows("SELECT id_lop,tenlop FROM lophoc".($r=='gv'?" WHERE magv='$ie'":'')." ORDER BY tenlop");
    $li=(int)($_GET['lop']??($lops[0]['id_lop']??0));
    $lop=$li?lopOk($li):null;
    $svs=$lop?rows("SELECT s.masv,s.ten FROM dangky_lop dk JOIN sinhvien s ON dk.masv=s.masv WHERE dk.id_lop=$li AND dk.trangthai=1 ORDER BY s.masv"):[];
    $sv=$_GET['sv']??($svs[0]['masv']??'');
    $cur=$lop&&$sv!==''?one("SELECT * FROM sv_monhoc WHERE masv='".e($sv)."' AND tenkh='".e($lop['monhoc'])."'"):null;
    $kh=$lop?one("SELECT hocphi FROM khoahoc WHERE tenkh='".e($lop['monhoc'])."'"):null;
    $lo=[];foreach($lops as $x)$lo[$x['id_lop']]=$x['tenlop'];
    $so=[];foreach($svs as $x)$so[$x['masv']]=$x['ten'].' ('.$x['masv'].')';
    $tts=['Chưa nộp'=>'Chưa nộp','Nộp thiếu'=>'Nộp thiếu','Đã nộp đủ'=>'Đã nộp đủ','Nộp thừa'=>'Nộp thừa'];
    if($r!='gv')searchbar('Nhập Mã hoặc Tên SV để tìm kiếm...');
    echo '<form method="get" class="card filter">'.fld('Lớp học',sel('lop',$lo,$li)).fld('Sinh viên',sel('sv',$so,$sv)).'<button class="btn b-gray">Tải học phí</button></form>';
    echo '<form method="post" class="card"><h3>Cập nhật Học Phí</h3><input type="hidden" name="lop" value="'.$li.'"><input type="hidden" name="sv" value="'.h($sv).'"><div class="grid">'
    .fld('Học phí khóa',inp('x',$kh?vnd($kh['hocphi']):'','text','readonly'))
    .fld('Số tiền đã nộp',inp('dn',$cur?(float)$cur['danop']:''))
    .fld('Trạng thái',sel('tt',$tts,$cur['trangthai_hp']??'Chưa nộp'))
    .'</div><div class="acts">'.btn('Cập Nhật Học Phí','b-blue','luu').'<a class="btn b-gray" href="hocphi.php">Làm Mới</a></div></form>';
}
tbl(['Sinh Viên','Lớp','Môn Học','Học Phí Khóa','Đã Nộp','Còn Nợ / Thừa','Trạng Thái']+($r!='sv'?[7=>'']:[]),$data);
foot();
