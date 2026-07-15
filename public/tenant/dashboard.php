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

        if($action==='save_customer') {
            $id=(int)($_POST['id'] ?? 0);
            $before=null; if($id){ $st=$pdo->prepare('SELECT * FROM customers WHERE id=?'); $st->execute([$id]); $before=$st->fetch(); }
            $code=tenant_customer_code($pdo,$_POST['customer_code'] ?? '',$id);
            $dup=$pdo->prepare('SELECT id,name FROM customers WHERE customer_code=? AND id<>? LIMIT 1'); $dup->execute([$code,$id]); $dupRow=$dup->fetch();
            if($dupRow) throw new RuntimeException('ID pelanggan '.$code.' sudah dipakai oleh '.$dupRow['name'].'.');
            $name=trim($_POST['name'] ?? ''); if($name==='') throw new RuntimeException('Nama pelanggan wajib diisi.');
            $packageId=(int)($_POST['package_id'] ?? 0); if($packageId<=0) $packageId=null;
            $status=$_POST['customer_status'] ?? 'active'; if(!in_array($status,['active','off','isolir'],true)) $status='active';
            $isActive=$status==='active'?1:0;
            $rawTikor=trim($_POST['tikor'] ?? '');
            [$lat,$lng]=tenant_parse_latlng($_POST['latitude'] ?? '',$_POST['longitude'] ?? '',$rawTikor);
            if($rawTikor!=='' && ($lat==='' || $lng==='')) throw new RuntimeException('Tikor belum kebaca. Gunakan format latitude, longitude.');
            $data=[$code,$name,trim($_POST['address'] ?? ''),trim($_POST['phone'] ?? ''),$packageId,$_POST['registered_at'] ?: date('Y-m-d'),(int)($_POST['due_day'] ?? 20),$status,$status,$isActive,trim($_POST['area_name'] ?? ''),$lat,$lng,trim($_POST['map_note'] ?? ''),trim($_POST['router_name'] ?? ''),trim($_POST['pppoe_username'] ?? ''),trim($_POST['onu_name'] ?? ''),trim($_POST['notes'] ?? '')];
            if($id){ $data[]=$id; $st=$pdo->prepare('UPDATE customers SET customer_code=?,name=?,address=?,phone=?,package_id=?,registered_at=?,due_day=?,status=?,customer_status=?,is_active=?,area_name=?,latitude=?,longitude=?,map_note=?,router_name=?,pppoe_username=?,onu_name=?,notes=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'); $st->execute($data); }
            else { $st=$pdo->prepare('INSERT INTO customers(customer_code,name,address,phone,package_id,registered_at,due_day,status,customer_status,is_active,area_name,latitude,longitude,map_note,router_name,pppoe_username,onu_name,notes,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)'); $st->execute($data); $id=(int)$pdo->lastInsertId(); }
            $st=$pdo->prepare('SELECT * FROM customers WHERE id=?'); $st->execute([$id]); $after=$st->fetch();
            $pdo->prepare('INSERT INTO customer_events(customer_id,event_type,title,notes,actor) VALUES(?,?,?,?,?)')->execute([$id,'customer','Data pelanggan disimpan','Via tenant V3 clone',($u['name'] ?? $u['username'] ?? 'Admin')]);
            log_event((int)$tenant['id'],!empty($u['is_master_admin'])?'platform_admin':'tenant_user',(int)($u['id'] ?? 0),$before?'tenant_customer_update':'tenant_customer_create',$before ? ('Mengubah pelanggan. '.tenant_changed_summary($before,$after)) : ('Menambah pelanggan '.$code.' - '.$name));
            flash('ok','Data pelanggan tersimpan.');
            redirect(tenant_v3_page_url($status==='active'?'data-warga':'data-pelanggan-off'));
        }
        if($action==='delete_customer') {
            $id=(int)($_POST['id'] ?? 0); $st=$pdo->prepare('SELECT * FROM customers WHERE id=?'); $st->execute([$id]); $old=$st->fetch();
            $pdo->prepare('DELETE FROM customers WHERE id=?')->execute([$id]);
            log_event((int)$tenant['id'],!empty($u['is_master_admin'])?'platform_admin':'tenant_user',(int)($u['id'] ?? 0),'tenant_customer_delete','Menghapus pelanggan '.(($old['customer_code'] ?? '').' - '.($old['name'] ?? '')));
            flash('ok','Pelanggan dihapus.'); redirect(tenant_v3_page_url('data-warga'));
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
<?php elseif($page==='data-warga' || $page==='data-pelanggan-free' || $page==='data-pelanggan-off'): ?>
<?php
$params=[]; $where='1=1';
if($page==='data-warga') $where="c.customer_status='active' AND c.is_active=1 AND COALESCE(p.price,0)>0";
elseif($page==='data-pelanggan-free') $where="c.customer_status='active' AND c.is_active=1 AND COALESCE(p.price,0)=0";
else $where="c.customer_status<>'active' OR c.is_active=0";
$q=trim($_GET['q'] ?? '');
if($q!==''){ $where.=" AND (c.customer_code LIKE ? OR c.name LIKE ? OR c.address LIKE ? OR c.phone LIKE ? OR c.pppoe_username LIKE ? OR c.onu_name LIKE ?)"; for($i=0;$i<6;$i++) $params[]='%'.$q.'%'; }
$st=$pdo->prepare("SELECT c.*,COALESCE(p.name,'') package_name,COALESCE(p.speed,'') speed,COALESCE(p.price,0) price FROM customers c LEFT JOIN packages p ON p.id=c.package_id WHERE $where ORDER BY c.id DESC LIMIT 500"); $st->execute($params); $rows=$st->fetchAll();
?>
<div class="card card-info"><div class="card-header"><h3 class="card-title"><?=e($title)?></h3><div><a class="btn btn-outline-info" href="<?=tenant_v3_page_url('data-warga')?>">Aktif</a> <a class="btn btn-outline-info" href="<?=tenant_v3_page_url('data-pelanggan-free')?>">FREE</a> <a class="btn btn-secondary" href="<?=tenant_v3_page_url('data-pelanggan-off')?>">OFF</a> <a class="btn btn-info" href="<?=tenant_v3_page_url($page==='data-pelanggan-off'?'add-pelanggan-off':'add-warga')?>">Tambah pelanggan</a></div></div><div class="card-body"><form class="form-inline mb-3" method="get"><input type="hidden" name="page" value="<?=e($page)?>"><input class="form-control mr-2" name="q" value="<?=e($q)?>" placeholder="Cari ID, nama, alamat, HP, PPPoE, Secret..."><button class="btn btn-info">Cari</button></form><div class="table-wrap"><table class="table table-striped"><thead><tr><th>No</th><th>ID Pelanggan</th><th>Nama</th><th>Paket</th><th>Harga</th><th>HP</th><th>Alamat</th><th>PPPoE</th><th>Status</th><th>Jatuh tempo</th><th>Aksi</th></tr></thead><tbody><?php $n=1; foreach($rows as $r): ?><tr><td><?=$n++?></td><td><b><?=e($r['customer_code'])?></b></td><td><?=e($r['name'])?><br><small><?=e($r['registered_at'] ?: '-')?></small></td><td><?=e($r['package_name'] ?: '-')?><br><small><?=e($r['speed'] ?: '')?></small></td><td><?=tenant_money((int)$r['price'])?></td><td><?=e($r['phone'] ?: '-')?></td><td><?=e($r['address'] ?: '-')?></td><td><?=e($r['pppoe_username'] ?: '-')?><br><small><?=e($r['onu_name'] ?: '')?></small></td><td><span class="badge <?=($r['customer_status']==='active'?'badge-success':'badge-warning')?>"><?=e($r['customer_status'])?></span></td><td>Tgl <?=e((string)$r['due_day'])?></td><td><form method="post" style="display:inline" onsubmit="return confirm('Hapus pelanggan ini?')"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="delete_customer"><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button class="btn btn-danger btn-sm">Hapus</button></form></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="11"><?php tenant_v3_empty_module($title,'Belum ada pelanggan pada segmen ini untuk tenant No Akun '.($tenant['account_no'] ?? '-').'.'); ?></td></tr><?php endif; ?></tbody></table></div></div></div>
<?php elseif($page==='add-warga' || $page==='add-pelanggan-off'): $isOff=$page==='add-pelanggan-off'; $packages=$pdo->query('SELECT * FROM packages ORDER BY price,name')->fetchAll(); ?>
<div class="card card-info"><div class="card-header"><h3 class="card-title"><?=$isOff?'Tambah pelanggan OFF / Tidak aktif':'Tambah data pelanggan aktif'?></h3></div><div class="card-body"><?php if(!$packages): ?><div class="alert alert-warning"><b>Paket belum ada.</b> Sebaiknya isi dulu Data tipe pembayaran supaya pelanggan langsung punya paket/harga.</div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="save_customer"><div class="row"><div class="col-4"><div class="form-group"><label>ID pelanggan</label><input class="form-control" name="customer_code" placeholder="otomatis DN + 10 angka" pattern="DN[0-9]{10}" maxlength="12"><small class="text-muted">Kosongkan untuk auto-generate.</small></div></div><div class="col-4"><div class="form-group"><label>Nama pelanggan</label><input class="form-control" name="name" required></div></div><div class="col-4"><div class="form-group"><label>Telepon</label><input class="form-control" name="phone"></div></div><div class="col-8"><div class="form-group"><label>Alamat</label><input class="form-control" name="address"></div></div><div class="col-4"><div class="form-group"><label>Paket</label><select class="form-control" name="package_id"><option value="">Pilih paket</option><?php foreach($packages as $pk): ?><option value="<?=(int)$pk['id']?>"><?=e($pk['name'].' - '.tenant_money((int)$pk['price']))?></option><?php endforeach; ?></select></div></div><div class="col-3"><div class="form-group"><label>Tanggal registrasi</label><input class="form-control" type="date" name="registered_at" value="<?=date('Y-m-d')?>"></div></div><div class="col-3"><div class="form-group"><label>Jatuh tempo</label><input class="form-control" type="number" name="due_day" value="20"></div></div><div class="col-3"><div class="form-group"><label>Status</label><select class="form-control" name="customer_status"><option value="active" <?=!$isOff?'selected':''?>>Aktif</option><option value="off" <?=$isOff?'selected':''?>>OFF / Tidak aktif</option><option value="isolir">Isolir</option></select></div></div><div class="col-3"><div class="form-group"><label>Area / Lokasi</label><input class="form-control" name="area_name"></div></div><div class="col-3"><div class="form-group"><label>Tikor / Koordinat</label><input class="form-control" name="tikor" placeholder="-8.123456, 112.123456"><small class="text-muted">Format latitude, longitude.</small></div></div><div class="col-3"><div class="form-group"><label>Catatan map</label><input class="form-control" name="map_note"></div></div><div class="col-3"><div class="form-group"><label>Router</label><input class="form-control" name="router_name"></div></div><div class="col-4"><div class="form-group"><label>Username PPPoE</label><input class="form-control" name="pppoe_username"></div></div><div class="col-4"><div class="form-group"><label>Secret</label><input class="form-control" name="onu_name"></div></div><div class="col-12"><div class="form-group"><label>Catatan</label><input class="form-control" name="notes"></div></div></div><button class="btn btn-info">Simpan</button> <a class="btn btn-secondary" href="<?=tenant_v3_page_url($isOff?'data-pelanggan-off':'data-warga')?>">Batal</a></form></div></div>
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
