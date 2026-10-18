<?php
require_once __DIR__.'/db.php';

function menu(){
    $r=role();
    $tq=['TỔNG QUAN',[['🏠','Trang chủ','trangchu.php'],['👤','Thông tin cá nhân','thongtincanhan.php']]];
    if($r=='qtv'||$r=='ql')return [$tq,
        ['QUẢN LÝ',[['🔑','Quản lý tài khoản','quanlytaikhoan.php'],['📅','Thời khóa biểu','thoikhoabieu.php'],['📚','Khóa học','khoahoc.php'],['👨‍🏫','Giáo viên','giaovien.php'],['🎓','Sinh viên','sinhvien.php'],['🏫','Lớp học','lophoc.php']]],
        ['HỌC VỤ',[['📝','Quản lý điểm','quanlydiem.php'],['💰','Quản lý học phí','hocphi.php'],['💵','Quản lý lương','tienluong.php']]],
        ['TIỆN ÍCH',[['🔔','Thông báo','thongbao.php'],['📊','Thống kê','thongke.php']]]];
    if($r=='gv')return [
        ['TỔNG QUAN',[['🏠','Trang chủ','trangchu.php'],['👤','Thông tin cá nhân','thongtincanhan.php'],['🔑','Quản lý tài khoản','quanlytaikhoan.php']]],
        ['LỚP & LỊCH',[['🏫','Lớp sinh hoạt','lopsinhhoat.php'],['📅','Lịch dạy','thoikhoabieu.php'],['📖','Lớp học','lophoc.php']]],
        ['HỌC VỤ',[['📝','Quản lý điểm','quanlydiem.php'],['💰','Quản lý học phí','hocphi.php'],['💵','Xem lương','tienluong.php']]],
        ['TIỆN ÍCH',[['🔔','Thông báo','thongbao.php']]]];
    return [$tq,
        ['HỌC TẬP',[['✏️','Đăng ký lớp','dangkylop.php'],['🏫','Lớp sinh hoạt','lopsinhhoat.php'],['📅','Thời khóa biểu','thoikhoabieu.php'],['📝','Điểm số','quanlydiem.php'],['💰','Xem học phí','hocphi.php']]],
        ['TIỆN ÍCH',[['🔔','Thông báo','thongbao.php']]]];
}

function roleName(){return ['qtv'=>'Quản trị viên','ql'=>'Quản lý','gv'=>'Giáo viên','sv'=>'Sinh viên'][role()]??role();}

function head($title){
    $cur=basename($_SERVER['SCRIPT_NAME']);
    $f=$_SESSION['flash']??null;unset($_SESSION['flash']);
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.h($title).' - Education Center</title><link rel="stylesheet" href="style.css"></head><body>';
    echo '<header class="top"><div class="logo"><i>🎓</i><div><b>HỆ THỐNG QUẢN LÝ</b><small>Education Center</small></div></div>';
    echo '<details class="user"><summary><span class="av">'.h(mb_strtoupper(mb_substr($_SESSION['ten'],0,1))).'</span>'.h($_SESSION['ten']).' ▾</summary><div><a href="dangxuat.php">Đăng xuất</a></div></details></header>';
    echo '<div class="wrap"><nav class="side">';
    foreach(menu() as [$sec,$items]){
        echo '<h6>'.h($sec).'</h6>';
        foreach($items as [$ic,$nm,$fi])echo '<a href="'.$fi.'"'.($fi==$cur?' class="on"':'').'><span>'.$ic.'</span>'.h($nm).'</a>';
    }
    echo '</nav><main>';
    if($f)echo '<div class="flash '.($f[1]=='err'?'err':'ok').'">'.h($f[0]).'</div>';
    echo '<h2 class="pt">'.h($title).'</h2>';
}

function foot(){echo '</main></div></body></html>';}

function tbl($heads,$data,$empty='Không có dữ liệu'){
    echo '<div class="tw"><table><tr>';
    foreach($heads as $x)echo '<th>'.h($x).'</th>';
    echo '</tr>';
    if(!$data)echo '<tr><td colspan="'.count($heads).'" class="empty">'.h($empty).'</td></tr>';
    foreach($data as $row){
        echo '<tr>';
        foreach($row as $c){
            if(is_array($c)){
                if($c[0]==='raw')echo '<td>'.$c[1].'</td>';
                else echo '<td class="'.h($c[0]).'">'.h($c[1]).'</td>';
            }else echo '<td>'.h($c).'</td>';
        }
        echo '</tr>';
    }
    echo '</table></div>';
}

function inp($n,$v='',$t='text',$x=''){return '<input type="'.$t.'" name="'.$n.'" value="'.h($v).'" '.$x.'>';}
function sel($n,$o,$c='',$x=''){
    $s='<select name="'.$n.'" '.$x.'>';
    foreach($o as $k=>$l)$s.='<option value="'.h($k).'"'.((string)$k===(string)$c?' selected':'').'>'.h($l).'</option>';
    return $s.'</select>';
}
function btn($t,$c='b-blue',$v='',$n='act'){return '<button class="btn '.$c.'" name="'.$n.'" value="'.h($v).'">'.h($t).'</button>';}
function fld($l,$html,$w=''){return '<label class="f '.$w.'"><span>'.h($l).'</span>'.$html.'</label>';}
function searchbar($ph){echo '<form method="get" class="search"><input name="q" value="'.h($_GET['q']??'').'" placeholder="'.h($ph).'"><button class="btn b-green">Tìm kiếm</button></form>';}
