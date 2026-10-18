<?php
require 'mucluc.php';need(['qtv','ql','gv']);
$r=role();$ie=e(uid());
if($_SERVER['REQUEST_METHOD']=='POST'&&$r!='gv'){
    $id=e($_POST['id']??'');
    $v=[];
    foreach(['lg','th','ph'] as $k){$x=str_replace(',','',trim($_POST[$k]??''));$v[$k]=$x===''?0:$x;}
    if(!is_numeric($v['lg'])||!is_numeric($v['th'])||!is_numeric($v['ph']))flash('Lỗi định dạng số!','err');
    elseif(!one("SELECT 1 FROM giaovien WHERE magv='$id'"))flash('Chưa chọn giáo viên!','err');
    elseif(q("UPDATE giaovien SET luong=".(float)$v['lg'].",tienthuong=".(float)$v['th'].",tienphat=".(float)$v['ph']." WHERE magv='$id'"))flash('Cập nhật Lương/Thưởng/Phạt thành công!');
    go('tienluong.php');
}
$kw=trim($_GET['q']??'');
$w="WHERE 1=1";
if($r=='gv')$w.=" AND magv='$ie'";
if($kw!=='')$w.=" AND (magv LIKE '%".e($kw)."%' OR tengv LIKE '%".e($kw)."%')";
$data=[];
foreach(rows("SELECT magv,tengv,luong,tienthuong,tienphat FROM giaovien $w ORDER BY magv") as $x)
    $data[]=[$x['magv'],$x['tengv'],vnd($x['luong']),['c-orange',vnd($x['tienthuong'])],['c-red',vnd($x['tienphat'])],['c-green',vnd($x['luong']+$x['tienthuong']-$x['tienphat'])],$r!='gv'?['raw','<a href="?sel='.urlencode($x['magv']).'">Chọn</a>']:''];
$s=$r!='gv'&&isset($_GET['sel'])?one("SELECT * FROM giaovien WHERE magv='".e($_GET['sel'])."'"):null;
head($r=='gv'?'Xem Lương':'Quản Lý Lương');
if($r!='gv'){
    searchbar('Nhập Mã hoặc Tên GV để tìm kiếm...');
    echo '<form method="post" class="card"><h3>Cập nhật Lương & Thưởng Phạt'.($s?' — '.h($s['tengv']).' ('.h($s['magv']).')':' — chọn giáo viên trong bảng').'</h3><input type="hidden" name="id" value="'.h($s['magv']??'').'"><div class="grid">'
    .fld('Lương Cơ Bản',inp('lg',isset($s)?(float)$s['luong']:''))
    .fld('Tiền Thưởng (+)',inp('th',isset($s)?(float)$s['tienthuong']:''))
    .fld('Tiền Phạt (-)',inp('ph',isset($s)?(float)$s['tienphat']:''))
    .'</div><div class="acts">'.btn('Cập Nhật Lương','b-orange','luu').'<a class="btn b-gray" href="tienluong.php">Làm Mới</a></div></form>';
}
tbl(['Mã GV','Tên Giáo Viên','Lương Cơ Bản','Tiền Thưởng','Tiền Phạt','Thực Lãnh',''],$data);
foot();
