<?php
const V3_APP_NAME = 'e-Billing PT Denta Sejahtera Group System';
const V3_ACCOUNT = '6778';
const V3_USER = 'ananta';
const V3_PASS = '6789';
date_default_timezone_set('Asia/Jakarta');

function v3_db(){
    static $pdo=null;
    if($pdo) return $pdo;
    $dir=__DIR__.'/../data';
    if(!is_dir($dir)) mkdir($dir,0755,true);
    $pdo=new PDO('sqlite:'.$dir.'/ebilling-v3.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
    v3_init($pdo);
    return $pdo;
}
function v3_source_path(){ return realpath(__DIR__.'/../../data/billing.sqlite') ?: (__DIR__.'/../../data/billing.sqlite'); }
function v3_source_db(){ $pdo=new PDO('sqlite:'.v3_source_path()); $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC); return $pdo; }
function v3_exec_safe($pdo,$sql){ try{$pdo->exec($sql);}catch(Throwable $e){} }
function v3_init($pdo){
    $pdo->exec("CREATE TABLE IF NOT EXISTS auth_users(id INTEGER PRIMARY KEY AUTOINCREMENT,account TEXT NOT NULL,username TEXT UNIQUE NOT NULL,password_hash TEXT NOT NULL,name TEXT NOT NULL DEFAULT '',level TEXT NOT NULL DEFAULT 'Administrator',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $superHash=password_hash('260200',PASSWORD_DEFAULT);
    $st=$pdo->prepare('INSERT INTO auth_users(account,username,password_hash,name,level) VALUES(?,?,?,?,?) ON CONFLICT(username) DO UPDATE SET password_hash=excluded.password_hash,name=excluded.name,level=excluded.level,account=excluded.account');
    $st->execute([V3_ACCOUNT,'ananta',$superHash,'ANANTA','Superadmin']);

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,username TEXT UNIQUE NOT NULL,password_hash TEXT NOT NULL,role TEXT NOT NULL DEFAULT 'admin',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS packages (id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,speed TEXT NOT NULL,price INTEGER NOT NULL DEFAULT 0,is_active INTEGER NOT NULL DEFAULT 1,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (id INTEGER PRIMARY KEY AUTOINCREMENT,customer_code TEXT UNIQUE NOT NULL,name TEXT NOT NULL,address TEXT NOT NULL DEFAULT '',phone TEXT NOT NULL DEFAULT '',package_id INTEGER,registered_at TEXT NOT NULL,due_day INTEGER NOT NULL DEFAULT 20,is_active INTEGER NOT NULL DEFAULT 1,router_name TEXT NOT NULL DEFAULT '',onu_name TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,customer_status TEXT NOT NULL DEFAULT 'active',area_name TEXT NOT NULL DEFAULT '',pppoe_username TEXT NOT NULL DEFAULT '',installation_address_note TEXT NOT NULL DEFAULT '',last_followup_at TEXT NOT NULL DEFAULT '',last_followup_note TEXT NOT NULL DEFAULT '',risk_level TEXT NOT NULL DEFAULT 'normal',updated_at TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS payments (id INTEGER PRIMARY KEY AUTOINCREMENT,payment_code TEXT UNIQUE NOT NULL,customer_id INTEGER NOT NULL,invoice_month TEXT NOT NULL,amount INTEGER NOT NULL DEFAULT 0,paid_at TEXT NOT NULL,method TEXT NOT NULL DEFAULT 'Cash',received_by TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,bank_account_id INTEGER,source_system TEXT NOT NULL DEFAULT '',source_ref TEXT NOT NULL DEFAULT '',collector_note TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS expenses (id INTEGER PRIMARY KEY AUTOINCREMENT,expense_code TEXT UNIQUE NOT NULL,title TEXT NOT NULL,amount INTEGER NOT NULL DEFAULT 0,spent_at TEXT NOT NULL,method TEXT NOT NULL DEFAULT 'Cash',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS payment_logs (id INTEGER PRIMARY KEY AUTOINCREMENT,payment_id INTEGER,action TEXT NOT NULL,customer_id INTEGER,customer_name TEXT NOT NULL DEFAULT '',amount INTEGER NOT NULL DEFAULT 0,invoice_month TEXT NOT NULL DEFAULT '',actor TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices (id INTEGER PRIMARY KEY AUTOINCREMENT,invoice_code TEXT UNIQUE NOT NULL,customer_id INTEGER NOT NULL,invoice_month TEXT NOT NULL,amount INTEGER NOT NULL DEFAULT 0,due_day INTEGER NOT NULL DEFAULT 20,status TEXT NOT NULL DEFAULT 'unpaid',generated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,paid_at TEXT NOT NULL DEFAULT '',payment_id INTEGER,notes TEXT NOT NULL DEFAULT '',followup_status TEXT NOT NULL DEFAULT 'new',followup_note TEXT NOT NULL DEFAULT '',followup_at TEXT NOT NULL DEFAULT '',promise_date TEXT NOT NULL DEFAULT '',UNIQUE(customer_id,invoice_month))");
    $pdo->exec("CREATE TABLE IF NOT EXISTS bank_accounts (id INTEGER PRIMARY KEY AUTOINCREMENT,bank_name TEXT NOT NULL,account_number TEXT NOT NULL DEFAULT '',account_name TEXT NOT NULL DEFAULT '',label TEXT NOT NULL DEFAULT '',is_active INTEGER NOT NULL DEFAULT 1,notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS customer_events (id INTEGER PRIMARY KEY AUTOINCREMENT,customer_id INTEGER,event_type TEXT NOT NULL,title TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',actor TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS v3_sync_log(id INTEGER PRIMARY KEY AUTOINCREMENT,source_path TEXT NOT NULL,table_name TEXT NOT NULL,row_count INTEGER NOT NULL DEFAULT 0,synced_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS app_settings(setting_key TEXT PRIMARY KEY,setting_value TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS activity_logs(id INTEGER PRIMARY KEY AUTOINCREMENT,actor_id INTEGER,actor_name TEXT NOT NULL DEFAULT '',actor_username TEXT NOT NULL DEFAULT '',actor_level TEXT NOT NULL DEFAULT '',action TEXT NOT NULL,entity_type TEXT NOT NULL,entity_id INTEGER,entity_label TEXT NOT NULL DEFAULT '',summary TEXT NOT NULL DEFAULT '',before_json TEXT NOT NULL DEFAULT '',after_json TEXT NOT NULL DEFAULT '',ip_address TEXT NOT NULL DEFAULT '',user_agent TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_table_preferences(id INTEGER PRIMARY KEY AUTOINCREMENT,username TEXT NOT NULL,table_key TEXT NOT NULL,columns_json TEXT NOT NULL DEFAULT '[]',updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,UNIQUE(username,table_key))");
    $pdo->exec("CREATE TABLE IF NOT EXISTS corporate_customers (id INTEGER PRIMARY KEY AUTOINCREMENT,corporate_code TEXT UNIQUE NOT NULL,company_name TEXT NOT NULL,pic_name TEXT NOT NULL DEFAULT '',phone TEXT NOT NULL DEFAULT '',email TEXT NOT NULL DEFAULT '',tax_id TEXT NOT NULL DEFAULT '',address TEXT NOT NULL DEFAULT '',service_name TEXT NOT NULL DEFAULT '',service_detail TEXT NOT NULL DEFAULT '',monthly_amount INTEGER NOT NULL DEFAULT 0,due_day INTEGER NOT NULL DEFAULT 20,status TEXT NOT NULL DEFAULT 'active',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS corporate_invoices (id INTEGER PRIMARY KEY AUTOINCREMENT,invoice_code TEXT UNIQUE NOT NULL,corporate_id INTEGER NOT NULL,invoice_month TEXT NOT NULL,item_name TEXT NOT NULL DEFAULT '',description TEXT NOT NULL DEFAULT '',amount INTEGER NOT NULL DEFAULT 0,discount_amount INTEGER NOT NULL DEFAULT 0,total_amount INTEGER NOT NULL DEFAULT 0,paid_amount INTEGER NOT NULL DEFAULT 0,balance_amount INTEGER NOT NULL DEFAULT 0,due_date TEXT NOT NULL DEFAULT '',status TEXT NOT NULL DEFAULT 'unpaid',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,paid_at TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS corporate_payments (id INTEGER PRIMARY KEY AUTOINCREMENT,payment_code TEXT UNIQUE NOT NULL,corporate_id INTEGER NOT NULL,invoice_id INTEGER NOT NULL,amount INTEGER NOT NULL DEFAULT 0,paid_at TEXT NOT NULL,method TEXT NOT NULL DEFAULT 'Transfer',received_by TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS collection_officers (id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,phone TEXT NOT NULL DEFAULT '',status TEXT NOT NULL DEFAULT 'active',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS collection_batches (id INTEGER PRIMARY KEY AUTOINCREMENT,batch_code TEXT UNIQUE NOT NULL,officer_id INTEGER NOT NULL,invoice_month TEXT NOT NULL,service_fee INTEGER NOT NULL DEFAULT 0,status TEXT NOT NULL DEFAULT 'ready',assigned_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,reported_at TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_by TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS collection_batch_items (id INTEGER PRIMARY KEY AUTOINCREMENT,batch_id INTEGER NOT NULL,invoice_id INTEGER NOT NULL,customer_id INTEGER NOT NULL,invoice_amount INTEGER NOT NULL DEFAULT 0,balance_amount INTEGER NOT NULL DEFAULT 0,service_fee INTEGER NOT NULL DEFAULT 0,report_status TEXT NOT NULL DEFAULT 'pending',report_note TEXT NOT NULL DEFAULT '',reported_at TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,UNIQUE(batch_id,invoice_id))");
    $pdo->exec("CREATE TABLE IF NOT EXISTS customer_points (id INTEGER PRIMARY KEY AUTOINCREMENT,customer_id INTEGER NOT NULL,payment_id INTEGER,points INTEGER NOT NULL DEFAULT 0,movement_type TEXT NOT NULL DEFAULT 'manual',source_label TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',actor TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS network_nodes (id INTEGER PRIMARY KEY AUTOINCREMENT,node_type TEXT NOT NULL,node_code TEXT UNIQUE NOT NULL,name TEXT NOT NULL,address TEXT NOT NULL DEFAULT '',latitude TEXT NOT NULL DEFAULT '',longitude TEXT NOT NULL DEFAULT '',capacity INTEGER NOT NULL DEFAULT 0,status TEXT NOT NULL DEFAULT 'active',upstream_node_id INTEGER,upstream_port TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS network_cable_routes (id INTEGER PRIMARY KEY AUTOINCREMENT,route_code TEXT UNIQUE NOT NULL,name TEXT NOT NULL,cable_type TEXT NOT NULL DEFAULT 'Fiber Optic',core_count INTEGER NOT NULL DEFAULT 1,length_m REAL NOT NULL DEFAULT 0,status TEXT NOT NULL DEFAULT 'active',start_node_id INTEGER NOT NULL,end_node_id INTEGER NOT NULL,path_note TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT '')");
    $pdo->exec("CREATE TABLE IF NOT EXISTS network_splices (id INTEGER PRIMARY KEY AUTOINCREMENT,joint_closure_id INTEGER NOT NULL,incoming_route_id INTEGER NOT NULL,incoming_core INTEGER NOT NULL,incoming_color TEXT NOT NULL DEFAULT '',outgoing_route_id INTEGER NOT NULL,outgoing_core INTEGER NOT NULL,outgoing_color TEXT NOT NULL DEFAULT '',from_node_id INTEGER,to_node_id INTEGER,usage_status TEXT NOT NULL DEFAULT 'used',signal_note TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT '',UNIQUE(joint_closure_id,incoming_route_id,incoming_core,outgoing_route_id,outgoing_core))");
    $pdo->exec("CREATE TABLE IF NOT EXISTS network_customer_placements (id INTEGER PRIMARY KEY AUTOINCREMENT,customer_id INTEGER UNIQUE NOT NULL,odp_id INTEGER NOT NULL,port_no INTEGER NOT NULL,drop_cable_label TEXT NOT NULL DEFAULT '',status TEXT NOT NULL DEFAULT 'active',installed_at TEXT NOT NULL DEFAULT '',notes TEXT NOT NULL DEFAULT '',created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT '')");
    $defaults=['office_brand'=>'DENTA NET','office_company'=>'PT DENTA SEJAHTERA GROUP','office_address'=>'','office_phone'=>'','receipt_note'=>'Nota resmi pembayaran pelanggan'];
    $set=$pdo->prepare('INSERT OR IGNORE INTO app_settings(setting_key,setting_value) VALUES(?,?)');
    foreach($defaults as $k=>$v){ $set->execute([$k,$v]); }

    v3_exec_safe($pdo,"ALTER TABLE auth_users ADD COLUMN last_login_at TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE auth_users ADD COLUMN last_login_ip TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE auth_users ADD COLUMN last_login_agent TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE auth_users ADD COLUMN login_count INTEGER NOT NULL DEFAULT 0");

    v3_exec_safe($pdo,"ALTER TABLE expenses ADD COLUMN category TEXT NOT NULL DEFAULT 'Operasional'");
    v3_exec_safe($pdo,"ALTER TABLE expenses ADD COLUMN used_by TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE expenses ADD COLUMN vendor TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE expenses ADD COLUMN reference_no TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE expenses ADD COLUMN status TEXT NOT NULL DEFAULT 'posted'");
    v3_exec_safe($pdo,"ALTER TABLE expenses ADD COLUMN updated_at TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_expenses_spent_at ON expenses(spent_at)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_expenses_category ON expenses(category)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_expenses_used_by ON expenses(used_by)");

    v3_exec_safe($pdo,"ALTER TABLE invoices ADD COLUMN original_amount INTEGER NOT NULL DEFAULT 0");
    v3_exec_safe($pdo,"ALTER TABLE invoices ADD COLUMN discount_amount INTEGER NOT NULL DEFAULT 0");
    v3_exec_safe($pdo,"ALTER TABLE invoices ADD COLUMN discount_note TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE invoices ADD COLUMN paid_amount INTEGER NOT NULL DEFAULT 0");
    v3_exec_safe($pdo,"ALTER TABLE invoices ADD COLUMN balance_amount INTEGER NOT NULL DEFAULT 0");
    v3_exec_safe($pdo,"UPDATE invoices SET original_amount=amount WHERE original_amount=0 AND amount>0");
    v3_exec_safe($pdo,"UPDATE invoices SET paid_amount=COALESCE((SELECT SUM(amount) FROM payments WHERE payments.customer_id=invoices.customer_id AND payments.invoice_month=invoices.invoice_month),0)");
    v3_exec_safe($pdo,"UPDATE invoices SET balance_amount=MAX(amount-paid_amount,0)");

    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_payments_customer_month ON payments(customer_id,invoice_month)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_payments_invoice_month ON payments(invoice_month)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_invoices_month_status ON invoices(invoice_month,status)");
    v3_exec_safe($pdo,"ALTER TABLE customers ADD COLUMN points_balance INTEGER NOT NULL DEFAULT 0");
    v3_exec_safe($pdo,"ALTER TABLE customers ADD COLUMN latitude TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE customers ADD COLUMN longitude TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"ALTER TABLE customers ADD COLUMN map_note TEXT NOT NULL DEFAULT ''");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_customers_area ON customers(area_name)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_activity_logs_created ON activity_logs(created_at)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_activity_logs_entity ON activity_logs(entity_type,entity_id)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_activity_logs_actor ON activity_logs(actor_username)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_user_table_preferences_user ON user_table_preferences(username,table_key)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_corporate_customers_status ON corporate_customers(status)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_corporate_invoices_month_status ON corporate_invoices(invoice_month,status)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_corporate_payments_invoice ON corporate_payments(invoice_id)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_collection_officers_status ON collection_officers(status)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_collection_batches_status ON collection_batches(status,invoice_month)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_collection_items_batch ON collection_batch_items(batch_id)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_collection_items_invoice ON collection_batch_items(invoice_id)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_customer_points_customer ON customer_points(customer_id,created_at)");
    v3_exec_safe($pdo,"CREATE UNIQUE INDEX IF NOT EXISTS idx_v3_customer_points_payment ON customer_points(payment_id) WHERE payment_id IS NOT NULL");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_network_nodes_type_status ON network_nodes(node_type,status)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_network_nodes_upstream ON network_nodes(upstream_node_id)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_network_routes_nodes ON network_cable_routes(start_node_id,end_node_id)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_network_splices_jc ON network_splices(joint_closure_id)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_network_splices_routes ON network_splices(incoming_route_id,outgoing_route_id)");
    v3_exec_safe($pdo,"CREATE INDEX IF NOT EXISTS idx_v3_network_customer_odp ON network_customer_placements(odp_id,status)");
    v3_exec_safe($pdo,"CREATE UNIQUE INDEX IF NOT EXISTS idx_v3_network_odp_active_port ON network_customer_placements(odp_id,port_no) WHERE status='active'");
    v3_backfill_payment_points($pdo);
}
function v3_table_cols($pdo,$table){ $rows=$pdo->query('PRAGMA table_info('.$table.')')->fetchAll(); return array_map(fn($r)=>$r['name'],$rows); }
function v3_copy_table($src,$dst,$table){
    $dstCols=v3_table_cols($dst,$table); if(!$dstCols) return 0;
    $srcCols=v3_table_cols($src,$table); if(!$srcCols) return 0;
    $cols=array_values(array_intersect($dstCols,$srcCols)); if(!$cols) return 0;
    $dst->exec('DELETE FROM '.$table);
    $select='SELECT '.implode(',',array_map(fn($c)=>'"'.$c.'"',$cols)).' FROM '.$table;
    $rows=$src->query($select)->fetchAll(PDO::FETCH_ASSOC);
    if(!$rows) return 0;
    $ph='('.implode(',',array_fill(0,count($cols),'?')).')';
    $sql='INSERT INTO '.$table.' ('.implode(',',array_map(fn($c)=>'"'.$c.'"',$cols)).') VALUES '.$ph;
    $ins=$dst->prepare($sql); $n=0;
    foreach($rows as $r){ $vals=[]; foreach($cols as $c)$vals[]=$r[$c]; $ins->execute($vals); $n++; }
    return $n;
}
function v3_sync_from_source($force=false){
    $dst=v3_db();
    $src=v3_source_db();
    $tables=['users','packages','customers','payments','expenses','payment_logs','invoices','bank_accounts','customer_events'];
    $dst->beginTransaction();
    try{
        foreach($tables as $t){ $n=v3_copy_table($src,$dst,$t); $st=$dst->prepare('INSERT INTO v3_sync_log(source_path,table_name,row_count) VALUES(?,?,?)'); $st->execute([v3_source_path(),$t,$n]); }
        $dst->commit();
    }catch(Throwable $e){ $dst->rollBack(); throw $e; }
}


function v3_bank_account_label($r){
    if(!$r) return '';
    $bank=trim((string)($r['bank_name']??''));
    $label=trim((string)($r['bank_label']??($r['label']??'')));
    $number=trim((string)($r['account_number']??''));
    $name=trim((string)($r['account_name']??''));
    $parts=[];
    if($label!=='') $parts[]=$label;
    if($bank!=='') $parts[]=$bank;
    if($number!=='') $parts[]=$number;
    if($name!=='') $parts[]='a.n. '.$name;
    return trim(implode(' · ', array_unique(array_filter($parts))));
}
function v3_payment_method_label($r){
    $method=trim((string)($r['method']??''));
    $bank=v3_bank_account_label($r);
    return $bank!=='' ? trim(($method?:'Transfer').' → '.$bank) : ($method?:'-');
}

function v3_settings(){
    $rows=qall('SELECT setting_key,setting_value FROM app_settings');
    $out=[]; foreach($rows as $r){ $out[$r['setting_key']]=$r['setting_value']; }
    return array_merge(['office_brand'=>'DENTA NET','office_company'=>'PT DENTA SEJAHTERA GROUP','office_address'=>'','office_phone'=>'','receipt_note'=>'Nota resmi pembayaran pelanggan'],$out);
}
function v3_setting($key,$default=''){ $s=v3_settings(); return $s[$key]??$default; }
function v3_save_settings($data){
    $pdo=v3_db();
    $allowed=['office_brand','office_company','office_address','office_phone','receipt_note'];
    $st=$pdo->prepare('INSERT INTO app_settings(setting_key,setting_value) VALUES(?,?) ON CONFLICT(setting_key) DO UPDATE SET setting_value=excluded.setting_value');
    foreach($allowed as $k){ $st->execute([$k,trim((string)($data[$k]??''))]); }
}



function v3_table_column_sets(){
    return [
        'customers'=>[
            'no'=>['No',true],
            'actions'=>['Aksi',true],
            'customer_code'=>['ID pelanggan',true],
            'name'=>['Nama pelanggan',true],
            'last_payment'=>['Pembayaran terakhir',true],
            'points_balance'=>['Point',true],
            'address'=>['Alamat',true],
            'phone'=>['Telepon',true],
            'package_name'=>['Nama langganan',true],
            'speed'=>['Keterangan paket',false],
            'price'=>['Harga',true],
            'unpaid_count'=>['Tagihan belum lunas',false],
            'area_name'=>['Nama lokasi',true],
            'router_name'=>['Nama router',true],
            'connection_type'=>['Jenis koneksi',false],
            'customer_status'=>['Status langganan',true],
            'pppoe_username'=>['Username',true],
            'customer_password'=>['Password',false],
            'onu_name'=>['Secret',true],
            'registered_at'=>['Tanggal registrasi',false],
            'due_day'=>['Jatuh tempo',true],
        ],
    ];
}
function v3_table_default_columns($tableKey){
    $sets=v3_table_column_sets(); $out=[];
    foreach(($sets[$tableKey]??[]) as $key=>$meta){ if(!empty($meta[1])) $out[]=$key; }
    return $out;
}

function v3_table_column_presets($tableKey){
    if($tableKey!=='customers') return [];
    return [
        'default'=>['label'=>'Default','desc'=>'Tampilan lengkap seimbang','cols'=>v3_table_default_columns('customers')],
        'compact'=>['label'=>'Ringkas','desc'=>'Cepat untuk cari pelanggan','cols'=>['no','actions','customer_code','name','points_balance','last_payment','phone','customer_status','due_day']],
        'billing'=>['label'=>'Billing','desc'=>'Fokus pembayaran dan tagihan','cols'=>['no','actions','customer_code','name','points_balance','last_payment','price','unpaid_count','due_day','customer_status','phone']],
        'technical'=>['label'=>'Teknis','desc'=>'Fokus PPPoE/router/secret','cols'=>['no','actions','customer_code','name','area_name','router_name','pppoe_username','onu_name','connection_type','customer_status']],
    ];
}

function v3_user_table_columns($tableKey){
    $sets=v3_table_column_sets(); $allowed=array_keys($sets[$tableKey]??[]); if(!$allowed) return [];
    $username=$_SESSION['v3_user']['username']??'system';
    $row=q1('SELECT columns_json FROM user_table_preferences WHERE username=? AND table_key=?',[$username,$tableKey]);
    $cols=[]; if($row){ $decoded=json_decode($row['columns_json']??'[]',true); if(is_array($decoded)) $cols=$decoded; }
    $cols=array_values(array_intersect($cols,$allowed));
    if(!$cols) $cols=v3_table_default_columns($tableKey);
    foreach(['no','actions'] as $must){ if(in_array($must,$allowed,true) && !in_array($must,$cols,true)) array_unshift($cols,$must); }
    if($tableKey==='customers' && in_array('points_balance',$allowed,true) && !in_array('points_balance',$cols,true)){
        $pos=array_search('name',$cols,true);
        if($pos===false) $pos=array_search('last_payment',$cols,true);
        array_splice($cols,$pos===false?min(4,count($cols)):$pos+1,0,['points_balance']);
    }
    return array_values(array_unique($cols));
}
function v3_save_table_columns($tableKey,$cols){
    $sets=v3_table_column_sets(); $allowed=array_keys($sets[$tableKey]??[]); if(!$allowed) return false;
    $cols=is_array($cols)?$cols:[]; $cols=array_values(array_intersect($cols,$allowed));
    foreach(['no','actions'] as $must){ if(in_array($must,$allowed,true) && !in_array($must,$cols,true)) array_unshift($cols,$must); }
    if(!$cols) $cols=v3_table_default_columns($tableKey);
    $username=$_SESSION['v3_user']['username']??'system';
    $json=json_encode(array_values(array_unique($cols)),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $st=v3_db()->prepare('INSERT INTO user_table_preferences(username,table_key,columns_json) VALUES(?,?,?) ON CONFLICT(username,table_key) DO UPDATE SET columns_json=excluded.columns_json,updated_at=CURRENT_TIMESTAMP');
    $st->execute([$username,$tableKey,$json]);
    return true;
}
function v3_reset_table_columns($tableKey){
    $username=$_SESSION['v3_user']['username']??'system';
    v3_db()->prepare('DELETE FROM user_table_preferences WHERE username=? AND table_key=?')->execute([$username,$tableKey]);
}

function v3_log_safe_array($data){
    if(!is_array($data)) return [];
    $deny=['password','password_hash','pass','csrf'];
    $out=[];
    foreach($data as $k=>$v){
        $lk=strtolower((string)$k);
        if(in_array($lk,$deny,true) || str_contains($lk,'password')){ $out[$k]='***'; continue; }
        if(is_array($v)) $out[$k]=v3_log_safe_array($v);
        elseif(is_object($v)) $out[$k]='[object]';
        else $out[$k]=is_string($v) && strlen($v)>500 ? substr($v,0,500).'…' : $v;
    }
    return $out;
}
function v3_log_json($data){
    if($data===null || $data==='') return '';
    return json_encode(v3_log_safe_array((array)$data), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
}
function v3_actor(){
    $u=$_SESSION['v3_user']??[];
    return [
        'id'=>(int)($u['id']??0),
        'name'=>(string)($u['name']??'Sistem'),
        'username'=>(string)($u['username']??'system'),
        'level'=>(string)($u['level']??''),
    ];
}
function v3_log_activity($action,$entityType,$entityId=null,$entityLabel='',$summary='',$before=null,$after=null){
    try{
        $pdo=v3_db(); $a=v3_actor();
        $st=$pdo->prepare('INSERT INTO activity_logs(actor_id,actor_name,actor_username,actor_level,action,entity_type,entity_id,entity_label,summary,before_json,after_json,ip_address,user_agent) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $st->execute([$a['id'],$a['name'],$a['username'],$a['level'],(string)$action,(string)$entityType,$entityId!==null?(int)$entityId:null,(string)$entityLabel,(string)$summary,v3_log_json($before),v3_log_json($after),$_SERVER['REMOTE_ADDR']??'',substr($_SERVER['HTTP_USER_AGENT']??'',0,240)]);
    }catch(Throwable $e){ /* audit log must never break operator workflow */ }
}
function v3_changed_summary($before,$after,$labels=[]){
    $before=is_array($before)?$before:[]; $after=is_array($after)?$after:[]; $keys=array_unique(array_merge(array_keys($before),array_keys($after))); $parts=[];
    foreach($keys as $k){
        if(in_array(strtolower((string)$k),['password','password_hash','csrf'],true) || str_contains(strtolower((string)$k),'password')) continue;
        $b=(string)($before[$k]??''); $a=(string)($after[$k]??'');
        if($b!==$a){ $label=$labels[$k]??$k; $parts[]=$label.': "'.($b===''?'-':$b).'" → "'.($a===''?'-':$a).'"'; }
        if(count($parts)>=8) break;
    }
    return $parts ? implode('; ', $parts) : 'Tidak ada perubahan field utama.';
}

function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function rp($n){return 'Rp. '.number_format((int)$n,0,',','.');}
function current_month(){return date('Y-m');}
function month_long($m){ if(!preg_match('/^\d{4}-\d{2}$/',(string)$m)) $m=current_month(); [$y,$mo]=explode('-', $m); $names=['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember']; return ($names[$mo]??$mo).' '.$y; }

function v3_parse_latlng($lat,$lng,$combined=''){
    $lat=trim((string)$lat); $lng=trim((string)$lng); $combined=trim((string)$combined);
    if(($lat==='' || $lng==='') && $combined!==''){
        if(preg_match('/(-?\d+(?:[.,]\d+)?)\s*[,;\s]\s*(-?\d+(?:[.,]\d+)?)/',$combined,$m)){
            $lat=str_replace(',','.',$m[1]); $lng=str_replace(',','.',$m[2]);
        }
    }
    $lat=str_replace(',','.',$lat); $lng=str_replace(',','.',$lng);
    if($lat!=='' && $lng!=='' && is_numeric($lat) && is_numeric($lng)){
        $laf=(float)$lat; $lnf=(float)$lng;
        if($laf>=-90 && $laf<=90 && $lnf>=-180 && $lnf<=180) return [rtrim(rtrim(sprintf('%.7F',$laf),'0'),'.'), rtrim(rtrim(sprintf('%.7F',$lnf),'0'),'.')];
    }
    return ['',''];
}
function v3_customer_has_latlng($r){ return isset($r['latitude'],$r['longitude']) && $r['latitude']!=='' && $r['longitude']!=='' && is_numeric($r['latitude']) && is_numeric($r['longitude']); }
function v3_google_maps_url($lat,$lng){ $q=trim((string)$lat).','.trim((string)$lng); return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($q); }
function v3_normalize_datetime($value,$fallback=null){
    $value=trim((string)$value);
    if($value==='') return $fallback ?: date('Y-m-d H:i:s');
    $value=str_replace('T',' ',$value);
    if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$value)) $value.=' '.date('H:i:s');
    elseif(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/',$value)) $value.=':00';
    $ts=strtotime($value);
    if($ts===false) return $fallback ?: date('Y-m-d H:i:s');
    return date('Y-m-d H:i:s',$ts);
}
function v3_datetime_local_value($value){
    $value=trim((string)$value); if($value==='') return date('Y-m-d\TH:i');
    $ts=strtotime(str_replace('T',' ',$value));
    return $ts===false ? e($value) : date('Y-m-d\TH:i',$ts);
}
function v3_payment_date_label($value){
    $value=trim((string)$value); if($value==='') return '-';
    $ts=strtotime(str_replace('T',' ',$value));
    return $ts===false ? e($value) : date('Y-m-d H:i:s',$ts);
}

function v3_points_for_paid_at($paidAt){
    $ts=strtotime(str_replace('T',' ',(string)$paidAt));
    if($ts===false) $ts=time();
    $day=(int)date('j',$ts);
    if($day>=1 && $day<=5) return 10;
    if($day>=6 && $day<=10) return 5;
    if($day>=11 && $day<=20) return 3;
    return 1;
}
function v3_point_rule_label($paidAt){
    $points=v3_points_for_paid_at($paidAt);
    $day=(int)date('j',strtotime(str_replace('T',' ',(string)$paidAt)) ?: time());
    if($day<=5) return 'Bayar tanggal 1-5';
    if($day<=10) return 'Bayar tanggal 6-10';
    if($day<=20) return 'Bayar tanggal 11-20';
    return 'Bayar tanggal 21-akhir bulan';
}
function v3_recalc_customer_points($customerId){
    $customerId=(int)$customerId; if($customerId<=0) return 0;
    $total=(int)scalar('SELECT COALESCE(SUM(points),0) FROM customer_points WHERE customer_id=?',[$customerId]);
    v3_db()->prepare('UPDATE customers SET points_balance=? WHERE id=?')->execute([$total,$customerId]);
    return $total;
}
function v3_sync_payment_points($paymentId,$actor='Sistem'){
    $paymentId=(int)$paymentId; if($paymentId<=0) return null;
    $pdo=v3_db();
    $pay=q1('SELECT pay.*,c.customer_code,c.name customer_name FROM payments pay LEFT JOIN customers c ON c.id=pay.customer_id WHERE pay.id=?',[$paymentId]);
    if(!$pay) return null;
    $points=v3_points_for_paid_at($pay['paid_at']??'');
    $label=v3_point_rule_label($pay['paid_at']??'');
    $notes=$label.' · periode '.($pay['invoice_month']??'-').' · '.rp($pay['amount']??0).' · '.($pay['payment_code']??('PAY#'.$paymentId));
    $before=q1('SELECT * FROM customer_points WHERE payment_id=?',[$paymentId]);
    if($before){
        $st=$pdo->prepare('UPDATE customer_points SET customer_id=?,points=?,movement_type=?,source_label=?,notes=?,actor=?,updated_at=CURRENT_TIMESTAMP WHERE id=?');
        $st->execute([(int)$pay['customer_id'],$points,'auto_payment',$label,$notes,(string)$actor,(int)$before['id']]);
    } else {
        $st=$pdo->prepare('INSERT INTO customer_points(customer_id,payment_id,points,movement_type,source_label,notes,actor,updated_at) VALUES(?,?,?,?,?,?,?,CURRENT_TIMESTAMP)');
        $st->execute([(int)$pay['customer_id'],$paymentId,$points,'auto_payment',$label,$notes,(string)$actor]);
    }
    if($before && (int)$before['customer_id']!==(int)$pay['customer_id']) v3_recalc_customer_points((int)$before['customer_id']);
    v3_recalc_customer_points((int)$pay['customer_id']);
    return ['customer_id'=>(int)$pay['customer_id'],'points'=>$points,'label'=>$label,'payment_code'=>$pay['payment_code']??''];
}
function v3_remove_payment_points($paymentId,$customerId=0){
    $paymentId=(int)$paymentId; if($paymentId<=0) return;
    $old=q1('SELECT * FROM customer_points WHERE payment_id=?',[$paymentId]);
    v3_db()->prepare('DELETE FROM customer_points WHERE payment_id=?')->execute([$paymentId]);
    if($old) v3_recalc_customer_points((int)$old['customer_id']);
    elseif((int)$customerId>0) v3_recalc_customer_points((int)$customerId);
}
function v3_add_manual_customer_points($customerId,$points,$notes='',$actor='Sistem'){
    $customerId=(int)$customerId; $points=(int)$points;
    if($customerId<=0) throw new Exception('Pelanggan tidak valid.');
    if($points===0) throw new Exception('Jumlah point tidak boleh 0.');
    if(!q1('SELECT id FROM customers WHERE id=?',[$customerId])) throw new Exception('Pelanggan tidak ditemukan.');
    v3_db()->prepare('INSERT INTO customer_points(customer_id,payment_id,points,movement_type,source_label,notes,actor,updated_at) VALUES(?,?,?,?,?,?,?,CURRENT_TIMESTAMP)')->execute([$customerId,null,$points,'manual_adjustment','Custom admin',trim((string)$notes),(string)$actor]);
    return v3_recalc_customer_points($customerId);
}
function v3_backfill_payment_points($pdo=null){
    $pdo=$pdo?:v3_db();
    try{
        $missing=(int)$pdo->query('SELECT COUNT(*) FROM payments pay LEFT JOIN customer_points cp ON cp.payment_id=pay.id WHERE cp.id IS NULL')->fetchColumn();
        if($missing<=0){
            foreach($pdo->query('SELECT id FROM customers')->fetchAll(PDO::FETCH_ASSOC) as $r) v3_recalc_customer_points((int)$r['id']);
            return;
        }
        $rows=$pdo->query('SELECT id FROM payments ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as $r) v3_sync_payment_points((int)$r['id'],'Sistem backfill');
    }catch(Throwable $e){ /* point backfill must not break login/app boot */ }
}

function redirect($to){header('Location: '.$to); exit;}
function csrf_token(){ if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_check(){ if($_SERVER['REQUEST_METHOD']==='POST'){ if(($_POST['csrf']??'')!==($_SESSION['csrf']??'')) die('CSRF token tidak valid.'); }}
function src(){return v3_db();}
function q1($sql,$p=[]){$st=src()->prepare($sql);$st->execute($p);return $st->fetch();}
function qall($sql,$p=[]){$st=src()->prepare($sql);$st->execute($p);return $st->fetchAll();}
function scalar($sql,$p=[]){$st=src()->prepare($sql);$st->execute($p);return $st->fetchColumn();}
function paginate($total,$page,$per){$pages=max(1,(int)ceil($total/$per));$page=max(1,min($page,$pages));return [$page,$pages,($page-1)*$per];}

function v3_recalc_invoice($customerId,$month){
    $pdo=v3_db();
    $inv=q1('SELECT * FROM invoices WHERE customer_id=? AND invoice_month=?',[$customerId,$month]);
    if(!$inv) return;
    $paid=(int)scalar('SELECT COALESCE(SUM(amount),0) FROM payments WHERE customer_id=? AND invoice_month=?',[$customerId,$month]);
    $amount=(int)$inv['amount'];
    $balance=max(0,$amount-$paid);
    $status=$paid<=0?'unpaid':($balance<=0?'paid':'partial');
    $paidAt=$status==='paid' ? (string)scalar('SELECT MAX(paid_at) FROM payments WHERE customer_id=? AND invoice_month=?',[$customerId,$month]) : '';
    $paymentId=$status==='paid' ? (int)scalar('SELECT id FROM payments WHERE customer_id=? AND invoice_month=? ORDER BY paid_at DESC,id DESC LIMIT 1',[$customerId,$month]) : null;
    $st=$pdo->prepare('UPDATE invoices SET paid_amount=?,balance_amount=?,status=?,paid_at=?,payment_id=? WHERE customer_id=? AND invoice_month=?');
    $st->execute([$paid,$balance,$status,$paidAt,$paymentId,$customerId,$month]);
}
function v3_generate_monthly_invoices($month=null,$force=false){
    $pdo=v3_db(); $month=$month?:current_month(); $created=0; $updated=0; $skipped=0;
    $rows=qall("SELECT c.id,c.due_day,COALESCE(p.price,0) price FROM customers c LEFT JOIN packages p ON p.id=c.package_id WHERE c.customer_status='active' AND c.is_active=1 AND COALESCE(p.price,0)>0");
    $pdo->beginTransaction();
    try{
        foreach($rows as $r){
            $cid=(int)$r['id']; $amount=(int)$r['price']; $due=(int)($r['due_day']?:20); $code=v3_invoice_code($cid,$month);
            $exists=q1('SELECT * FROM invoices WHERE customer_id=? AND invoice_month=?',[$cid,$month]);
            if(!$exists){
                $st=$pdo->prepare('INSERT INTO invoices(invoice_code,customer_id,invoice_month,amount,original_amount,due_day,status,notes,balance_amount) VALUES(?,?,?,?,?,?,?,?,?)');
                $st->execute([$code,$cid,$month,$amount,$amount,$due,'unpaid','Auto invoice bulanan V3',$amount]); $created++;
            } elseif($force && ($exists['status']??'unpaid')==='unpaid' && (int)($exists['paid_amount']??0)===0) {
                $st=$pdo->prepare('UPDATE invoices SET amount=?,original_amount=?,due_day=?,balance_amount=?,notes=CASE WHEN notes="" THEN ? ELSE notes END WHERE id=?');
                $st->execute([$amount,$amount,$due,$amount,'Auto invoice bulanan V3',(int)$exists['id']]); $updated++;
            } else { $skipped++; }
        }
        $pdo->commit();
    }catch(Throwable $e){ $pdo->rollBack(); throw $e; }
    foreach($rows as $r){ v3_recalc_invoice((int)$r['id'],$month); }
    return ['created'=>$created,'updated'=>$updated,'skipped'=>$skipped,'month'=>$month,'customers'=>count($rows)];
}

function v3_payment_code(){return 'PAY-'.date('Ymd-His').'-'.random_int(100,999);}
function v3_expense_code(){return 'OPS-'.date('Ymd-His').'-'.random_int(100,999);}
function v3_invoice_code($customerId,$month){return 'INV-'.str_pad((string)$customerId,5,'0',STR_PAD_LEFT).'-'.str_replace('-','',$month);}
function v3_corporate_code(){return 'CORP-'.date('ymd').'-'.random_int(100,999);}
function v3_corporate_invoice_code($corporateId,$month){return 'CINV-'.str_pad((string)$corporateId,4,'0',STR_PAD_LEFT).'-'.str_replace('-','',$month).'-'.random_int(10,99);}
function v3_corporate_payment_code(){return 'CPAY-'.date('Ymd-His').'-'.random_int(100,999);}
function v3_recalc_corporate_invoice($invoiceId){
    $pdo=v3_db(); $inv=q1('SELECT * FROM corporate_invoices WHERE id=?',[$invoiceId]); if(!$inv) return;
    $paid=(int)scalar('SELECT COALESCE(SUM(amount),0) FROM corporate_payments WHERE invoice_id=?',[$invoiceId]);
    $total=(int)$inv['total_amount']; $balance=max(0,$total-$paid); $status=$paid<=0?'unpaid':($balance<=0?'paid':'partial');
    $paidAt=$status==='paid' ? (string)scalar('SELECT MAX(paid_at) FROM corporate_payments WHERE invoice_id=?',[$invoiceId]) : '';
    $pdo->prepare('UPDATE corporate_invoices SET paid_amount=?,balance_amount=?,status=?,paid_at=? WHERE id=?')->execute([$paid,$balance,$status,$paidAt,$invoiceId]);
}
?>
