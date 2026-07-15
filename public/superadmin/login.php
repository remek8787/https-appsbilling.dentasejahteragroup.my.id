<?php require (is_file(__DIR__.'/../../app/bootstrap.php') ? __DIR__.'/../../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php'); csrf_check();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $u=trim($_POST['username']??''); $p=(string)($_POST['password']??''); $row=q1('SELECT * FROM platform_admins WHERE username=? AND status=?',[$u,'active']);
 if($row && password_verify($p,$row['password_hash'])){ $_SESSION['superadmin_id']=(int)$row['id']; execsql('UPDATE platform_admins SET last_login_at=? WHERE id=?',[now(),$row['id']]); redirect(app_url('superadmin/index.php')); }
 flash('err','Login superadmin gagal.'); redirect(app_url('superadmin/login.php'));
}
render_header('Login Admin','auth'); ?><div class="auth-card card"><a href="<?=app_url('index.php')?>">← Home</a><h1>Admin</h1><p>Panel admin untuk approve, disable, dan kelola akun mitra.</p><?php if($m=flash('err')):?><div class="alert alert-danger"><?=e($m)?></div><?php endif;?><form class="form" method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="field"><label>Username</label><input class="input" name="username" required autofocus></div><div class="field"><label>Password</label><input class="input" type="password" name="password" required></div><button class="btn btn-primary">Masuk</button></form></div><?php render_footer(); ?>
