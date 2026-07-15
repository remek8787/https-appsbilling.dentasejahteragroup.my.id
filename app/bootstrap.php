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
    $pdo->exec("CREATE TABLE IF NOT EXISTS packages(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,speed TEXT NOT NULL DEFAULT '',price INTEGER NOT NULL DEFAULT 0,is_active INTEGER NOT NULL DEFAULT 1,status TEXT NOT NULL DEFAULT 'active',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS bank_accounts(id INTEGER PRIMARY KEY AUTOINCREMENT,bank_name TEXT NOT NULL,account_number TEXT NOT NULL DEFAULT '',account_name TEXT NOT NULL DEFAULT '',label TEXT NOT NULL DEFAULT '',is_active INTEGER NOT NULL DEFAULT 1,notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers(id INTEGER PRIMARY KEY AUTOINCREMENT,customer_code TEXT UNIQUE,name TEXT NOT NULL,address TEXT,phone TEXT,package_id INTEGER,status TEXT NOT NULL DEFAULT 'active',due_day INTEGER DEFAULT 20,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices(id INTEGER PRIMARY KEY AUTOINCREMENT,invoice_code TEXT UNIQUE,customer_id INTEGER,invoice_month TEXT,amount INTEGER DEFAULT 0,paid_amount INTEGER DEFAULT 0,balance_amount INTEGER DEFAULT 0,status TEXT DEFAULT 'unpaid',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS payments(id INTEGER PRIMARY KEY AUTOINCREMENT,payment_code TEXT UNIQUE,customer_id INTEGER,invoice_id INTEGER,invoice_month TEXT,amount INTEGER DEFAULT 0,method TEXT,paid_at TEXT,notes TEXT,bank_account_id INTEGER)");
    $st=$pdo->prepare('INSERT OR REPLACE INTO settings(key,value) VALUES(?,?)');
    $st->execute(['office_brand',$tenant['company_name']]);
    $st->execute(['tenant_uid',$tenant['tenant_uid']]);
    $st->execute(['schema_version','commercial-v1']);
    $st->execute(['copyright','PT Denta Sejahtera Group dan Ananta Satriya Ferdian']);
}


