<?php require (is_file(__DIR__.'/../app/bootstrap.php') ? __DIR__.'/../app/bootstrap.php' : '/var/www/appsbilling-commercial-platform/app/bootstrap.php'); render_header('Platform Billing ISP untuk Mitra'); ?>
<nav class="nav">
  <a class="brand" href="<?=app_url('index.php')?>"><span class="brand-mark">AB</span><span><strong>AppsBilling</strong><small>Platform Mitra ISP</small></span></a>
  <div class="nav-actions">
    <a class="btn btn-light" href="<?=app_url('tenant/login.php')?>">Login Mitra</a>
    <a class="btn btn-light" href="<?=app_url('superadmin/login.php')?>">Superadmin</a>
    <a class="btn btn-primary" href="<?=app_url('register.php')?>">Daftar Mitra</a>
  </div>
</nav>

<section class="hero hero-commercial">
  <div class="hero-copy">
    <span class="eyebrow">Untuk ISP, RT/RW Net, dan operator jaringan</span>
    <h1>Billing internet yang siap dipakai banyak mitra.</h1>
    <p class="lead">AppsBilling membantu pengelola jaringan mengurus pelanggan, paket, tagihan, pembayaran, dan kwitansi dalam ruang kerja yang rapi. Setiap mitra punya No Akun dan database sendiri, jadi data tidak bercampur.</p>
    <div class="nav-actions hero-actions">
      <a class="btn btn-primary btn-lg" href="<?=app_url('register.php')?>">Mulai daftar mitra</a>
      <a class="btn btn-light btn-lg" href="#cara-daftar">Lihat cara daftar</a>
    </div>
    <div class="trust-row">
      <span>DB terpisah</span>
      <span>Approval superadmin</span>
      <span>Siap berkembang ke V3 penuh</span>
    </div>
  </div>

  <div class="product-panel">
    <div class="panel-top">
      <div><small>Contoh mitra</small><b>MRT NET</b></div>
      <span class="badge active">active</span>
    </div>
    <div class="account-ticket">
      <small>No Akun Mitra</small>
      <strong>4 digit acak</strong>
      <p>Dibuat otomatis saat registrasi. Mitra login memakai No Akun, username, dan password.</p>
    </div>
    <div class="operator-card">
      <div><span></span><span></span><span></span></div>
      <h3>Ringkas untuk operator</h3>
      <p>Mulai dari pendaftaran sampai dashboard dibuat jelas, tanpa istilah teknis yang bikin calon mitra bingung.</p>
    </div>
  </div>
</section>

<section id="cara-daftar" class="section split-section">
  <div>
    <span class="eyebrow">Tutorial pendaftaran</span>
    <h2>Alurnya sederhana dan mudah dijelaskan.</h2>
    <p class="section-lead">Mitra cukup mengisi data usaha dan akun admin. Setelah disetujui, sistem membuat ruang billing sendiri lengkap dengan No Akun 4 digit.</p>
  </div>
  <div class="steps-list">
    <article class="step-card"><b>1</b><div><h3>Isi form pendaftaran</h3><p>Masukkan nama usaha, PIC, WhatsApp, area layanan, username, dan password admin. Contoh nama usaha: <strong>MRT NET</strong>.</p></div></article>
    <article class="step-card"><b>2</b><div><h3>Simpan No Akun</h3><p>Setelah form terkirim, sistem membuat No Akun 4 digit secara acak. Nomor ini menjadi identitas login mitra.</p></div></article>
    <article class="step-card"><b>3</b><div><h3>Tunggu approval</h3><p>Superadmin mengecek data lalu menyetujui akun. Saat approve, database billing mitra dibuat otomatis.</p></div></article>
    <article class="step-card"><b>4</b><div><h3>Masuk dashboard</h3><p>Mitra login memakai No Akun, username, dan password yang dibuat saat pendaftaran.</p></div></article>
  </div>
</section>

<section class="section feature-section">
  <div class="grid">
    <article class="card feature-card"><span>01</span><h3>Data aman per mitra</h3><p>Setiap mitra mendapat database sendiri agar pelanggan, tagihan, dan pembayaran tidak bercampur.</p></article>
    <article class="card feature-card"><span>02</span><h3>Kontrol dari pusat</h3><p>Superadmin bisa approve, disable, reaktifkan, dan soft-delete akun tanpa menghapus data sembarangan.</p></article>
    <article class="card feature-card"><span>03</span><h3>Siap tumbuh</h3><p>Fondasinya disiapkan untuk pelanggan, paket, invoice, pembayaran, kwitansi, dan laporan pendapatan.</p></article>
  </div>
</section>

<section class="section final-cta">
  <div class="card cta-card">
    <div><span class="eyebrow">Siap dicoba?</span><h2>Daftarkan mitra pertama dan jalankan flow-nya.</h2><p>Alur dasar sudah siap dari registrasi, approval, sampai login tenant.</p></div>
    <a class="btn btn-primary btn-lg" href="<?=app_url('register.php')?>">Daftar sekarang</a>
  </div>
</section>
<?php render_footer(); ?>
