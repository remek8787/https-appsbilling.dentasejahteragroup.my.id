<?php require (is_file(__DIR__.'/../../app/bootstrap.php') ? __DIR__.'/../../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php'); csrf_check(); $admin=require_superadmin();
$id=(int)($_GET['id'] ?? $_POST['tenant_id'] ?? 0);
$t=q1('SELECT * FROM tenants WHERE id=?',[$id]);
if(!$t){ flash('err','Tenant tidak ditemukan.'); redirect(app_url('superadmin/index.php')); }
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action'] ?? '';
    if($action==='approve'){
        execsql('UPDATE tenants SET status=?,approved_at=?,disabled_at=NULL,deleted_at=NULL WHERE id=?',['active',now(),$id]); tenant_db_path($t); log_event($id,'superadmin',(int)$admin['id'],'tenant_approved','Tenant disetujui dari detail akun.'); flash('ok','Akun mitra sudah aktif.');
    } elseif($action==='disable'){
        execsql('UPDATE tenants SET status=?,disabled_at=? WHERE id=?',['disabled',now(),$id]); log_event($id,'superadmin',(int)$admin['id'],'tenant_disabled','Tenant dinonaktifkan dari detail akun.'); flash('ok','Akun mitra dinonaktifkan.');
    } elseif($action==='reactivate'){
        execsql('UPDATE tenants SET status=?,disabled_at=NULL,deleted_at=NULL WHERE id=?',['active',$id]); tenant_db_path($t); log_event($id,'superadmin',(int)$admin['id'],'tenant_reactivated','Tenant diaktifkan kembali dari detail akun.'); flash('ok','Akun mitra aktif kembali.');
    }
    redirect(app_url('superadmin/tenant_detail.php?id='.$id));
}
$t=q1('SELECT * FROM tenants WHERE id=?',[$id]);
$dbRow=q1('SELECT * FROM tenant_databases WHERE tenant_id=?',[$id]);
$users=qall('SELECT id,username,name,role,status,last_login_at,created_at FROM tenant_users WHERE tenant_id=? ORDER BY id ASC',[$id]);
$events=qall('SELECT * FROM tenant_events WHERE tenant_id=? ORDER BY id DESC LIMIT 8',[$id]);
$tenantStats=['packages'=>0,'customers'=>0,'invoices'=>0,'payments'=>0];
if($dbRow && is_file($dbRow['db_path'])){
    $pdo=new PDO('sqlite:'.$dbRow['db_path']);
    foreach($tenantStats as $k=>$v){ try{$tenantStats[$k]=(int)$pdo->query('SELECT COUNT(*) FROM '.$k)->fetchColumn();}catch(Throwable $e){} }
}
render_header('Detail Akun Mitra'); ?>
<div class="dash"><aside class="side"><div class="brand"><span class="brand-mark">AB</span><span>Admin</span></div><a href="<?=app_url('superadmin/index.php')?>">← Semua Mitra</a><a class="active" href="<?=app_url('superadmin/tenant_detail.php?id='.$id)?>">Detail Akun</a><a href="<?=app_url('superadmin/logout.php')?>">Keluar</a><p class="footer-note">Route internal tetap aman, label publik Admin.</p></aside><main class="main"><div class="topline"><div><h1><?=e($t['company_name'])?></h1><p>No Akun <strong><?=e($t['account_no'] ?: '-')?></strong> · <?=e($t['owner_name'])?> · <?=e($t['phone'])?></p></div><span class="badge <?=e($t['status'])?>"><?=e($t['status'])?></span></div><?php if($m=flash('ok')):?><div class="alert alert-ok"><?=e($m)?></div><?php endif;?><?php if($m=flash('err')):?><div class="alert alert-danger"><?=e($m)?></div><?php endif;?>
<div class="grid"><div class="card"><h3>Pelanggan</h3><p><b><?=$tenantStats['customers']?></b> data</p></div><div class="card"><h3>Invoice</h3><p><b><?=$tenantStats['invoices']?></b> tagihan</p></div><div class="card"><h3>Pembayaran</h3><p><b><?=$tenantStats['payments']?></b> transaksi</p></div></div>
<div class="grid detail-grid" style="margin-top:18px"><section class="card"><h3>Ringkasan Akun</h3><p><b>Tenant UID:</b><br><?=e($t['tenant_uid'])?></p><p><b>Slug internal:</b><br><?=e($t['slug'])?></p><p><b>Email:</b><br><?=e($t['email'] ?: '-')?></p><p><b>Kota/Area:</b><br><?=e($t['city'] ?: '-')?></p><p><b>Dibuat:</b><br><?=e($t['created_at'])?></p><p><b>Disetujui:</b><br><?=e($t['approved_at'] ?: '-')?></p></section><section class="card"><h3>Kontrol Akun</h3><p class="footer-note">Disable memblokir login mitra tanpa menghapus data/DB.</p><div class="actions"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="tenant_id" value="<?=$id?>"><?php if($t['status']==='pending'):?><button class="btn btn-ok" name="action" value="approve">Approve & Aktifkan</button><?php endif;?><?php if($t['status']==='active'):?><button onclick="return confirm('Nonaktifkan login mitra ini?')" class="btn btn-warn" name="action" value="disable">Disable Akun</button><?php endif;?><?php if(in_array($t['status'],['disabled','deleted'],true)):?><button class="btn btn-ok" name="action" value="reactivate">Aktifkan Lagi</button><?php endif;?></form></div><hr><h3>User Mitra</h3><?php foreach($users as $u):?><p><b><?=e($u['username'])?></b> — <?=e($u['name'])?><br><small><?=e($u['role'])?> · <?=e($u['status'])?> · last login <?=e($u['last_login_at'] ?: '-')?></small></p><?php endforeach;?></section><section class="card"><h3>Database</h3><p><b>Status:</b> <?=e($dbRow?'ready':'belum dibuat')?></p><p><b>Path:</b><br><small><?=e($dbRow['db_path'] ?? '-')?></small></p><p><b>Schema:</b> <?=e($dbRow['schema_version'] ?? '-')?></p><p class="footer-note">DB tenant tidak dihapus saat disable.</p></section></div>
<section class="card" style="margin-top:18px"><h3>Aktivitas Terakhir</h3><div class="table-wrap"><table class="table"><thead><tr><th>Waktu</th><th>Event</th><th>Catatan</th></tr></thead><tbody><?php foreach($events as $ev):?><tr><td><?=e($ev['created_at'])?></td><td><?=e($ev['event_type'])?></td><td><?=e($ev['notes'])?></td></tr><?php endforeach;?><?php if(!$events):?><tr><td colspan="3">Belum ada aktivitas.</td></tr><?php endif;?></tbody></table></div></section></main></div><?php render_footer(); ?>
