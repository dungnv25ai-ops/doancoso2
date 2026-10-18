<?php
require 'mucluc.php';need(['qtv','ql','gv']);
$r=role();$me=uid();$ie=e($me);
$tab=$_GET['tab']??'lop';
if($tab=='duyet'&&$r!='gv')$tab='lop';
if($r=='gv'){
    $g=one("SELECT monday FROM giaovien WHERE magv='$ie'");
    $mons=array_values(array_filter(array_map('trim',explode(',',$g['monday']??''))));
}else $mons=array_column(rows("SELECT tenkh FROM khoahoc ORDER BY tenkh"),'tenkh');
$thus=['Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7','Chủ nhật'];
$phongs=['Phòng 1','Phòng 2','Phòng 3','Phòng 4'];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $a=$_POST['act']??'';$back='lophoc.php';
    if($a=='tao'){
        $mon=$_POST['mon']??'';$back.='?mon='.urlencode($mon);
        $ten=trim($_POST['ten']??'');$bd=$_POST['bd']??'';
        $me2=e($mon);
        $kh=in_array($mon,$mons)?one("SELECT sobuoihoc,sotuanhoc FROM khoahoc WHERE tenkh='$me2'"):null;
        $magv=$r=='gv'?$me:($_POST['gv']??'');
        $gvOk=one("SELECT magv FROM giaovien WHERE magv='".e($magv)."'");
        if($ten==''||!$kh||!$gvOk||!strtotime($bd))flash('Nhập đủ Tên lớp, Môn học, Giáo viên và Ngày bắt đầu!','err');
        elseif((int)$kh['sobuoihoc']==0)flash('Chưa có thông tin Khóa học!','err');
        else{
            $sb=(int)$kh['sobuoihoc'];$bdT=strtotime($bd);
            $ngBD=date('Y-m-d',$bdT);$ngKT=date('Y-m-d',strtotime('+'.(int)$kh['sotuanhoc'].' weeks',$bdT));
            $L=[];$loi='';
            for($i=0;$i<$sb;$i++){
                $L[$i]=[$_POST['thu'][$i]??'',$_POST['ca'][$i]??'',$_POST['phong'][$i]??''];
                if(!in_array($L[$i][0],$thus)||!in_array($L[$i][1],['1','2','3','4'])||!in_array($L[$i][2],$phongs))$loi='Lịch học không hợp lệ!';
            }
            for($i=0;$i<$sb&&!$loi;$i++)for($j=$i+1;$j<$sb;$j++)if($L[$i]==$L[$j])$loi='Lỗi: Lịch học các buổi đang bị trùng nhau!';
            for($i=0;$i<$sb&&!$loi;$i++){
                [$t,$c,$p]=array_map('e',$L[$i]);$mg=e($gvOk['magv']);
                $ov="('$ngBD'<=l.ngayketthuc AND '$ngKT'>=l.ngaybatdau)";
                $x=one("SELECT l.tenlop FROM lophoc_lich ll JOIN lophoc l ON ll.id_lop=l.id_lop WHERE ll.thu='$t' AND ll.ca='$c' AND ll.phong='$p' AND $ov");
                if($x){$loi='Trùng lịch: Phòng này đã có lớp '.$x['tenlop'].' đang sử dụng!';break;}
                $x=one("SELECT l.tenlop FROM lophoc_lich ll JOIN lophoc l ON ll.id_lop=l.id_lop WHERE ll.thu='$t' AND ll.ca='$c' AND l.magv='$mg' AND $ov");
                if($x){$loi='Trùng lịch: Giáo viên này đã có lịch dạy lớp '.$x['tenlop'].' vào thời gian này!';break;}
            }
            if($loi)flash($loi,'err');
            else{
                $td=($_POST['ht']??'tudo')=='tudo'?1:0;
                if(q("INSERT INTO lophoc (tenlop,monhoc,magv,ngaybatdau,ngayketthuc,tudo) VALUES ('".e($ten)."','$me2','".e($gvOk['magv'])."','$ngBD','$ngKT',$td)")){
                    $id=$db->insert_id;
                    foreach($L as $l)q("INSERT INTO lophoc_lich (id_lop,thu,ca,phong) VALUES ($id,'".e($l[0])."','".e($l[1])."','".e($l[2])."')");
                    flash('Tạo lớp học thành công!');
                }
            }
        }
    }elseif($a=='xoa'){
        $id=(int)($_POST['id']??0);
        if(q("DELETE FROM lophoc WHERE id_lop=$id".($r=='gv'?" AND magv='$ie'":'')))flash('Đã xóa lớp học!');
    }elseif($a=='duyet'||$a=='tuchoi'){
        $back='lophoc.php?tab=duyet';
        $id=(int)($_POST['id']??0);
        $x=$r=='gv'?one("SELECT dk.masv,l.monhoc FROM dangky_lop dk JOIN lophoc l ON dk.id_lop=l.id_lop WHERE dk.id_dk_lop=$id AND l.magv='$ie' AND dk.trangthai=0"):null;
        if(!$x)flash('Không có quyền xử lý yêu cầu này!','err');
        elseif($a=='tuchoi'){q("DELETE FROM dangky_lop WHERE id_dk_lop=$id");flash('Đã từ chối yêu cầu!');}
        else{
            $sv=e($x['masv']);$mn=e($x['monhoc']);
            q("UPDATE dangky_lop SET trangthai=1 WHERE id_dk_lop=$id");
            if(!one("SELECT 1 FROM diem WHERE masv='$sv' AND monhoc='$mn'"))q("INSERT INTO diem (masv,monhoc) VALUES ('$sv','$mn')");
            if(!one("SELECT 1 FROM sv_monhoc WHERE masv='$sv' AND tenkh='$mn'"))q("INSERT INTO sv_monhoc (masv,tenkh,danop) VALUES ('$sv','$mn',0)");
            flash('Đã duyệt sinh viên vào lớp!');
        }
    }
    go($back);
}

