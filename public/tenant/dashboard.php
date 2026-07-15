<?php
require (is_file(__DIR__.'/../../app/bootstrap.php') ? __DIR__.'/../../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php');
$u=require_tenant_user();
$tenant=q1('SELECT * FROM tenants WHERE id=?',[(int)$u['tenant_id']]);
$db=tenant_db_path($tenant);
$pdo=new PDO('sqlite:'.$db);
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
tenant_ensure_v3_core_schema($pdo);

if($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $action=$_POST['action'] ?? '';
    try {
        if($action==='save_package') {
            $id=(int)($_POST['id'] ?? 0);
            $before=$id ? ($pdo->prepare('SELECT * FROM packages WHERE id=?') && null) : null;
            if($id){ $st=$pdo->prepare('SELECT * FROM packages WHERE id=?'); $st->execute([$id]); $before=$st->fetch(); }
            $name=trim($_POST['name'] ?? '');
            $speed=trim($_POST['speed'] ?? '');
            $price=(int)preg_replace('/\D+/', '', $_POST['price'] ?? '0');
            $active=(int)($_POST['is_active'] ?? 1);
            if($name==='') throw new RuntimeException('Nama tipe / paket wajib diisi.');
            if($id){ $st=$pdo->prepare('UPDATE packages SET name=?,speed=?,price=?,is_active=?,status=? WHERE id=?'); $st->execute([$name,$speed,$price,$active,$active?'active':'inactive',$id]); }
            else { $st=$pdo->prepare('INSERT INTO packages(name,speed,price,is_active,status,created_at) VALUES(?,?,?,?,?,?)'); $st->execute([$name,$speed,$price,$active,$active?'active':'inactive',now()]); $id=(int)$pdo->lastInsertId(); }
            $st=$pdo->prepare('SELECT * FROM packages WHERE id=?'); $st->execute([$id]); $after=$st->fetch();
            log_event((int)$tenant['id'],!empty($u['is_master_admin'])?'platform_admin':'tenant_user',(int)($u['id'] ?? 0),$before?'tenant_package_update':'tenant_package_create',$before ? ('Mengubah paket. '.tenant_changed_summary($before,$after)) : ('Menambah paket '.$name));
            flash('ok','Tipe pembayaran / paket tersimpan.');
            redirect(tenant_v3_page_url('data-tipe-pembayaran'));
        }
        if($action==='archive_package') {
            $id=(int)($_POST['id'] ?? 0);
            $usedSt=$pdo->prepare('SELECT COUNT(*) FROM customers WHERE package_id=?'); $usedSt->execute([$id]); $used=(int)$usedSt->fetchColumn();
            $st=$pdo->prepare('UPDATE packages SET is_active=0,status=? WHERE id=?'); $st->execute(['inactive',$id]);
            log_event((int)$tenant['id'],!empty($u['is_master_admin'])?'platform_admin':'tenant_user',(int)($u['id'] ?? 0),'tenant_package_archive','Menonaktifkan paket yang dipakai '.$used.' pelanggan.');
            flash('ok','Paket dinonaktifkan. Pelanggan lama tetap aman.');
            redirect(tenant_v3_page_url('data-tipe-pembayaran'));
        }
        if($action==='save_bank') {
            $id=(int)($_POST['id'] ?? 0);
            $before=null; if($id){ $st=$pdo->prepare('SELECT * FROM bank_accounts WHERE id=?'); $st->execute([$id]); $before=$st->fetch(); }
            $bank=trim($_POST['bank_name'] ?? '');
            $number=trim($_POST['account_number'] ?? '');
            $name=trim($_POST['account_name'] ?? '');
            $label=trim($_POST['label'] ?? '');
            $active=(int)($_POST['is_active'] ?? 1);
            $notes=trim($_POST['notes'] ?? '');
            if($bank==='' || $name==='') throw new RuntimeException('Nama rekening/bank dan atas nama wajib diisi.');
            if($id){ $st=$pdo->prepare('UPDATE bank_accounts SET bank_name=?,account_number=?,account_name=?,label=?,is_active=?,notes=? WHERE id=?'); $st->execute([$bank,$number,$name,$label,$active,$notes,$id]); }
            else { $st=$pdo->prepare('INSERT INTO bank_accounts(bank_name,account_number,account_name,label,is_active,notes,created_at) VALUES(?,?,?,?,?,?,?)'); $st->execute([$bank,$number,$name,$label,$active,$notes,now()]); $id=(int)$pdo->lastInsertId(); }
            log_event((int)$tenant['id'],!empty($u['is_master_admin'])?'platform_admin':'tenant_user',(int)($u['id'] ?? 0),$before?'tenant_bank_update':'tenant_bank_create',$before ? ('Mengubah rekening '.$bank) : ('Menambah rekening '.$bank));
            flash('ok','Rekening tersimpan.');
            redirect(tenant_v3_page_url('data-rekening'));
        }
        if($action==='delete_bank') {
            $id=(int)($_POST['id'] ?? 0);
            $st=$pdo->prepare('SELECT * FROM bank_accounts WHERE id=?'); $st->execute([$id]); $old=$st->fetch();
            $st=$pdo->prepare('DELETE FROM bank_accounts WHERE id=?'); $st->execute([$id]);
            log_event((int)$tenant['id'],!empty($u['is_master_admin'])?'platform_admin':'tenant_user',(int)($u['id'] ?? 0),'tenant_bank_delete','Menghapus rekening '.($old['bank_name'] ?? ''));
            flash('ok','Rekening dihapus.');
            redirect(tenant_v3_page_url('data-rekening'));
        }
    } catch(Throwable $e) { flash('err',$e->getMessage()); redirect(tenant_v3_page_url($_GET['page'] ?? 'dashboard')); }
}

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
<?php elseif($page==='data-tipe-pembayaran'): $rows=$pdo->query('SELECT p.*,COUNT(c.id) c FROM packages p LEFT JOIN customers c ON c.package_id=p.id GROUP BY p.id ORDER BY p.price,p.name')->fetchAll(); ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title">Data tipe pembayaran</h3><div><a class="btn btn-info" href="<?=tenant_v3_page_url('add-package')?>">Tambah data</a></div></div><div class="card-body table-wrap"><table class="table table-striped"><thead><tr><th>No</th><th>Nama tipe</th><th>Keterangan tipe</th><th>Harga</th><th>Jenis pembayaran</th><th>Profil</th><th>Status</th><th>Jumlah pelanggan</th><th>Aksi</th></tr></thead><tbody><?php $n=1; foreach($rows as $r): ?><tr><td><?=$n++?></td><td><?=e($r['name'])?></td><td><?=e($r['speed'] ?? '')?></td><td><?=tenant_money((int)$r['price'])?></td><td>Bulanan</td><td><?=e($r['speed'] ?? '')?></td><td><span class="badge <?=((int)($r['is_active'] ?? 1)?'badge-success':'badge-danger')?>"><?=((int)($r['is_active'] ?? 1)?'Aktif':'Nonaktif')?></span></td><td><?=e((string)$r['c'])?></td><td><form method="post" style="display:inline" onsubmit="return confirm('Nonaktifkan paket ini?')"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="archive_package"><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button class="btn btn-warning btn-sm">Nonaktifkan</button></form></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="9"><?php tenant_v3_empty_module('Belum ada tipe pembayaran','Tambahkan paket/tipe pembayaran pertama untuk tenant No Akun '.($tenant['account_no'] ?? '-').'.'); ?></td></tr><?php endif; ?></tbody></table></div></div>
<?php elseif($page==='add-package'): ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title">Tambah Tipe Pembayaran / Paket</h3></div><div class="card-body"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="save_package"><div class="row"><div class="col-4"><div class="form-group"><label>Nama tipe / Paket</label><input class="form-control" name="name" required></div></div><div class="col-4"><div class="form-group"><label>Keterangan / Speed / Profil</label><input class="form-control" name="speed" placeholder="contoh: 10 Mbps"></div></div><div class="col-2"><div class="form-group"><label>Harga</label><input class="form-control" name="price" inputmode="numeric" required></div></div><div class="col-2"><div class="form-group"><label>Status</label><select class="form-control" name="is_active"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div></div></div><button class="btn btn-info">Simpan</button> <a class="btn btn-secondary" href="<?=tenant_v3_page_url('data-tipe-pembayaran')?>">Batal</a></form></div></div>
<?php elseif($page==='data-rekening'): $rows=$pdo->query('SELECT * FROM bank_accounts ORDER BY id')->fetchAll(); ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title">Data Rekening</h3><a class="btn btn-info" href="<?=tenant_v3_page_url('add-bank')?>">Tambah data</a></div><div class="card-body table-wrap"><table class="table table-striped"><thead><tr><th>No</th><th>Nama Rekening</th><th>Label</th><th>Atas Nama</th><th>No Rekening</th><th>Tampil Di Invoice Broadband</th><th>Catatan</th><th>Aksi</th></tr></thead><tbody><?php $n=1; foreach($rows as $r): ?><tr><td><?=$n++?></td><td><?=e($r['bank_name'])?></td><td><?=e($r['label'] ?: '-')?></td><td><?=e($r['account_name'])?></td><td><?=e($r['account_number'])?></td><td><?=((int)$r['is_active']?'Ya':'Tidak')?></td><td><?=e($r['notes'] ?: '-')?></td><td><form method="post" style="display:inline" onsubmit="return confirm('Hapus rekening ini?')"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="delete_bank"><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button class="btn btn-danger btn-sm">Hapus</button></form></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="8"><?php tenant_v3_empty_module('Belum ada rekening','Tambahkan rekening/QRIS/transfer untuk tampil di invoice tenant ini.'); ?></td></tr><?php endif; ?></tbody></table></div></div>
<?php elseif($page==='add-bank'): ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title">Tambah Rekening</h3></div><div class="card-body"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="save_bank"><div class="row"><div class="col-3"><div class="form-group"><label>Nama Rekening / Bank</label><input class="form-control" name="bank_name" required></div></div><div class="col-3"><div class="form-group"><label>Atas Nama</label><input class="form-control" name="account_name" required></div></div><div class="col-3"><div class="form-group"><label>No Rekening</label><input class="form-control" name="account_number"></div></div><div class="col-3"><div class="form-group"><label>Label</label><input class="form-control" name="label" placeholder="Invoice / QRIS / Transfer"></div></div><div class="col-3"><div class="form-group"><label>Tampil Di Invoice</label><select class="form-control" name="is_active"><option value="1">Ya</option><option value="0">Tidak</option></select></div></div><div class="col-9"><div class="form-group"><label>Catatan</label><input class="form-control" name="notes"></div></div></div><button class="btn btn-info">Simpan</button> <a class="btn btn-secondary" href="<?=tenant_v3_page_url('data-rekening')?>">Batal</a></form></div></div>
<?php elseif($page==='data-ipl' || $page==='data-tagihan' || $page==='data-belum-bayar-7121' || $page==='data-sudah-bayar'): ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title"><?=e($title)?></h3></div><div class="card-body"><?php tenant_v3_empty_module($title,'Tampilan mengikuti AppsBilling V3. Data pembayaran/tagihan tenant ini masih kosong karena memakai DB terpisah.'); ?></div></div>
<?php elseif(str_starts_with($page,'add-')): ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title"><?=e($title)?></h3></div><div class="card-body"><?php tenant_v3_empty_module($title,'Form akan mengikuti AppsBilling V3 dan disimpan ke DB tenant ini. Modul ini masuk antrean porting berikutnya.'); ?></div></div>
<?php elseif($page==='admin-tutorial'): ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title">Tutorial admin</h3></div><div class="card-body"><ol><li>Login memakai No Akun tenant.</li><li>Kelola data di menu seperti AppsBilling V3.</li><li>Semua data tenant disimpan di database terpisah.</li><li>Admin pusat bisa masuk memakai mode master untuk membantu setup.</li></ol></div></div>
<?php else: ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title"><?=e($title)?></h3></div><div class="card-body"><?php tenant_v3_empty_module($title); ?></div></div>
<?php endif; tenant_v3_render_footer(); ?>
