<?php require (is_file(__DIR__.'/../app/bootstrap.php') ? __DIR__.'/../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php'); csrf_check();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $company=trim($_POST['company_name']??''); $owner=trim($_POST['owner_name']??''); $phone=trim($_POST['phone']??''); $email=trim($_POST['email']??''); $city=trim($_POST['city']??''); $username=trim($_POST['username']??''); $password=(string)($_POST['password']??'');
    if($company===''||$owner===''||$phone===''||$username===''||strlen($password)<6){ flash('err','Lengkapi data wajib. Password minimal 6 karakter.'); redirect(app_url('register.php')); }
    $slug=slugify($company); $base=$slug; $i=2; while(q1('SELECT id FROM tenants WHERE slug=?',[$slug])){ $slug=$base.'-'.$i++; }
    $uid=random_uid();
    $pdo=platform_db(); $pdo->beginTransaction();
    try{
        $st=$pdo->prepare('INSERT INTO tenants(tenant_uid,slug,company_name,owner_name,phone,email,city,status,created_at) VALUES(?,?,?,?,?,?,?,?,?)');
        $st->execute([$uid,$slug,$company,$owner,$phone,$email,$city,'pending',now()]); $tenantId=(int)$pdo->lastInsertId();
        $st=$pdo->prepare('INSERT INTO tenant_users(tenant_id,username,password_hash,name,role,status,created_at) VALUES(?,?,?,?,?,?,?)');
        $st->execute([$tenantId,$username,password_hash($password,PASSWORD_DEFAULT),$owner,'owner','active',now()]);
        $pdo->commit(); log_event($tenantId,'public',null,'tenant_registered','Registrasi mitra baru menunggu approval.');
    }catch(Throwable $e){ $pdo->rollBack(); flash('err','Registrasi gagal: username/slug mungkin sudah dipakai.'); redirect(app_url('register.php')); }
    flash('ok','Registrasi berhasil. Akun menunggu approval superadmin.'); redirect(app_url('register.php'));
}
render_header('Registrasi Mitra','auth'); ?>
<div class="auth-card card"><a href="<?=app_url('index.php')?>">← Kembali</a><h1>Registrasi Mitra</h1><p>Daftarkan ISP/RTRW-net/client. Setelah disetujui superadmin, workspace billing akan aktif.</p><?php if($m=flash('ok')):?><div class="alert alert-ok"><?=e($m)?></div><?php endif;?><?php if($m=flash('err')):?><div class="alert alert-danger"><?=e($m)?></div><?php endif;?><form class="form" method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="field"><label>Nama usaha / mitra *</label><input class="input" name="company_name" required></div><div class="field"><label>Nama owner / PIC *</label><input class="input" name="owner_name" required></div><div class="field"><label>No. WhatsApp *</label><input class="input" name="phone" required></div><div class="field"><label>Email</label><input class="input" type="email" name="email"></div><div class="field"><label>Kota / Area</label><input class="input" name="city"></div><div class="field"><label>Username admin mitra *</label><input class="input" name="username" required></div><div class="field"><label>Password admin mitra *</label><input class="input" type="password" name="password" minlength="6" required></div><button class="btn btn-primary">Kirim Registrasi</button></form></div><?php render_footer(); ?>
