<?php require (is_file(__DIR__.'/../app/bootstrap.php') ? __DIR__.'/../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php'); csrf_check();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $company=trim($_POST['company_name']??''); $owner=trim($_POST['owner_name']??''); $phone=trim($_POST['phone']??''); $email=trim($_POST['email']??''); $city=trim($_POST['city']??''); $username=trim($_POST['username']??''); $password=(string)($_POST['password']??'');
    if($company===''||$owner===''||$phone===''||$username===''||strlen($password)<6){ flash('err','Lengkapi data wajib. Password minimal 6 karakter.'); redirect(app_url('register.php')); }
    $accountNo=generate_account_no();
    $slug='acct-'.$accountNo;
    $uid=random_uid();
    $pdo=platform_db(); $pdo->beginTransaction();
    try{
        $st=$pdo->prepare('INSERT INTO tenants(tenant_uid,slug,account_no,company_name,owner_name,phone,email,city,status,created_at) VALUES(?,?,?,?,?,?,?,?,?,?)');
        $st->execute([$uid,$slug,$accountNo,$company,$owner,$phone,$email,$city,'pending',now()]); $tenantId=(int)$pdo->lastInsertId();
        $st=$pdo->prepare('INSERT INTO tenant_users(tenant_id,username,password_hash,name,role,status,created_at) VALUES(?,?,?,?,?,?,?)');
        $st->execute([$tenantId,$username,password_hash($password,PASSWORD_DEFAULT),$owner,'owner','active',now()]);
        $pdo->commit(); log_event($tenantId,'public',null,'tenant_registered','Registrasi mitra baru menunggu approval.');
    }catch(Throwable $e){ $pdo->rollBack(); flash('err','Registrasi gagal: username/slug mungkin sudah dipakai.'); redirect(app_url('register.php')); }
    flash('ok','Registrasi berhasil. No akun Anda: '.$accountNo.'. Simpan nomor ini untuk login setelah disetujui admin.'); redirect(app_url('register.php'));
}
render_header('Registrasi Mitra','auth'); ?>
<div class="auth-card card register-card"><a href="<?=app_url('index.php')?>">← Kembali ke beranda</a><span class="eyebrow">Pendaftaran Mitra</span><h1>Ajukan akun AppsBilling</h1><p>Isi data usaha dengan rapi. Setelah dikirim, akun masuk antrean review admin. Kalau disetujui, sistem otomatis membuat No Akun 4 digit dan ruang billing terpisah.</p><?php if($m=flash('ok')):?><div class="alert alert-ok"><?=e($m)?></div><?php endif;?><?php if($m=flash('err')):?><div class="alert alert-danger"><?=e($m)?></div><?php endif;?>
<div class="guide-steps"><div><b>1</b><span>Daftar</span><small>Isi nama usaha, PIC, dan login admin.</small></div><div><b>2</b><span>Review</span><small>Admin mengecek dan approve akun.</small></div><div><b>3</b><span>Aktif</span><small>Mitra login memakai No Akun + username.</small></div></div>
<form class="form" method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="field"><label>Nama usaha / mitra *</label><input class="input" name="company_name" placeholder="Contoh: MRT NET" required></div><div class="field"><label>Nama owner / PIC *</label><input class="input" name="owner_name" placeholder="Nama penanggung jawab" required></div><div class="field"><label>No. WhatsApp aktif *</label><input class="input" name="phone" placeholder="08xxxxxxxxxx" required></div><div class="field"><label>Email</label><input class="input" type="email" name="email" placeholder="ops@mrtnet.id"></div><div class="field"><label>Kota / Area layanan</label><input class="input" name="city" placeholder="Contoh: Makassar"></div><div class="field"><label>Username admin mitra *</label><input class="input" name="username" placeholder="Contoh: adminmrt" required></div><div class="field"><label>Password admin mitra *</label><input class="input" type="password" name="password" minlength="6" required><small>Minimal 6 karakter. Simpan baik-baik, nanti dipakai setelah akun disetujui.</small></div><button class="btn btn-primary">Kirim Pendaftaran</button></form><p class="footer-note">No Akun 4 digit dibuat otomatis oleh sistem, bukan dari nama usaha.</p></div><?php render_footer(); ?>
