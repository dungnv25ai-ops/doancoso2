<?php
require 'mucluc.php';need();
$r=role();$ie=e(uid());
function sc($k){$v=str_replace(',','.',trim($_POST[$k]??''));if($v==='')return 'NULL';if(!is_numeric($v)||$v<0||$v>10)return false;return (float)$v;}
function lopOk($id){global $r,$ie;return one("SELECT id_lop,tenlop,monhoc FROM lophoc WHERE id_lop=".(int)$id.($r=='gv'?" AND magv='$ie'":''));}
if($_SERVER['REQUEST_METHOD']=='POST'&&$r!='sv'){
    $lop=lopOk($_POST['lop']??0);$sv=e($_POST['sv']??'');
    $back='quanlydiem.php?lop='.(int)($_POST['lop']??0).'&sv='.urlencode($_POST['sv']??'');
    if(!$lop||!one("SELECT 1 FROM dangky_lop WHERE id_lop={$lop['id_lop']} AND masv='$sv' AND trangthai=1"))flash('Dữ liệu không hợp lệ!','err');
    else{
        $mon=e($lop['monhoc']);
        $ex=one("SELECT id_diem FROM diem WHERE masv='$sv' AND monhoc='$mon'");
        if(($_POST['act']??'')=='xoa'){
            if($ex&&q("DELETE FROM diem WHERE id_diem={$ex['id_diem']}"))flash('Xóa bảng điểm thành công!');
        }else{
            $v=[];foreach(['tx1','tx2','tx3','gk','ck'] as $k)$v[$k]=sc($k);
            if(in_array(false,$v,true))flash('Điểm phải là số từ 0 đến 10!','err');
            elseif($ex){
                if(q("UPDATE diem SET diem_tx1={$v['tx1']},diem_tx2={$v['tx2']},diem_tx3={$v['tx3']},diem_gk={$v['gk']},diem_ck={$v['ck']} WHERE id_diem={$ex['id_diem']}"))flash('Cập nhật điểm thành công!');
            }elseif(q("INSERT INTO diem (masv,monhoc,diem_tx1,diem_tx2,diem_tx3,diem_gk,diem_ck) VALUES ('$sv','$mon',{$v['tx1']},{$v['tx2']},{$v['tx3']},{$v['gk']},{$v['ck']})"))flash('Tạo bảng điểm và thêm điểm thành công!');
        }
    }
    go($back);
}
$kw=trim($_GET['q']??'');
$w="WHERE dk.trangthai=1";
if($r=='gv')$w.=" AND l.magv='$ie'";
elseif($r=='sv')$w.=" AND dk.masv='$ie'";
if($kw!=='')$w.=" AND (s.masv LIKE '%".e($kw)."%' OR s.ten LIKE '%".e($kw)."%')";
$data=[];
foreach(rows("SELECT DISTINCT s.masv,s.ten,l.id_lop,l.tenlop,l.monhoc,g.tengv,d.diem_tx1,d.diem_tx2,d.diem_tx3,d.diem_gk,d.diem_ck FROM dangky_lop dk JOIN sinhvien s ON dk.masv=s.masv JOIN lophoc l ON dk.id_lop=l.id_lop JOIN giaovien g ON l.magv=g.magv LEFT JOIN diem d ON d.masv=dk.masv AND d.monhoc=l.monhoc $w ORDER BY l.tenlop,s.masv") as $x){
    $sum=0;$he=0;
    foreach([['diem_tx1',1],['diem_tx2',1],['diem_tx3',1],['diem_gk',2],['diem_ck',3]] as [$k,$m])if($x[$k]!==null){$sum+=$x[$k]*$m;$he+=$m;}
    $dtb='-';$xl=['c-gray','-'];
    if($he){
        $d=$sum/$he;$dtb=number_format($d,2);
        if($d>=8.5)$xl=['c-green','A (Giỏi)'];elseif($d>=7)$xl=['c-blue','B (Khá)'];elseif($d>=5.5)$xl=['c-amber','C (TB)'];elseif($d>=4)$xl=['c-orange','D (Yếu)'];else $xl=['c-red','F (Trượt)'];
    }
    $row=[$x['ten'],$x['tenlop'],$x['monhoc'],$x['tengv'],nv($x['diem_tx1']),nv($x['diem_tx2']),nv($x['diem_tx3']),nv($x['diem_gk']),nv($x['diem_ck']),$dtb,$xl];
    if($r!='sv')$row[]=['raw','<a href="?lop='.$x['id_lop'].'&sv='.urlencode($x['masv']).'">Chọn</a>'];
    $data[]=$row;
}
head($r=='sv'?'Điểm Số':'Quản Lý Điểm');
if($r!='sv'){
    $lops=rows("SELECT id_lop,tenlop FROM lophoc".($r=='gv'?" WHERE magv='$ie'":'')." ORDER BY tenlop");
    $li=(int)($_GET['lop']??($lops[0]['id_lop']??0));
    $lop=$li?lopOk($li):null;
    $svs=$lop?rows("SELECT s.masv,s.ten FROM dangky_lop dk JOIN sinhvien s ON dk.masv=s.masv WHERE dk.id_lop=$li AND dk.trangthai=1 ORDER BY s.masv"):[];
    $sv=$_GET['sv']??($svs[0]['masv']??'');
    $cur=$lop&&$sv!==''?one("SELECT * FROM diem WHERE masv='".e($sv)."' AND monhoc='".e($lop['monhoc'])."'"):null;
    $lo=[];foreach($lops as $x)$lo[$x['id_lop']]=$x['tenlop'];
    $so=[];foreach($svs as $x)$so[$x['masv']]=$x['ten'].' ('.$x['masv'].')';
    if($r!='gv')searchbar('Nhập Mã hoặc Tên SV để tìm kiếm...');
    echo '<form method="get" class="card filter">'.fld('Lớp học',sel('lop',$lo,$li)).fld('Sinh viên',sel('sv',$so,$sv)).'<button class="btn b-gray">Tải bảng điểm</button></form>';
    echo '<form method="post" class="card"><h3>Cập nhật Bảng Điểm'.($lop?' — '.h($lop['tenlop']).' / '.h($lop['monhoc']):'').'</h3><input type="hidden" name="lop" value="'.$li.'"><input type="hidden" name="sv" value="'.h($sv).'"><div class="grid">'
    .fld('Điểm TX 1',inp('tx1',$cur['diem_tx1']??''))
    .fld('Điểm TX 2',inp('tx2',$cur['diem_tx2']??''))
    .fld('Điểm TX 3',inp('tx3',$cur['diem_tx3']??''))
    .fld('Giữa kỳ (x2)',inp('gk',$cur['diem_gk']??''))
    .fld('Cuối kỳ (x3)',inp('ck',$cur['diem_ck']??''))
    .'</div><p style="color:#c0392b;font-size:12px;font-style:italic">(*) Bỏ trống nếu chưa có điểm, nhập 0 nếu bị 0 điểm.</p><div class="acts">'
    .btn($cur?'Sửa Điểm':'Thêm Điểm',$cur?'b-blue':'b-green','luu').($cur?btn('Xóa Điểm','b-red','xoa'):'').'<a class="btn b-gray" href="quanlydiem.php">Làm Mới</a></div></form>';
}
tbl(['Sinh Viên','Lớp','Môn Học','GV Dạy','TX 1','TX 2','TX 3','Giữa Kỳ','Cuối Kỳ','Điểm TB','Xếp Loại']+($r!='sv'?[11=>'']:[]),$data);
foot();
