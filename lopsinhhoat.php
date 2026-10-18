<?php
require 'mucluc.php';need(['gv','sv']);
$me=uid();
$g=role()=='gv'?$me:substr($me,0,strpos($me,'v')+1);
$ge=e($g);
$gv=one("SELECT * FROM giaovien WHERE magv='$ge'")?:[];
$ds=rows("SELECT * FROM sinhvien WHERE masv LIKE '$ge%' ORDER BY masv");
$data=[];$n=1;
foreach($ds as $x)$data[]=[$n++,$x['masv'],$x['ten'].(role()=='sv'&&$x['masv']==$me?' (Bạn)':''),nv($x['gioitinh']),nv($x['sdt']),nv($x['email'])];
head('Lớp sinh hoạt');
echo '<div class="card"><h3 style="color:#337ab7">Thông Tin Giáo Viên Chủ Nhiệm</h3><div class="grid">'
.fld('Họ tên',inp('x',nv($gv['tengv']??null,'Chưa cập nhật'),'text','readonly'))
.fld('SĐT',inp('x',nv($gv['sdt']??null,'Chưa cập nhật'),'text','readonly'))
.fld('Giới tính',inp('x',nv($gv['gioitinh']??null,'Chưa cập nhật'),'text','readonly'))
.fld('Email',inp('x',nv($gv['email']??null,'Chưa cập nhật'),'text','readonly'))
.'</div></div>';
echo '<h3>Danh Sách Sinh Viên Cùng Lớp (Do GV '.h($g).' chủ nhiệm)</h3>';
tbl(['STT','Mã SV','Họ Tên','Giới Tính','SĐT','Email'],$data);
foot();