function tenant_db_columns(PDO $pdo,string $table): array {
    $cols=[]; foreach($pdo->query('PRAGMA table_info('.$table.')')->fetchAll(PDO::FETCH_ASSOC) as $r){ $cols[$r['name']]=true; } return $cols;
}
function tenant_ensure_v3_core_schema(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings(key TEXT PRIMARY KEY, value TEXT)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS packages(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,price INTEGER NOT NULL DEFAULT 0,status TEXT NOT NULL DEFAULT 'active')");
    $cols=tenant_db_columns($pdo,'packages');
    if(!isset($cols['speed'])) $pdo->exec("ALTER TABLE packages ADD COLUMN speed TEXT NOT NULL DEFAULT ''");
    if(!isset($cols['is_active'])) $pdo->exec("ALTER TABLE packages ADD COLUMN is_active INTEGER NOT NULL DEFAULT 1");
    if(!isset($cols['created_at'])) $pdo->exec("ALTER TABLE packages ADD COLUMN created_at TEXT NOT NULL DEFAULT ''");
    $pdo->exec("UPDATE packages SET is_active=CASE WHEN COALESCE(status,'active')='active' THEN 1 ELSE 0 END WHERE is_active IS NULL");
    $pdo->exec("CREATE TABLE IF NOT EXISTS bank_accounts(id INTEGER PRIMARY KEY AUTOINCREMENT,bank_name TEXT NOT NULL,account_number TEXT NOT NULL DEFAULT '',account_name TEXT NOT NULL DEFAULT '',label TEXT NOT NULL DEFAULT '',is_active INTEGER NOT NULL DEFAULT 1,notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers(id INTEGER PRIMARY KEY AUTOINCREMENT,customer_code TEXT UNIQUE,name TEXT NOT NULL,address TEXT,phone TEXT,package_id INTEGER,status TEXT NOT NULL DEFAULT 'active',due_day INTEGER DEFAULT 20,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices(id INTEGER PRIMARY KEY AUTOINCREMENT,invoice_code TEXT UNIQUE,customer_id INTEGER,invoice_month TEXT,amount INTEGER DEFAULT 0,paid_amount INTEGER DEFAULT 0,balance_amount INTEGER DEFAULT 0,status TEXT DEFAULT 'unpaid',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS payments(id INTEGER PRIMARY KEY AUTOINCREMENT,payment_code TEXT UNIQUE,customer_id INTEGER,invoice_id INTEGER,invoice_month TEXT,amount INTEGER DEFAULT 0,method TEXT,paid_at TEXT,notes TEXT,bank_account_id INTEGER)");
}
function tenant_money(int $n): string { return 'Rp '.number_format($n,0,',','.'); }
function tenant_changed_summary(?array $before,?array $after): string {
    if(!$before || !$after) return '';
    $changes=[]; foreach($after as $k=>$v){ if(array_key_exists($k,$before) && (string)$before[$k] !== (string)$v) $changes[]=$k; }
    return $changes ? 'Kolom berubah: '.implode(', ',array_slice($changes,0,8)).'.' : 'Tidak ada perubahan besar.';
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


function tenant_v3_page_url(string $page,array $params=[]): string { return app_url('tenant/dashboard.php?'.http_build_query(array_merge(['page'=>$page],$params))); }
function tenant_v3_active(string $page): string { return (($_GET['page'] ?? 'dashboard')===$page) ? 'active' : ''; }
function tenant_v3_menu_open(array $items): bool { $cur=$_GET['page'] ?? 'dashboard'; foreach($items as $it){ if(($it[1] ?? '')===$cur) return true; } return false; }
function tenant_v3_menu_groups(): array {
    return [
        ['Dashboard','fas fa-tachometer-alt', [
            ['Dashboard','dashboard','far fa-circle'],
            ['Track record pendapatan','dashboard-income-summary','far fa-circle'],
            ['Analisa pendapatan','income-analysis','far fa-circle'],
        ]],
        ['Kelola data','fas fa-database', [
            ['Data pelanggan aktif','data-warga','far fa-circle'],
            ['Tambah pelanggan','add-warga','far fa-circle'],
            ['Pelanggan FREE / Gratis','data-pelanggan-free','far fa-circle'],
            ['Pelanggan OFF / Nonaktif','data-pelanggan-off','far fa-circle'],
            ['Tambah pelanggan OFF','add-pelanggan-off','far fa-circle'],
            ['Data diskon','data-diskon','far fa-circle'],
            ['Data deposit pelanggan','data-deposit-pelanggan','far fa-circle'],
            ['Data lokasi','data-lokasi','far fa-circle'],
            ['Data router','data-router','far fa-circle'],
            ['Data tipe pembayaran','data-tipe-pembayaran','far fa-circle'],
            ['Tambah tipe pembayaran','add-package','far fa-circle'],
            ['Data rekening','data-rekening','far fa-circle'],
            ['Tambah rekening','add-bank','far fa-circle'],
        ]],
        ['Admin sistem','fas fa-user-shield', [
            ['Data user','data-user','far fa-circle'],
            ['Tambah user','add-user','far fa-circle'],
            ['Pengaturan kantor','office-settings','far fa-circle'],
            ['Logo & Branding','branding-settings','far fa-circle'],
            ['Tutorial admin','admin-tutorial','far fa-circle'],
            ['Log aktivitas','activity-log','far fa-circle'],
        ]],
        ['Corporate','fas fa-building', [
            ['Data Corporate','corporate-customers','far fa-circle'],
            ['Tambah Corporate','add-corporate-customer','far fa-circle'],
            ['Tagihan Corporate','corporate-invoices','far fa-circle'],
            ['Buat Tagihan Corporate','add-corporate-invoice','far fa-circle'],
        ]],
        ['Kelola pembayaran','fas fa-money-bill-wave', [
            ['Pembayaran langganan','data-ipl','far fa-circle'],
            ['Tambah pembayaran','add-laporan-ipl','far fa-circle'],
            ['Pembayaran umum','data-ipl-non','far fa-circle'],
            ['Data tagihan','data-tagihan','far fa-circle'],
            ['Tambah tagihan','add-tagihan','far fa-circle'],
            ['Sudah bayar','data-sudah-bayar','far fa-circle'],
            ['Belum bayar','data-belum-bayar-7121','far fa-circle'],
            ['Tim penagihan','collection-team','far fa-circle'],
        ]],
        ['Kelola PPPoE','fas fa-network-wired', [
            ['Data user PPPoE','data-user-pppoe','far fa-circle'],
            ['Data sesi PPPoE','data-session-ppoe','far fa-circle'],
            ['Data instalasi','data-installasi','far fa-circle'],
            ['Tiket pelanggan','dashboard-ticket-pelanggan','far fa-circle'],
        ]],
    ];
}
function tenant_v3_render_header(string $title,array $tenant,array $user): void {
    $brand=trim((string)($tenant['company_name'] ?? 'e-Billing DSG'));
    $account=(string)($tenant['account_no'] ?? '-');
    $groups=tenant_v3_menu_groups();
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#0f5ea8"><title><?=e($title)?> · e-Billing DSG</title><script>tailwind={config:{prefix:'tw-',corePlugins:{preflight:false},theme:{extend:{colors:{denta:{50:'#eef8ff',100:'#d9f0ff',500:'#1584d1',700:'#0f5ea8'},mint:{50:'#effef8',500:'#14b8a6'}},boxShadow:{soft:'0 18px 50px rgba(15,23,42,.10)',glow:'0 18px 40px rgba(20,184,166,.16)'}}}}}</script><script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="<?=app_url('assets/adminlte-clone.css')?>?v=commercial-v3-shell-20260716"><style>.tenant-account-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;background:#fff3cd;color:#664d03;border:1px solid rgba(102,77,3,.16);padding:.3rem .7rem;font-weight:800}.empty-v3{border:1px dashed #cbd5e1;border-radius:18px;background:rgba(255,255,255,.72);padding:24px;text-align:center;color:#64748b}.empty-v3 i{font-size:34px;color:#0f5ea8;margin-bottom:10px}.brand-logo{object-fit:contain;background:#fff}.content-wrapper .card{border-radius:14px;overflow:hidden}.small-box{border-radius:16px}.main-footer{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.nav-treeview{display:none}.menu-open>.nav-treeview{display:block}</style></head><body class="hold-transition sidebar-mini tw-bg-slate-100"><div class="wrapper tw-min-h-screen tw-bg-[radial-gradient(circle_at_top_left,_rgba(21,132,209,.16),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(20,184,166,.14),_transparent_28%)]"><nav class="main-header navbar navbar-expand navbar-yellow navbar-light"><a class="nav-link toggle" href="#">☰</a><ul class="navbar-nav"><li><a class="topbar-brand-copy" href="<?=tenant_v3_page_url('dashboard')?>"><b>e-Billing DSG</b><span>Billing Management System</span></a></li></ul><ul class="navbar-nav ml-auto"><li><span class="tenant-account-pill"><i class="fas fa-id-card"></i>No Akun <?=e($account)?></span></li><li><a href="<?=app_url('tenant/logout.php')?>">Keluar</a></li></ul></nav><aside class="main-sidebar sidebar-dark-primary elevation-4"><a class="brand-link ebilling-dsg-brand" href="<?=tenant_v3_page_url('dashboard')?>"><img class="brand-logo" src="<?=app_url('assets/dentanet-logo-20260715.jpg')?>" alt="DENTA NET"><span class="brand-text"><b>e-Billing DSG</b><small><?=e($brand)?></small></span></a><div class="sidebar"><div class="user-panel"><div class="image"><?=e(strtoupper(substr((string)($user['name'] ?? 'A'),0,1)))?></div><div><a href="#"><?=e($user['name'] ?? 'Admin')?></a><small><?=!empty($user['is_master_admin'])?'Mode Admin Pusat':'Administrator Mitra'?></small></div></div><nav class="mt-2"><ul class="nav nav-pills nav-sidebar flex-column"><?php foreach($groups as $g): $open=tenant_v3_menu_open($g[2]); ?><li class="nav-item has-treeview <?=$open?'menu-open':''?>"><a class="nav-link nav-parent <?=$open?'active':''?>" href="#"><i class="nav-icon <?=$g[1]?>"></i><p><?=e($g[0])?><i class="right fas fa-angle-left"></i></p></a><ul class="nav nav-treeview" <?=$open?'style="display:block"':''?>><?php foreach($g[2] as $m): ?><li class="nav-item"><a class="nav-link <?=tenant_v3_active($m[1])?>" href="<?=tenant_v3_page_url($m[1])?>"><i class="nav-icon <?=$m[2]?>"></i><p><?=e($m[0])?></p></a></li><?php endforeach; ?></ul></li><?php endforeach; ?></ul></nav></div></aside><div class="content-wrapper tw-backdrop-blur-sm"><section class="content-header"><div class="container-fluid"><div class="tw-rounded-2xl tw-bg-white/70 tw-border tw-border-white/70 tw-shadow-soft tw-p-4 tw-mb-3"><div class="d-flex justify-content-between align-items-start gap-3 flex-wrap"><div><h1><?=e($title)?></h1><ol class="breadcrumb"><li>Beranda</li><li><?=e($title)?></li></ol></div><div class="text-end"><span class="tenant-account-pill">Tenant kosong · DB terpisah</span></div></div></div></div></section><section class="content"><div class="container-fluid tw-space-y-3">
<?php }
function tenant_v3_render_footer(): void { ?></div></section></div><aside class="control-sidebar control-sidebar-dark"></aside><footer class="main-footer"><strong>e-Billing DSG System</strong><span>PT Denta Sejahtera Group</span><small>Billing Management System · Version 3.0 · Commercial Tenant</small></footer></div><script>document.querySelector('.toggle')?.addEventListener('click',function(e){e.preventDefault();document.body.classList.toggle('sidebar-collapse')});document.querySelectorAll('.has-treeview>.nav-parent').forEach(function(a){a.addEventListener('click',function(e){e.preventDefault();const li=a.parentElement;li.classList.toggle('menu-open');const tree=li.querySelector('.nav-treeview');if(tree)tree.style.display=li.classList.contains('menu-open')?'block':'none';});});</script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html><?php }
function tenant_v3_empty_module(string $title,string $desc=''): void { ?><div class="empty-v3"><i class="fas fa-database"></i><h3><?=e($title)?></h3><p><?=e($desc ?: 'Modul mengikuti AppsBilling V3. Data tenant ini masih kosong karena memakai database terpisah per No Akun.')?></p><span class="badge badge-info">DB tenant isolated</span></div><?php }

function render_header(string $title,string $bodyClass=''): void { ?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> · AppsBilling Commercial</title><link rel="stylesheet" href="<?=app_url('assets/app.css')?>"></head><body class="<?=e($bodyClass)?>"><?php }
function render_footer(): void { ?></body></html><?php }
