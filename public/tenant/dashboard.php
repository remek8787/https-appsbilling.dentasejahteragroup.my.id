<?php
require (is_file(__DIR__.'/../../app/bootstrap.php') ? __DIR__.'/../../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php');
$u=require_tenant_user();
$tenant=q1('SELECT * FROM tenants WHERE id=?',[(int)$u['tenant_id']]);
$db=tenant_db_path($tenant);
$pdo=new PDO('sqlite:'.$db);
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
$page=$_GET['page'] ?? 'dashboard';
$stats=[
 'packages'=>(int)$pdo->query('SELECT COUNT(*) FROM packages')->fetchColumn(),
 'customers'=>(int)$pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
 'active_customers'=>(int)$pdo->query("SELECT COUNT(*) FROM customers WHERE status='active'")->fetchColumn(),
 'invoices'=>(int)$pdo->query('SELECT COUNT(*) FROM invoices')->fetchColumn(),
 'unpaid'=>(int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE status<>'paid'")->fetchColumn(),
 'payments'=>(int)$pdo->query('SELECT COUNT(*) FROM payments')->fetchColumn(),
 'income'=>(int)$pdo->query('SELECT COALESCE(SUM(amount),0) FROM payments')->fetchColumn(),
];
$titles=[
 'dashboard'=>'Dashboard', 'dashboard-income-summary'=>'Track record pendapatan', 'income-analysis'=>'Analisa pendapatan',
 'data-warga'=>'Data pelanggan aktif','add-warga'=>'Tambah pelanggan','data-pelanggan-free'=>'Pelanggan FREE / Gratis','data-pelanggan-off'=>'Pelanggan OFF / Nonaktif','add-pelanggan-off'=>'Tambah pelanggan OFF','data-diskon'=>'Data diskon','data-deposit-pelanggan'=>'Data deposit pelanggan','data-lokasi'=>'Data lokasi','data-router'=>'Data router','data-tipe-pembayaran'=>'Data tipe pembayaran','add-package'=>'Tambah tipe pembayaran','data-rekening'=>'Data rekening','add-bank'=>'Tambah rekening',
 'data-user'=>'Data user','add-user'=>'Tambah user','office-settings'=>'Pengaturan kantor','branding-settings'=>'Logo & Branding','admin-tutorial'=>'Tutorial admin','activity-log'=>'Log aktivitas',
 'corporate-customers'=>'Data Corporate','add-corporate-customer'=>'Tambah Corporate','corporate-invoices'=>'Tagihan Corporate','add-corporate-invoice'=>'Buat Tagihan Corporate',
 'data-ipl'=>'Pembayaran langganan','add-laporan-ipl'=>'Tambah pembayaran','data-ipl-non'=>'Pembayaran umum','data-tagihan'=>'Data tagihan','add-tagihan'=>'Tambah tagihan','data-sudah-bayar'=>'Sudah bayar','data-belum-bayar-7121'=>'Belum bayar','collection-team'=>'Tim penagihan',
 'data-user-pppoe'=>'Data user PPPoE','data-session-ppoe'=>'Data sesi PPPoE','data-installasi'=>'Data instalasi','dashboard-ticket-pelanggan'=>'Tiket pelanggan',
];
$title=$titles[$page] ?? 'Dashboard';
tenant_v3_render_header($title,$tenant,$u);
if($m=flash('ok')) echo '<div class="alert alert-success">'.e($m).'</div>';
if($m=flash('err')) echo '<div class="alert alert-danger">'.e($m).'</div>';
if($page==='branding-settings'){ define('TENANT_SETTINGS_EMBED', true); require __DIR__.'/settings.php'; tenant_v3_render_footer(); exit; }
if($page==='dashboard'):
?>
<div class="row">
  <div class="col-3"><div class="small-box bg-info"><div class="inner"><h3><?=$stats['customers']?></h3><p>Pelanggan</p></div><div class="icon"><i class="fas fa-users"></i></div><a class="small-box-footer" href="<?=tenant_v3_page_url('data-warga')?>">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a></div></div>
  <div class="col-3"><div class="small-box bg-success"><div class="inner"><h3><?=$stats['payments']?></h3><p>Pembayaran</p></div><div class="icon"><i class="fas fa-money-bill-wave"></i></div><a class="small-box-footer" href="<?=tenant_v3_page_url('data-ipl')?>">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a></div></div>
  <div class="col-3"><div class="small-box bg-warning"><div class="inner"><h3><?=$stats['unpaid']?></h3><p>Belum bayar</p></div><div class="icon"><i class="fas fa-file-invoice"></i></div><a class="small-box-footer" href="<?=tenant_v3_page_url('data-belum-bayar-7121')?>">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a></div></div>
  <div class="col-3"><div class="small-box bg-danger"><div class="inner"><h3><?=number_format($stats['income'],0,',','.')?></h3><p>Pendapatan</p></div><div class="icon"><i class="fas fa-chart-line"></i></div><a class="small-box-footer" href="<?=tenant_v3_page_url('dashboard-income-summary')?>">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row">
 <div class="col-8"><div class="card card-info"><div class="card-header"><h3 class="card-title">AppsBilling V3 Tenant Instance</h3><span class="tenant-account-pill">No Akun <?=e($tenant['account_no'] ?? '-')?></span></div><div class="card-body"><p>Area ini mengikuti tampilan dan menu <b>AppsBilling V3</b>. Bedanya, tenant ini memakai database sendiri yang masih kosong, jadi data pelanggan, tagihan, pembayaran, PPPoE, corporate, dan modul lain tidak bercampur dengan tenant lain.</p><div class="alert alert-info mb-0">Status: shell V3 tenant sudah aktif. Porting fungsi detail V3 berikutnya tinggal diarahkan ke DB tenant ini.</div></div></div></div>
 <div class="col-4"><div class="card"><div class="card-header"><h3 class="card-title">Identitas Tenant</h3></div><div class="card-body"><p><b><?=e($tenant['company_name'])?></b></p><p>No Akun: <span class="badge badge-warning"><?=e($tenant['account_no'] ?? '-')?></span></p><p>DB: <code><?=e(basename(dirname($db)))?></code></p><?php if(!empty($u['is_master_admin'])):?><span class="badge badge-info">Mode Admin Pusat</span><?php endif;?></div></div></div>
</div>
<?php elseif($page==='data-warga'): $rows=$pdo->query("SELECT c.*,p.name package_name FROM customers c LEFT JOIN packages p ON p.id=c.package_id WHERE c.status='active' ORDER BY c.id DESC LIMIT 50")->fetchAll(); ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title">Data pelanggan aktif</h3><a class="btn btn-success btn-sm" href="<?=tenant_v3_page_url('add-warga')?>">Tambah pelanggan</a></div><div class="card-body table-wrap"><table class="table table-striped"><thead><tr><th>ID Pelanggan</th><th>Nama</th><th>Paket</th><th>Alamat</th><th>Telepon</th><th>Status</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['customer_code'])?></td><td><?=e($r['name'])?></td><td><?=e($r['package_name'] ?? '-')?></td><td><?=e($r['address'])?></td><td><?=e($r['phone'])?></td><td><span class="badge badge-success"><?=e($r['status'])?></span></td></tr><?php endforeach;?><?php if(!$rows):?><tr><td colspan="6"><?php tenant_v3_empty_module('Belum ada pelanggan','Tenant ini masih kosong. Data pelanggan akan masuk ke DB tenant No Akun '.($tenant['account_no'] ?? '-').'.'); ?></td></tr><?php endif;?></tbody></table></div></div>
<?php elseif($page==='data-ipl' || $page==='data-tagihan' || $page==='data-belum-bayar-7121' || $page==='data-sudah-bayar'): ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title"><?=e($title)?></h3></div><div class="card-body"><?php tenant_v3_empty_module($title,'Tampilan mengikuti AppsBilling V3. Data pembayaran/tagihan tenant ini masih kosong karena memakai DB terpisah.'); ?></div></div>
<?php elseif(str_starts_with($page,'add-')): ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title"><?=e($title)?></h3></div><div class="card-body"><?php tenant_v3_empty_module($title,'Form akan mengikuti AppsBilling V3 dan disimpan ke DB tenant ini. Saat ini shell tenant V3 sudah disamakan dulu supaya arah produk tidak melenceng.'); ?></div></div>
<?php elseif($page==='admin-tutorial'): ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title">Tutorial admin</h3></div><div class="card-body"><ol><li>Login memakai No Akun tenant.</li><li>Kelola data di menu seperti AppsBilling V3.</li><li>Semua data tenant disimpan di database terpisah.</li><li>Admin pusat bisa masuk memakai mode master untuk membantu setup.</li></ol></div></div>
<?php else: ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title"><?=e($title)?></h3></div><div class="card-body"><?php tenant_v3_empty_module($title); ?></div></div>
<?php endif; tenant_v3_render_footer(); ?>
