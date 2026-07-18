<?php
function active($p){ return ($_GET['page']??'dashboard')===$p?'active':''; }
function page_url($p,$params=[]){ return 'index.php?'.http_build_query(array_merge(['page'=>$p],$params)); }
function menu_open($items){ $cur=$_GET['page']??'dashboard'; foreach($items as $it){ if(($it[1]??'')===$cur) return true; } return false; }
function render_header($title='Dashboard'){
 $user=$_SESSION['v3_user']??['name'=>'ANANTA','level'=>'Administrator','account'=>''];
 $isCrew=current_user_is_crew();
 $groups=$isCrew ? [
  ['Data pelanggan','fas fa-users', [
    ['Pelanggan aktif','data-warga','far fa-circle'],
    ['Pelanggan FREE / Gratis','data-pelanggan-free','far fa-circle'],
  ]],
 ] : [
  ['Dashboard','fas fa-tachometer-alt', [
    ['Dashboard','dashboard','far fa-circle'],
    ['Track record pendapatan','dashboard-income-summary','far fa-circle'],
    ['Analisa pendapatan','income-analysis','far fa-circle'],
  ]],
  ['Kelola data','fas fa-database', [
    ['Data pelanggan aktif','data-warga','far fa-circle'],
    ['Tambah pelanggan','add-warga','far fa-circle'],
    ['Pelanggan FREE / Gratis','data-pelanggan-free','far fa-circle'],
    ['Pelanggan OFF / Nonaktif','data-pelanggan-off','far fa-circle'],
    ['Tambah pelanggan OFF','add-pelanggan-off','far fa-circle'],
    ['Data diskon','data-diskon','far fa-circle'],
    ['Data deposit pelanggan','data-deposit-pelanggan','far fa-circle'],
    ['Data lokasi','data-lokasi','far fa-circle'],
    ['Data router','data-router','far fa-circle'],
    ['Data tipe pembayaran','data-tipe-pembayaran','far fa-circle'],
    ['Tambah tipe pembayaran','add-package','far fa-circle'],
    ['Data rekening','data-rekening','far fa-circle'],
    ['Tambah rekening','add-bank','far fa-circle'],
  ]],
  ['Admin sistem','fas fa-user-shield', [
    ['Data user','data-user','far fa-circle'],
    ['Tambah user','add-user','far fa-circle'],
    ['Pengaturan kantor','office-settings','far fa-circle'],
    ['Tutorial admin','admin-tutorial','far fa-circle'],
    ['Log aktivitas','activity-log','far fa-circle'],
  ]],
  ['Corporate','fas fa-building', [
    ['Data Corporate','corporate-customers','far fa-circle'],
    ['Tambah Corporate','add-corporate-customer','far fa-circle'],
    ['Tagihan Corporate','corporate-invoices','far fa-circle'],
    ['Buat Tagihan Corporate','add-corporate-invoice','far fa-circle'],
  ]],
  ['Infrastruktur Jaringan','fas fa-project-diagram', [
    ['Ringkasan Infrastruktur','network-dashboard','far fa-circle'],
    ['Data ODC','network-odc','far fa-circle'],
    ['Data ODP','network-odp','far fa-circle'],
    ['Joint Closure','network-jc','far fa-circle'],
    ['Jalur Kabel','network-cables','far fa-circle'],
    ['Sambungan Core','network-splices','far fa-circle'],
    ['Penempatan Pelanggan','network-customers','far fa-circle'],
  ]],
  ['Kelola pembayaran','fas fa-money-bill-wave', [
    ['Pembayaran langganan','data-ipl','far fa-circle'],
    ['Tambah pembayaran','add-laporan-ipl','far fa-circle'],
    ['Pembayaran umum','data-ipl-non','far fa-circle'],
    ['Pengeluaran operasional','operational-expenses','far fa-circle'],
    ['Data tagihan','data-tagihan','far fa-circle'],
    ['Tambah tagihan','add-tagihan','far fa-circle'],
    ['Sudah bayar','data-sudah-bayar','far fa-circle'],
    ['Belum bayar','data-belum-bayar-7121','far fa-circle'],
    ['Tim penagihan','collection-team','far fa-circle'],
  ]],
  ['Kelola PPPoE','fas fa-network-wired', [
    ['Data user PPPoE','data-user-pppoe','far fa-circle'],
    ['Data sesi PPPoE','data-session-ppoe','far fa-circle'],
    ['Data instalasi','data-installasi','far fa-circle'],
    ['Tiket pelanggan','dashboard-ticket-pelanggan','far fa-circle'],
  ]],
 ];
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#0f5ea8"><meta name="mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-title" content="e-Billing DSG"><link rel="manifest" href="/v3/manifest-pwa-receipt-d-20260715.json"><link rel="icon" href="/v3/assets/pwa-dentanet-receipt-d-20260715-512.png" type="image/png"><link rel="apple-touch-icon" href="/v3/assets/apple-dentanet-receipt-d-20260715.png"><title>e-Billing DSG System</title><script>tailwind={config:{prefix:'tw-',corePlugins:{preflight:false},theme:{extend:{colors:{denta:{50:'#eef8ff',100:'#d9f0ff',500:'#1584d1',700:'#0f5ea8'},mint:{50:'#effef8',500:'#14b8a6'}},boxShadow:{soft:'0 18px 50px rgba(15,23,42,.10)',glow:'0 18px 40px rgba(20,184,166,.16)'}}}}}</script><script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="assets/adminlte-clone.css?v=20260719-network-infrastructure"><link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQkqKR8JmX6j0q77LT7Ff4lZPz7bJo=" crossorigin=""></head><body class="hold-transition sidebar-mini tw-bg-slate-100"><div class="wrapper tw-min-h-screen tw-bg-[radial-gradient(circle_at_top_left,_rgba(21,132,209,.16),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(20,184,166,.14),_transparent_28%)]"><nav class="main-header navbar navbar-expand navbar-yellow navbar-light"><a class="nav-link toggle" href="#">☰</a><ul class="navbar-nav"><li><a class="topbar-brand-copy" href="index.php"><b>e-Billing DSG</b><span>Billing Management System</span></a></li></ul><ul class="navbar-nav ml-auto"><li><a href="logout.php">Keluar</a></li></ul></nav><aside class="main-sidebar sidebar-dark-primary elevation-4"><a class="brand-link ebilling-dsg-brand" href="index.php"><img class="brand-logo" src="assets/dentanet-logo-20260715.jpg" alt="DENTA NET"><span class="brand-text"><b>e-Billing DSG</b><small>PT Denta Sejahtera Group</small></span></a><div class="sidebar"><div class="user-panel"><div class="image">A</div><div><a href="#"><?php echo e($user['name']); ?></a><small><?php echo e($user['level']); ?></small></div></div><nav class="mt-2"><ul class="nav nav-pills nav-sidebar flex-column">
<?php foreach($groups as $g): $open=menu_open($g[2]); ?>
<li class="nav-item has-treeview <?php echo $open?'menu-open':''; ?>"><a class="nav-link nav-parent <?php echo $open?'active':''; ?>" href="#"><i class="nav-icon <?php echo $g[1]; ?>"></i><p><?php echo e($g[0]); ?><i class="right fas fa-angle-left"></i></p></a><ul class="nav nav-treeview" <?php echo $open?'style="display:block"':''; ?>><?php foreach($g[2] as $m): ?><li class="nav-item"><a class="nav-link <?php echo active($m[1]); ?>" href="<?php echo page_url($m[1]); ?>"><i class="nav-icon <?php echo $m[2]; ?>"></i><p><?php echo e($m[0]); ?></p></a></li><?php endforeach; ?></ul></li>
<?php endforeach; ?>
</ul></nav></div></aside><div class="content-wrapper tw-backdrop-blur-sm"><section class="content-header"><div class="container-fluid"><div class="tw-rounded-2xl tw-bg-white/70 tw-border tw-border-white/70 tw-shadow-soft tw-p-4 tw-mb-3"><h1><?php echo e($title); ?></h1><ol class="breadcrumb"><li>Beranda</li><li><?php echo e($title); ?></li></ol></div></div></section><section class="content"><div class="container-fluid tw-space-y-3">
<?php }
function render_footer(){ ?></div></section></div><aside class="control-sidebar control-sidebar-dark"></aside><footer class="main-footer"><strong>e-Billing DSG System</strong><span>PT Denta Sejahtera Group</span><small>Billing Management System · Version 3.0</small></footer></div><script>document.querySelector('.toggle')?.addEventListener('click',()=>document.body.classList.toggle('sidebar-collapse'));document.querySelectorAll('.has-treeview>.nav-parent').forEach(a=>a.addEventListener('click',e=>{e.preventDefault();const li=a.parentElement;li.classList.toggle('menu-open');const tree=li.querySelector('.nav-treeview');if(tree)tree.style.display=li.classList.contains('menu-open')?'block':'none';}));</script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script><script src="assets/v3-ajax.js?v=20260719-network-infrastructure"></script><script>if('serviceWorker' in navigator){window.addEventListener('load',function(){navigator.serviceWorker.register('/v3/service-worker.js?v=20260719-network-infrastructure',{scope:'/v3/',updateViaCache:'none'}).then(function(reg){reg.update();if(reg.waiting){reg.waiting.postMessage({type:'SKIP_WAITING'});}console.info('e-Billing DSG System ready');}).catch(function(err){console.warn('PWA register failed',err);});});}</script></body></html><?php }
?>
