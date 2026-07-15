<?php
require_once (is_file(__DIR__.'/../../app/bootstrap.php') ? __DIR__.'/../../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php');
$embedded=defined('TENANT_SETTINGS_EMBED');
$u=$u ?? require_tenant_user();
$tenant=$tenant ?? q1('SELECT * FROM tenants WHERE id=?',[(int)$u['tenant_id']]);
$db=$db ?? tenant_db_path($tenant);
$pdo=$pdo ?? new PDO('sqlite:'.$db);
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    try{
        $brand=trim($_POST['office_brand'] ?? $tenant['company_name']);
        if($brand==='') $brand=$tenant['company_name'];
        tenant_set_setting($pdo,'office_brand',$brand);
        if($fn=process_logo_upload('app_logo',$tenant,'app-logo',900,524288)) tenant_set_setting($pdo,'app_logo',tenant_public_upload_url($tenant,$fn));
        if($fn=process_logo_upload('receipt_logo',$tenant,'receipt-logo',1200,524288)) tenant_set_setting($pdo,'receipt_logo',tenant_public_upload_url($tenant,$fn));
        tenant_set_setting($pdo,'copyright','PT Denta Sejahtera Group dan Ananta Satriya Ferdian');
        log_event((int)$tenant['id'],!empty($u['is_master_admin'])?'platform_admin':'tenant_user',(int)($u['id'] ?? 0),'tenant_branding_updated','Branding/logo tenant diperbarui.');
        flash('ok','Pengaturan branding tersimpan. Logo otomatis divalidasi dan dikompres bila server mendukung.');
    }catch(Throwable $e){ flash('err',$e->getMessage()); }
    redirect(tenant_v3_page_url('branding-settings'));
}
$appLogo=tenant_setting($pdo,'app_logo'); $receiptLogo=tenant_setting($pdo,'receipt_logo'); $brand=tenant_setting($pdo,'office_brand',$tenant['company_name']); $copyright=tenant_setting($pdo,'copyright','PT Denta Sejahtera Group dan Ananta Satriya Ferdian');
if(!$embedded) tenant_v3_render_header('Logo & Branding',$tenant,$u);
?>
<div class="row"><div class="col-8"><div class="card card-info"><div class="card-header"><h3 class="card-title">Pengaturan kantor & logo</h3></div><div class="card-body"><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="form-group mb-3"><label>Nama brand di kwitansi</label><input class="form-control" name="office_brand" value="<?=e($brand)?>"></div><div class="form-group mb-3"><label>Logo aplikasi JPG/PNG</label><input class="form-control" type="file" name="app_logo" accept="image/png,image/jpeg"><small>Maksimal 5MB sebelum kompres, target hasil ±512KB.</small></div><div class="form-group mb-3"><label>Logo kwitansi JPG/PNG</label><input class="form-control" type="file" name="receipt_logo" accept="image/png,image/jpeg"><small>Kalau file kebesaran, server akan mencoba kompres/resize bila GD tersedia.</small></div><button class="btn btn-success">Simpan Branding</button></form></div></div></div><div class="col-4"><div class="card"><div class="card-header"><h3 class="card-title">Preview</h3></div><div class="card-body"><p><b><?=e($brand)?></b></p><p>No Akun: <span class="badge badge-warning"><?=e($tenant['account_no'] ?? '-')?></span></p><hr><small>Logo aplikasi</small><div class="mb-3"><?php if($appLogo):?><img style="max-width:180px;max-height:90px;object-fit:contain" src="<?=e($appLogo)?>" alt="Logo aplikasi"><?php else:?><div class="empty-v3">Belum ada logo aplikasi</div><?php endif;?></div><small>Logo kwitansi</small><div class="mb-3"><?php if($receiptLogo):?><img style="max-width:220px;max-height:100px;object-fit:contain" src="<?=e($receiptLogo)?>" alt="Logo kwitansi"><?php else:?><div class="empty-v3">Belum ada logo kwitansi</div><?php endif;?></div><p><b>Copyright:</b><br><?=e($copyright)?></p></div></div></div></div>

<?php if(!$embedded) tenant_v3_render_footer(); ?>
