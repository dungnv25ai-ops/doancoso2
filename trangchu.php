<?php
require 'mucluc.php';need();
$r=role();$i=e(uid());
$sv=one("SELECT COUNT(*) c FROM sinhvien")['c'];
$gv=one("SELECT COUNT(*) c FROM giaovien")['c'];
$lop=one("SELECT COUNT(*) c FROM lophoc")['c'];
$thus=[1=>'Thứ 2',2=>'Thứ 3',3=>'Thứ 4',4=>'Thứ 5',5=>'Thứ 6',6=>'Thứ 7',7=>'Chủ nhật'];
$thu=$thus[(int)date('N')];
$base="SELECT l.tenlop,ll.ca,ll.phong FROM lophoc_lich ll JOIN lophoc l ON ll.id_lop=l.id_lop";
if($r=='gv'){$t='Lịch dạy hôm nay';$em='Không có lịch dạy hôm nay';$sql="$base WHERE ll.thu='$thu' AND l.magv='$i'";}
elseif($r=='sv'){$t='Lịch học hôm nay';$em='Không có lịch học hôm nay';$sql="$base JOIN dangky_lop dk ON l.id_lop=dk.id_lop WHERE ll.thu='$thu' AND dk.masv='$i' AND dk.trangthai=1";}
else{$t='Các lớp học hôm nay';$em='Không có lớp học nào hôm nay';$sql="$base WHERE ll.thu='$thu'";}
$ls=rows($sql.' ORDER BY ll.ca');
head('Tổng Quan Hệ Thống');
echo '<div class="pt2"><span></span><span style="color:#64748b">'.h($thu.', '.date('d/m/Y')).'</span></div>';
echo '<div class="stats">';
foreach([['Tổng Sinh Viên','👨‍🎓','#10a06e',$sv],['Tổng Giáo Viên','👩‍🏫','#2563eb',$gv],['Lớp Đang Mở','📚','#ca8a04',$lop]] as [$n,$ic,$c,$v])
    echo '<div class="stat" style="--c:'.$c.'"><i>'.$ic.'</i><div><b>'.h($v).'</b><small>'.h($n).'</small></div></div>';
echo '</div><div class="lich"><h3>📅 '.h($t.' — '.$thu).'</h3>';
if(!$ls)echo '<div style="color:#64748b;font-style:italic;padding:8px 0">✦ '.h($em).'</div>';
foreach($ls as $x)echo '<div class="item"><b>'.h($x['tenlop']).'</b><span><span class="pill a">Ca '.h($x['ca']).'</span><span class="pill p">📍 '.h($x['phong']).'</span></span></div>';
echo '</div>';
foot();
