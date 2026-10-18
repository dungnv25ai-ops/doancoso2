<?php
require 'mucluc.php';need();
$r=role();$i=e(uid());
$ph=$_GET['phong']??'Phòng 1';
$sql="SELECT l.tenlop,ll.thu,ll.ca,ll.phong,g.tengv FROM lophoc_lich ll JOIN lophoc l ON ll.id_lop=l.id_lop JOIN giaovien g ON l.magv=g.magv WHERE ";
if($r=='gv')$sql.="l.magv='$i'";
elseif($r=='sv')$sql.="l.id_lop IN (SELECT id_lop FROM dangky_lop WHERE masv='$i' AND trangthai=1)";
else $sql.="ll.phong='".e($ph)."'";
$cot=['Thứ 2'=>1,'Thứ 3'=>2,'Thứ 4'=>3,'Thứ 5'=>4,'Thứ 6'=>5,'Thứ 7'=>6,'Chủ nhật'=>7];
$m=[];
foreach(rows($sql) as $x){
    $c=(int)$x['ca'];$d=$cot[trim($x['thu'])]??0;
    if($c>=1&&$c<=4&&$d)$m[$c][$d][]=$x;
}
$gio=[1=>'07:00 - 09:30',2=>'09:45 - 12:15',3=>'13:00 - 15:30',4=>'15:45 - 18:15'];
head($r=='gv'?'Lịch Dạy':'Thời Khóa Biểu');
if($r=='qtv'||$r=='ql')echo '<form method="get" class="card filter">'.fld('Phòng học',sel('phong',['Phòng 1'=>'Phòng 1','Phòng 2'=>'Phòng 2','Phòng 3'=>'Phòng 3','Phòng 4'=>'Phòng 4'],$ph)).'<button class="btn b-blue">Xem</button></form>';
echo '<div class="tw"><table class="tkb"><tr><th>Ca \ Thứ</th>';
foreach(array_keys($cot) as $t)echo '<th>'.h($t).'</th>';
echo '</tr>';
for($c=1;$c<=4;$c++){
    echo '<tr><td class="ca"><b>CA '.$c.'</b><br><small style="color:#6c757d">'.$gio[$c].'</small></td>';
    for($d=1;$d<=7;$d++){
        echo '<td>';
        foreach($m[$c][$d]??[] as $x){
            echo '<div class="slot"><b>'.h($x['tenlop']).'</b><br>Phòng: '.h($x['phong']);
            if($r!='gv')echo '<br><small style="color:#6c757d">GV: '.h($x['tengv']).'</small>';
            echo '</div>';
        }
        echo '</td>';
    }
    echo '</tr>';
}
echo '</table></div>';
foot();
