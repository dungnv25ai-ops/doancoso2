<?php
require 'mucluc.php';need(['qtv','ql','gv']);
$r=role();$me=uid();
$cre=['qtv'=>['ql'=>'Quản lý','gv'=>'Giáo viên','sv'=>'Sinh viên'],'ql'=>['gv'=>'Giáo viên','sv'=>'Sinh viên'],'gv'=>['sv'=>'Sinh viên']][$r];
function mx($sql){$x=one($sql);return $x?(int)array_values($x)[0]:0;}
function quyen($id){
    global $cre,$r,$me;
    $t=one("SELECT * FROM taikhoan WHERE tendangnhap='".e($id)."'");
    if(!$t||!isset($cre[$t['vaitro']]))return null;
    if($r=='gv'&&strpos($t['tendangnhap'],$me)!==0)return null;
    return $t;
}
if($_SERVER['REQUEST_METHOD']=='POST'){
    $a=$_POST['act']??'';$ten=trim($_POST['ten']??'');$mk=trim($_POST['mk']??'');$back='quanlytaikhoan.php';
    if($a=='them'){
        $vt=$_POST['vt']??'';
        if(!isset($cre[$vt])||$ten==''||$mk=='')flash('Nhập đủ Mật khẩu, Tên!','err');
        else{
            $id=null;
            if($vt=='gv')$id=sprintf('%03dgv',mx("SELECT MAX(CAST(LEFT(magv,3) AS UNSIGNED)) FROM giaovien")+1);
            elseif($vt=='sv'){
                $p=$r=='gv'?$me:($_POST['gvcn']??'');
                $g=one("SELECT magv FROM giaovien WHERE magv='".e($p)."'");
                if($g){
                    $p=$g['magv'];
                    $id=$p.sprintf('%03d',mx("SELECT MAX(CAST(SUBSTRING(masv,".(strlen($p)+1).") AS UNSIGNED)) FROM sinhvien WHERE masv LIKE '".e($p)."%'")+1);
                    $back.='?loc='.urlencode($p);
                }
            }else $id=sprintf('ql%02d',mx("SELECT MAX(CAST(SUBSTRING(tendangnhap,3) AS UNSIGNED)) FROM taikhoan WHERE vaitro='ql'")+1);
            if(!$id)flash('Chưa chọn GVCN hợp lệ!','err');
            else{
                $i=e($id);$tn=e($ten);
                if(q("INSERT INTO taikhoan VALUES ('$i','".e($mk)."','$tn','$vt')")){
                    if($vt=='gv')q("INSERT INTO giaovien (magv,tengv) VALUES ('$i','$tn')");
                    elseif($vt=='sv')q("INSERT INTO sinhvien (masv,ten) VALUES ('$i','$tn')");
                    flash('Tạo thành công! ID đăng nhập là: '.$id);
                }
            }
        }
    }elseif($a=='sua'){
        $t=quyen($_POST['id']??'');
        if(!$t)flash('Không có quyền sửa tài khoản này!','err');
        else{
            $i=e($t['tendangnhap']);$tn=e($ten);
            if(q("UPDATE taikhoan SET matkhau='".e($mk)."',ten='$tn' WHERE tendangnhap='$i'")){
                if($t['vaitro']=='gv')q("UPDATE giaovien SET tengv='$tn' WHERE magv='$i'");
                elseif($t['vaitro']=='sv')q("UPDATE sinhvien SET ten='$tn' WHERE masv='$i'");
                flash('Sửa thành công!');
            }
        }
    }elseif($a=='xoa'){
        $t=quyen($_POST['id']??'');
        if(!$t)flash('Không có quyền xóa tài khoản này!','err');
        elseif($t['tendangnhap']=='admin'||$t['tendangnhap']==$me)flash('Không thể tự xóa tài khoản của chính mình hoặc admin!','err');
        elseif(q("DELETE FROM taikhoan WHERE tendangnhap='".e($t['tendangnhap'])."'"))flash('Đã xóa tài khoản và hồ sơ liên quan!');
    }
    go($back);
}
$loc=$_GET['loc']??'ALL';
if($r=='gv'){$loc=$me;$sql="SELECT * FROM taikhoan WHERE vaitro='sv' AND tendangnhap LIKE '".e($me)."%'";}
elseif($loc=='ALL')$sql="SELECT * FROM taikhoan WHERE vaitro IN (".($r=='qtv'?"'ql','gv'":"'gv'").")";
else $sql="SELECT * FROM taikhoan WHERE vaitro='sv' AND tendangnhap LIKE '".e($loc)."%'";
$gvs=rows("SELECT magv,tengv FROM giaovien ORDER BY magv");
$opt=['ALL'=>'-- Cán bộ, Quản lý, Giáo viên --'];
foreach($gvs as $g)$opt[$g['magv']]='Lớp sinh hoạt của '.$g['tengv'];
$gvo=[];foreach($gvs as $g)$gvo[$g['magv']]=$g['tengv'].' ('.$g['magv'].')';
$s=isset($_GET['sel'])?quyen($_GET['sel']):null;
$data=[];
foreach(rows($sql.' ORDER BY tendangnhap') as $x)$data[]=[$x['tendangnhap'],$x['matkhau'],$x['ten'],$x['vaitro'],['raw','<a href="?loc='.urlencode($loc).'&sel='.urlencode($x['tendangnhap']).'">Chọn</a>']];
head('Cấp & Quản lý Tài khoản');
if($r!='gv')echo '<form method="get" class="card filter">'.fld('Hiển thị danh sách',sel('loc',$opt,$loc)).'<button class="btn b-blue">Xem</button></form>';
echo '<form method="post" class="card"><h3>Cấp & Quản lý Tài khoản <small style="font-weight:normal;color:#6c757d">— ID tài khoản được tạo tự động</small></h3><div class="grid">'
.fld('Họ và tên',inp('ten',$s['ten']??''))
.fld('Mật khẩu',inp('mk',$s['matkhau']??''))
.fld('Vai trò cấp',sel('vt',$cre,$s['vaitro']??'',$s?'disabled':''));
if($r!='gv')echo fld('Xếp vào Lớp (GVCN) — dùng khi tạo Sinh viên',sel('gvcn',$gvo,$loc!='ALL'?$loc:'',$s?'disabled':''));
echo '</div>';
if($s)echo '<input type="hidden" name="id" value="'.h($s['tendangnhap']).'">';
echo '<div class="acts">'.($s?btn('Đổi Tên/MK','b-blue','sua').btn('Xóa TK','b-red','xoa').'<a class="btn b-gray" href="quanlytaikhoan.php?loc='.urlencode($loc).'">Làm mới</a>':btn('Tạo TK Mới','b-green','them')).'</div></form>';
tbl(['ID Đăng Nhập','Mật khẩu','Họ tên','Vai trò',''],$data);
foot();
