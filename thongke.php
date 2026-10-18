<?php
require 'mucluc.php';need(['qtv','ql']);
function ab($v){
    if(abs($v)>=1e9)return round($v/1e9,1).'T';
    if(abs($v)>=1e6)return round($v/1e6).'M';
    return vnd($v);
}
$tt=0;$tc=0;$dt=[];$dc=[];$n=1;
foreach(rows("SELECT tenkh,SUM(danop) tong FROM sv_monhoc GROUP BY tenkh") as $x){$tt+=$x['tong'];$dt[]=[$n++,$x['tenkh'],['c-green right',vnd($x['tong'])]];}
$n=1;
foreach(rows("SELECT tengv,luong,tienthuong,tienphat FROM giaovien") as $x){
    $v=$x['luong']+$x['tienthuong']-$x['tienphat'];
    if($v>0){$tc+=$v;$dc[]=[$n++,$x['tengv'],['c-red right',vnd($v)]];}
}
$ln=$tt-$tc;
function chart($t,$c,$l){
    $max=max($t,$c,abs($l),1);$w=640;$h=280;$pl=60;$pt=30;$cw=$w-$pl-20;$ch=$h-$pt-40;
    $o='<svg viewBox="0 0 '.$w.' '.$h.'" class="chart" font-family="Arial">';
    for($i=0;$i<=5;$i++){
        $y=$pt+$ch-$ch*$i/5;
        $o.='<line x1="'.$pl.'" x2="'.($pl+$cw).'" y1="'.$y.'" y2="'.$y.'" stroke="#ddd"/><text x="'.($pl-6).'" y="'.($y+4).'" text-anchor="end" font-size="11" fill="#969696">'.h(ab($max*$i/5)).'</text>';
    }
    $bars=[[$t,'#27ae60','Tổng thu',$t],[$c,'#e74c3c','Tổng chi',$c],[abs($l),$l>=0?'#2980b9':'#e74c3c',$l>=0?'Lợi nhuận':'Lỗ',$l]];
    $bw=80;$sp=($cw-$bw*3)/4;
    foreach($bars as $i=>[$v,$col,$lb,$raw]){
        $x=$pl+$sp+$i*($bw+$sp);$bh=$v/$max*$ch;$y=$pt+$ch-$bh;$cx=$x+$bw/2;
        $o.='<rect x="'.$x.'" y="'.$y.'" width="'.$bw.'" height="'.$bh.'" rx="5" fill="'.$col.'"/>'
        .'<text x="'.$cx.'" y="'.($y-6).'" text-anchor="middle" font-size="12" font-weight="bold" fill="#444">'.h(ab($raw)).'</text>'
        .'<text x="'.$cx.'" y="'.($pt+$ch+20).'" text-anchor="middle" font-size="12" font-weight="bold" fill="#505050">'.h($lb).'</text>';
    }
    return $o.'<line x1="'.$pl.'" x2="'.($pl+$cw).'" y1="'.($pt+$ch).'" y2="'.($pt+$ch).'" stroke="#b4b4b4"/></svg>';
}
head('BÁO CÁO TÀI CHÍNH TRUNG TÂM');
echo '<div class="cards3">'
.'<div class="kpi" style="border-top:3px solid #27ae60"><b style="color:#27ae60;font-size:13px">Tổng thu</b><small>Học phí sinh viên</small><b>'.vnd($tt).' VNĐ</b></div>'
.'<div class="kpi" style="border-top:3px solid #e74c3c"><b style="color:#e74c3c;font-size:13px">Tổng chi</b><small>Lương giáo viên</small><b>'.vnd($tc).' VNĐ</b></div>'
.'<div class="kpi" style="border-top:3px solid #2980b9"><b style="color:#2980b9;font-size:13px">Lợi nhuận</b><small>Thu – Chi</small><b style="color:'.($ln>=0?'#27ae60':'#e74c3c').'">'.vnd($ln).' VNĐ</b></div></div>';
echo '<div class="two"><div><h4>Chi tiết nguồn thu (học phí)</h4>';
tbl(['STT','Khóa học','Tiền thu (VNĐ)'],$dt);
echo '</div><div><h4>Chi tiết nguồn chi (lương)</h4>';
tbl(['STT','Giáo viên','Thực lãnh (VNĐ)'],$dc);
echo '</div></div>';
if(role()=='qtv')echo '<div class="card"><h3>BIỂU ĐỒ TRỰC QUAN</h3>'.chart($tt,$tc,$ln).'</div>';
foot();
