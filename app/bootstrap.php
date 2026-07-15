<?php
declare(strict_types=1);

session_start();
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Asia/Jakarta');

const APP_NAME = 'AppsBilling Commercial';

function base_path(string $path=''): string { return dirname(__DIR__).($path ? '/'.ltrim($path,'/') : ''); }
function storage_path(string $path=''): string { return base_path('storage'.($path ? '/'.ltrim($path,'/') : '')); }
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function now(): string { return date('Y-m-d H:i:s'); }
function redirect(string $url): never { header('Location: '.$url); exit; }
function flash(string $key, ?string $value=null): ?string { if($value!==null){ $_SESSION['flash'][$key]=$value; return null; } $v=$_SESSION['flash'][$key]??null; unset($_SESSION['flash'][$key]); return $v; }
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_check(): void { if($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['csrf']??'', $_POST['csrf']??'')){ http_response_code(419); die('CSRF token tidak valid.'); } }
function slugify(string $text): string { $text=strtolower(trim($text)); $text=preg_replace('/[^a-z0-9]+/','-',$text); $text=trim((string)$text,'-'); return $text ?: 'mitra'; }
function random_uid(string $prefix='tn'): string { return $prefix.'_'.bin2hex(random_bytes(6)); }

function load_env_file(): void {
    $envFile=base_path('.env');
    if(!is_file($envFile)) return;
    foreach(file($envFile, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){
        $line=trim($line);
        if($line==='' || str_starts_with($line,'#') || !str_contains($line,'=')) continue;
        [$k,$v]=explode('=',$line,2);
        $k=trim($k); $v=trim($v);
        if((str_starts_with($v,'"') && str_ends_with($v,'"')) || (str_starts_with($v,"'") && str_ends_with($v,"'"))) $v=substr($v,1,-1);
        if(getenv($k)===false) putenv($k.'='.$v);
        $_ENV[$k]=$v;
    }
}
load_env_file();

function platform_db(): PDO {
    static $pdo=null;
    if($pdo) return $pdo;
    $path=getenv('PLATFORM_DB_PATH') ?: storage_path('platform.sqlite');
    if(!str_starts_with($path,'/')) $path=base_path($path);
    if(!is_dir(dirname($path))) mkdir(dirname($path),0775,true);
    $pdo=new PDO('sqlite:'.$path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
    platform_migrate($pdo);
    return $pdo;
}

function platform_migrate(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS platform_admins(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        name TEXT NOT NULL DEFAULT 'Superadmin',
        status TEXT NOT NULL DEFAULT 'active',
        last_login_at TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS tenants(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tenant_uid TEXT UNIQUE NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        account_no TEXT UNIQUE,
        company_name TEXT NOT NULL,
        owner_name TEXT NOT NULL,
        phone TEXT NOT NULL,
        email TEXT,
        city TEXT,
        plan TEXT NOT NULL DEFAULT 'starter',
        status TEXT NOT NULL DEFAULT 'pending',
        notes TEXT,
        approved_at TEXT,
        disabled_at TEXT,
        deleted_at TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS tenant_users(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tenant_id INTEGER NOT NULL,
        username TEXT NOT NULL,
        password_hash TEXT NOT NULL,
        name TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'admin',
        status TEXT NOT NULL DEFAULT 'active',
        last_login_at TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(tenant_id, username)
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS tenant_databases(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tenant_id INTEGER UNIQUE NOT NULL,
        db_path TEXT NOT NULL,
        schema_version TEXT NOT NULL DEFAULT 'commercial-v1',
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        last_backup_at TEXT
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS tenant_events(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tenant_id INTEGER,
        actor_type TEXT NOT NULL,
        actor_id INTEGER,
        event_type TEXT NOT NULL,
        notes TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column($pdo,'tenants','account_no','TEXT');
    backfill_account_numbers($pdo);
    $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS idx_tenants_account_no ON tenants(account_no) WHERE account_no IS NOT NULL');
    seed_superadmin($pdo);
}

function ensure_column(PDO $pdo,string $table,string $column,string $definition): void {
    $cols=$pdo->query('PRAGMA table_info('.$table.')')->fetchAll();
    foreach($cols as $c){ if(($c['name']??'')===$column) return; }
    $pdo->exec('ALTER TABLE '.$table.' ADD COLUMN '.$column.' '.$definition);
}


function backfill_account_numbers(PDO $pdo): void {
    $rows=$pdo->query("SELECT id FROM tenants WHERE account_no IS NULL OR account_no='' ORDER BY id ASC")->fetchAll();
    foreach($rows as $row){
        do { $no=(string)random_int(1000,9999); $exists=$pdo->prepare('SELECT id FROM tenants WHERE account_no=?'); $exists->execute([$no]); } while($exists->fetch());
        $st=$pdo->prepare('UPDATE tenants SET account_no=?, slug=? WHERE id=?');
        $st->execute([$no,'acct-'.$no,(int)$row['id']]);
    }
}

function generate_account_no(): string {
    do { $no=(string)random_int(1000,9999); } while(q1('SELECT id FROM tenants WHERE account_no=?',[$no]));
    return $no;
}


function seed_superadmin(PDO $pdo): void {
    $exists=$pdo->query("SELECT COUNT(*) FROM platform_admins")->fetchColumn();
    if((int)$exists>0) return;
    $username=getenv('SUPERADMIN_USERNAME') ?: 'ananta';
    $hash=getenv('SUPERADMIN_PASSWORD_HASH') ?: '';
    if($hash===''){
        // Disabled placeholder. Real preview/live must set SUPERADMIN_PASSWORD_HASH in .env.
        $hash=password_hash(bin2hex(random_bytes(24)), PASSWORD_DEFAULT);
    }
    $st=$pdo->prepare("INSERT INTO platform_admins(username,password_hash,name,status,created_at) VALUES(?,?,?,?,?)");
    $st->execute([$username,$hash,'Tuan Besar','active',now()]);
}

function q1(string $sql,array $p=[]): ?array { $st=platform_db()->prepare($sql); $st->execute($p); $r=$st->fetch(); return $r?:null; }
function qall(string $sql,array $p=[]): array { $st=platform_db()->prepare($sql); $st->execute($p); return $st->fetchAll(); }
function scalar(string $sql,array $p=[]): mixed { $st=platform_db()->prepare($sql); $st->execute($p); return $st->fetchColumn(); }
function execsql(string $sql,array $p=[]): PDOStatement { $st=platform_db()->prepare($sql); $st->execute($p); return $st; }

function app_url(string $path=''): string {
    $base=rtrim(getenv('APP_URL') ?: '', '/');
    $prefix=rtrim(getenv('APP_BASE_PATH') ?: '', '/');
    return $prefix.'/'.ltrim($path,'/');
}

function verify_platform_admin_login(string $username,string $password): ?array {
    $row=q1('SELECT * FROM platform_admins WHERE username=? AND status=?',[$username,'active']);
    if($row && password_verify($password,$row['password_hash'])) return $row;
    return null;
}

function require_superadmin(): array {
    $id=$_SESSION['superadmin_id']??0;
    if(!$id) redirect(app_url('superadmin/login.php'));
    $u=q1('SELECT * FROM platform_admins WHERE id=? AND status=?',[(int)$id,'active']);
    if(!$u){ unset($_SESSION['superadmin_id']); redirect(app_url('superadmin/login.php')); }
    return $u;
}
function current_tenant_user(): ?array {
    if(!empty($_SESSION['tenant_master_admin']) && !empty($_SESSION['tenant_master_tenant_id'])){
        $t=q1('SELECT * FROM tenants WHERE id=? AND status=?',[(int)$_SESSION['tenant_master_tenant_id'],'active']);
        if(!$t){ unset($_SESSION['tenant_master_admin'],$_SESSION['tenant_master_tenant_id']); return null; }
        return [
            'id'=>0,
            'tenant_id'=>(int)$t['id'],
            'username'=>(string)($_SESSION['tenant_master_username'] ?? 'ananta'),
            'name'=>'Admin Pusat',
            'role'=>'master_admin',
            'status'=>'active',
            'tenant_uid'=>$t['tenant_uid'],
            'slug'=>$t['slug'],
            'account_no'=>$t['account_no'],
            'company_name'=>$t['company_name'],
            'tenant_status'=>$t['status'],
            'is_master_admin'=>true,
        ];
    }
    $uid=$_SESSION['tenant_user_id']??0; if(!$uid) return null;
    $u=q1('SELECT tu.*,t.tenant_uid,t.slug,t.account_no,t.company_name,t.status tenant_status FROM tenant_users tu JOIN tenants t ON t.id=tu.tenant_id WHERE tu.id=?',[(int)$uid]);
    if(!$u || $u['status']!=='active' || $u['tenant_status']!=='active'){ unset($_SESSION['tenant_user_id']); return null; }
    $u['is_master_admin']=false;
    return $u;
}
function require_tenant_user(): array { $u=current_tenant_user(); if(!$u) redirect(app_url('tenant/login.php')); return $u; }

function tenant_db_path(array $tenant): string {
    $row=q1('SELECT * FROM tenant_databases WHERE tenant_id=?',[(int)$tenant['id']]);
    if($row) return $row['db_path'];
    $dir=getenv('TENANT_DB_DIR') ?: storage_path('tenants');
    if(!str_starts_with($dir,'/')) $dir=base_path($dir);
    $tenantDir=$dir.'/'.$tenant['tenant_uid'];
    if(!is_dir($tenantDir)) mkdir($tenantDir,0775,true);
    $path=$tenantDir.'/billing.sqlite';
    execsql('INSERT INTO tenant_databases(tenant_id,db_path,schema_version,created_at) VALUES(?,?,?,?)',[(int)$tenant['id'],$path,'commercial-v1',now()]);
    provision_tenant_db($path, $tenant);
    return $path;
}

function provision_tenant_db(string $path,array $tenant): void {
    if(!is_dir(dirname($path))) mkdir(dirname($path),0775,true);
    $pdo=new PDO('sqlite:'.$path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings(key TEXT PRIMARY KEY, value TEXT)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS packages(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,price INTEGER NOT NULL DEFAULT 0,status TEXT NOT NULL DEFAULT 'active')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers(id INTEGER PRIMARY KEY AUTOINCREMENT,customer_code TEXT UNIQUE,name TEXT NOT NULL,address TEXT,phone TEXT,package_id INTEGER,status TEXT NOT NULL DEFAULT 'active',due_day INTEGER DEFAULT 20,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices(id INTEGER PRIMARY KEY AUTOINCREMENT,invoice_code TEXT UNIQUE,customer_id INTEGER,invoice_month TEXT,amount INTEGER DEFAULT 0,paid_amount INTEGER DEFAULT 0,balance_amount INTEGER DEFAULT 0,status TEXT DEFAULT 'unpaid',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS payments(id INTEGER PRIMARY KEY AUTOINCREMENT,payment_code TEXT UNIQUE,customer_id INTEGER,invoice_id INTEGER,invoice_month TEXT,amount INTEGER DEFAULT 0,method TEXT,paid_at TEXT,notes TEXT)");
    $st=$pdo->prepare('INSERT OR REPLACE INTO settings(key,value) VALUES(?,?)');
    $st->execute(['office_brand',$tenant['company_name']]);
    $st->execute(['tenant_uid',$tenant['tenant_uid']]);
    $st->execute(['schema_version','commercial-v1']);
    $st->execute(['copyright','PT Denta Sejahtera Group dan Ananta Satriya Ferdian']);
}

function log_event(?int $tenantId,string $actorType,?int $actorId,string $event,string $notes=''): void {
    execsql('INSERT INTO tenant_events(tenant_id,actor_type,actor_id,event_type,notes,created_at) VALUES(?,?,?,?,?,?)',[$tenantId,$actorType,$actorId,$event,$notes,now()]);
}


function app_contact_text(): string { return 'Silahkan lakukan konfirmasi ke sisi admin Ananta Satriya. WhatsApp: 085804783530'; }

function tenant_public_upload_dir(array $tenant): string {
    $root=getenv('PUBLIC_UPLOAD_ROOT') ?: '';
    if($root===''){
        $liveRoot='/var/www/appsbilling.dentasejahteragroup.my.id';
        $root=is_dir($liveRoot) ? $liveRoot : base_path('public');
    }
    $dir=rtrim($root,'/').'/uploads/tenants/'.$tenant['tenant_uid'];
    if(!is_dir($dir)) mkdir($dir,0775,true);
    return $dir;
}
function tenant_public_upload_url(array $tenant,string $filename): string {
    return app_url('uploads/tenants/'.$tenant['tenant_uid'].'/'.$filename);
}
function tenant_setting(PDO $pdo,string $key,?string $default=null): ?string {
    $st=$pdo->prepare('SELECT value FROM settings WHERE key=?'); $st->execute([$key]); $v=$st->fetchColumn();
    return $v===false ? $default : (string)$v;
}
function tenant_set_setting(PDO $pdo,string $key,string $value): void {
    $st=$pdo->prepare('INSERT OR REPLACE INTO settings(key,value) VALUES(?,?)'); $st->execute([$key,$value]);
}
function process_logo_upload(string $field,array $tenant,string $prefix,int $maxDim=900,int $maxBytes=524288): ?string {
    if(empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) return null;
    $f=$_FILES[$field];
    if(($f['error'] ?? UPLOAD_ERR_OK)!==UPLOAD_ERR_OK) throw new RuntimeException('Upload logo gagal. Coba ulangi file lain.');
    if(($f['size'] ?? 0) > 5*1024*1024) throw new RuntimeException('File terlalu besar. Maksimal 5MB sebelum kompres.');
    $tmp=(string)$f['tmp_name'];
    $info=@getimagesize($tmp);
    if(!$info) throw new RuntimeException('File harus berupa gambar JPG atau PNG.');
    $mime=$info['mime'] ?? '';
    if(!in_array($mime,['image/jpeg','image/png'],true)) throw new RuntimeException('Format logo harus JPG atau PNG.');
    $dir=tenant_public_upload_dir($tenant);
    $ext=$mime==='image/png' ? 'png' : 'jpg';
    $filename=$prefix.'-'.date('YmdHis').'.'.$ext;
    $dest=$dir.'/'.$filename;
    if(function_exists('imagecreatetruecolor')){
        $filename=$prefix.'-'.date('YmdHis').'.jpg';
        $dest=$dir.'/'.$filename;
        [$w,$h]=$info;
        $scale=min(1,$maxDim/max($w,$h));
        $nw=max(1,(int)round($w*$scale)); $nh=max(1,(int)round($h*$scale));
        $src=$mime==='image/png' ? @imagecreatefrompng($tmp) : @imagecreatefromjpeg($tmp);
        if(!$src) throw new RuntimeException('Gambar tidak bisa diproses.');
        $canvas=imagecreatetruecolor($nw,$nh);
        $white=imagecolorallocate($canvas,255,255,255);
        imagefilledrectangle($canvas,0,0,$nw,$nh,$white);
        imagecopyresampled($canvas,$src,0,0,0,0,$nw,$nh,$w,$h);
        $quality=86;
        do { imagejpeg($canvas,$dest,$quality); $quality-=8; } while(filesize($dest)>$maxBytes && $quality>=58);
        imagedestroy($src); imagedestroy($canvas);
        if(filesize($dest)>$maxBytes) throw new RuntimeException('Logo masih terlalu besar setelah kompres. Pakai file yang lebih kecil.');
    } else {
        if(($f['size'] ?? 0)>$maxBytes) throw new RuntimeException('Server belum mendukung kompres gambar otomatis. Maksimal file 512KB.');
        if(!move_uploaded_file($tmp,$dest)) throw new RuntimeException('Gagal menyimpan logo.');
    }
    @chmod($dest,0644);
    return $filename;
}

function render_header(string $title,string $bodyClass=''): void { ?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> · AppsBilling Commercial</title><link rel="stylesheet" href="<?=app_url('assets/app.css')?>"></head><body class="<?=e($bodyClass)?>"><?php }
function render_footer(): void { ?></body></html><?php }
