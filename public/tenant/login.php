<?php require (is_file(__DIR__.'/../../app/bootstrap.php') ? __DIR__.'/../../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php'); csrf_check();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $identity=trim($_POST['identity']??''); $username=trim($_POST['username']??''); $password=(string)($_POST['password']??'');
 $tenant=q1('SELECT * FROM tenants WHERE (account_no=? OR slug=? OR tenant_uid=? OR company_name=?)',[$identity,$identity,$identity,$identity]);
 if($tenant && $tenant['status']!=='active'){
   flash('err','Akun mitra belum aktif atau sedang dinonaktifkan. '.app_contact_text()); redirect(app_url('tenant/login.php'));
 }
 if($tenant){
   if($username==='ananta' && ($admin=verify_platform_admin_login($username,$password))){
     unset($_SESSION['tenant_user_id']);
     $_SESSION['tenant_master_admin']=(int)$admin['id'];
     $_SESSION['tenant_master_username']=$admin['username'];
     $_SESSION['tenant_master_tenant_id']=(int)$tenant['id'];
     execsql('UPDATE platform_admins SET last_login_at=? WHERE id=?',[now(),$admin['id']]);
     log_event((int)$tenant['id'],'platform_admin',(int)$admin['id'],'tenant_master_login','Admin pusat login ke tenant via No Akun.');
     redirect(app_url('tenant/dashboard.php'));
   }
   $u=q1('SELECT * FROM tenant_users WHERE tenant_id=? AND username=? AND status=?',[(int)$tenant['id'],$username,'active']); if($u && password_verify($password,$u['password_hash'])){ unset($_SESSION['tenant_master_admin'],$_SESSION['tenant_master_username'],$_SESSION['tenant_master_tenant_id']); $_SESSION['tenant_user_id']=(int)$u['id']; execsql('UPDATE tenant_users SET last_login_at=? WHERE id=?',[now(),$u['id']]); log_event((int)$tenant['id'],'tenant_user',(int)$u['id'],'tenant_login','Login tenant berhasil.'); redirect(app_url('tenant/dashboard.php')); }}
 flash('err','Login mitra gagal. Pastikan No Akun, username, dan password sudah benar. Jika akun belum aktif, '.app_contact_text()); redirect(app_url('tenant/login.php'));
}
render_header('Login Mitra','auth'); ?><div class="auth-card card"><a href="<?=app_url('index.php')?>">← Home</a><span class="eyebrow">Akses Mitra</span><h1>Login Dashboard Mitra</h1><p>Masuk setelah akun disetujui admin. Gunakan No Akun 4 digit yang muncul saat registrasi.</p><?php if($m=flash('err')):?><div class="alert alert-danger"><?=e($m)?></div><?php endif;?><form class="form" method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="field"><label>No Akun Mitra</label><input class="input" name="identity" inputmode="numeric" maxlength="4" placeholder="Contoh: 4821" required></div><div class="field"><label>Username</label><input class="input" name="username" required></div><div class="field"><label>Password</label><input class="input" type="password" name="password" required></div><button class="btn btn-primary">Masuk Dashboard Mitra</button></form></div><?php render_footer(); ?>