head('Lớp Học');
if($r=='gv')echo '<div class="tabs"><a href="lophoc.php"'.($tab=='lop'?' class="on"':'').'>Quản Lý & Tạo Lớp Học</a><a href="lophoc.php?tab=duyet"'.($tab=='duyet'?' class="on"':'').'>Duyệt Sinh Viên</a></div>';

if($tab=='duyet'){
    $data=[];
    foreach(rows("SELECT dk.id_dk_lop,s.masv,s.ten,l.tenlop,l.monhoc FROM dangky_lop dk JOIN sinhvien s ON dk.masv=s.masv JOIN lophoc l ON dk.id_lop=l.id_lop WHERE dk.trangthai=0 AND l.magv='$ie' ORDER BY dk.id_dk_lop") as $x)
        $data[]=[$x['masv'],$x['ten'],$x['tenlop'],$x['monhoc'],['raw','<form method="post" style="display:flex;gap:6px"><input type="hidden" name="id" value="'.$x['id_dk_lop'].'">'.btn('Duyệt','b-green sm','duyet').btn('Từ chối','b-red sm','tuchoi').'</form>']];
    tbl(['Mã SV','Tên Sinh Viên','Lớp Đăng Ký','Môn Học',''],$data,'Không có yêu cầu nào đang chờ duyệt');
    foot();exit;
}

$mon=$_GET['mon']??($mons[0]??'');
if(!in_array($mon,$mons))$mon=$mons[0]??'';
$kh=$mon!==''?one("SELECT * FROM khoahoc WHERE tenkh='".e($mon)."'"):null;
$sb=$kh?(int)$kh['sobuoihoc']:0;
$gvs=[];
if($r=='gv'){$x=one("SELECT magv,tengv FROM giaovien WHERE magv='$ie'");if($x)$gvs[$x['magv']]=$x['tengv'].' ('.$x['magv'].')';}
elseif($mon!=='')foreach(rows("SELECT magv,tengv FROM giaovien WHERE CONCAT(',',REPLACE(monday,', ',','),',') LIKE '%,".e($mon).",%'") as $x)$gvs[$x['magv']]=$x['tengv'].' ('.$x['magv'].')';
$mo=array_combine($mons,$mons)?:[];

echo '<form method="get" class="card filter">'.fld('Chọn môn học để tạo lớp',sel('mon',$mo,$mon)).'<button class="btn b-gray">Chọn môn</button></form>';
echo '<form method="post" class="card"><h3>Tạo Lớp Mới'.($kh?' — '.h($mon).' ('.(int)$kh['sotuanhoc'].' tuần, '.$sb.' buổi/tuần)':'').'</h3><input type="hidden" name="mon" value="'.h($mon).'"><div class="grid">'
.fld('Tên lớp',inp('ten'))
.fld('Giáo viên',sel('gv',$gvs))
.fld('Ngày bắt đầu',inp('bd',date('Y-m-d'),'date'))
.fld('Hình thức','<div class="rad"><label><input type="radio" name="ht" value="tudo" checked> Vào tự do</label><label><input type="radio" name="ht" value="duyet"> Cần duyệt</label></div>')
.'</div>';
for($i=0;$i<$sb;$i++){
    echo '<div class="grid" style="margin-top:10px">'
    .fld('Buổi '.($i+1).' → Thứ',sel('thu[]',array_combine($thus,$thus)))
    .fld('Ca',sel('ca[]',['1'=>'1','2'=>'2','3'=>'3','4'=>'4']))
    .fld('Phòng',sel('phong[]',array_combine($phongs,$phongs))).'</div>';
}
echo '<div class="acts">'.btn('Tạo Lớp Mới','b-green','tao').'</div></form>';

$data=[];
$sql="SELECT l.*,g.tengv,(SELECT GROUP_CONCAT(CONCAT(thu,' (Ca ',ca,' - ',phong,')') ORDER BY id_lich SEPARATOR ' | ') FROM lophoc_lich WHERE id_lop=l.id_lop) lich FROM lophoc l LEFT JOIN giaovien g ON l.magv=g.magv ORDER BY l.id_lop DESC";
foreach(rows($sql) as $x){
    $xoa=($r!='gv'||$x['magv']==$me)?['raw','<form method="post"><input type="hidden" name="id" value="'.$x['id_lop'].'">'.btn('Xóa lớp','b-red sm','xoa').'</form>']:'';
    $data[]=[$x['tenlop'],$x['monhoc'],nv($x['tengv'],'Chưa xếp GV'),nv($x['ngaybatdau'],'Chưa có'),nv($x['ngayketthuc'],'Chưa có'),$x['tudo']==1?'Tự do':'Duyệt',nv($x['lich'],''),$xoa];
}
tbl(['Tên Lớp','Môn','GV','Bắt Đầu','Kết Thúc','Hình Thức','Lịch Học',''],$data);
foot();
