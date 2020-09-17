<?php 
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';
global $woocommerce;

function get_pict($ch){
    $url = get_site_url();

    switch ($ch) {
        case '800':
             echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/bri.png";
            break;
        case '801':
            echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/bni.png";
            break;
        case '802':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/mandiricp.png";
            break;
        case '707':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/alfamart.png";
            break;
		case '708':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/danamononline.png";
            break;
		case '701':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/danamononline.png";
            break;
        case '706':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/indomaret.png";
            break;
        case '703':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/mandiricp.png";
            break;
        case '702':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/bca.png";
            break;
        case '405':
            echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/bca-klikpay.png";
            break;
        case '408':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/maybank.png";
            break;
        case '407':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/maybank.png";
            break;
        case '402':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/va_permata.png";
            break;
        case '400':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/mocash.png";
            break;
        case '303':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/xl_tunai.png";
            break;
        case '711':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/shoppepayQRIS.png";
            break;
        case '713':
           echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/shoppepayApp.png";
            break;
        default:
            echo $url."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/faspay-logo.jpg";
            break;
    }
}

function get_guide($ch,$trxid,$merchant,$mid,$currency,$total){
    switch ($ch) {
        case '800':
        ?>
        <button class="accordion">Tata Cara Membayar Melalui ATM BRI<i class="fa fa-arrow-down" style="float: right;"></i></button>
        <div class="panel" style="display: block;">
            <ol>
                <li>Nasabah melakukan pembayaran melalui ATM Bank BRI</li>
                <li>Pilih Menu Transaksi Lain</li>
                <li>Pilih Menu Pembayaran</li>
                <li>Pilih Menu Lainnya</li>
                <li>Pilih Menu BRIVA</li>
                <li>Masukan 16 digit Nomor Virtual Account :<?php echo $trxid ?>.</li>
                <li>Proses Pembayaran (Ya/Tidak)</li>
                <li>Harap Simpan Struk Transaksi yang anda dapatkan</li>
            </ol>
        </div>
        <button class="accordion" >Tata Cara Membayar Melalui Mobile Banking BRI <i class="fa fa-arrow-down" style="float: right;"></i></button>
        <div class="panel">
            <li>Nasabah melakukan pembayaran melalui Mobile/SMS Banking BRI</li>
            <li>Nasabah memilih Menu Pembayaran melalui Menu Mobile/SMS Banking BRI</li>
            <li>Nasabah memilih Menu BRIVA</li>
            <li>Masukan 16 digit Nomor Virtual Account : <?php echo $trxid ?></li>
            <li>Masukan Jumlah Pembayaran sesuai Tagihan</li>
            <li>Masukan PIN Mobile/SMS Banking BRI</li>
            <li>Nasabah mendapat Notifikasi Pembayaran</li>
        </div>
         <button class="accordion" >Tata Cara Membayar Melalui Internet Banking BRI <i class="fa fa-arrow-down" style="float: right;"></i></button>
         <div class="panel">
            <li>Nasabah melakukan pembayaran melalui Internet Banking BRI</li>
            <li>Nasabah memilih Menu Pembayaran</li>
            <li>Nasabah memilih Menu BRIVA</li>
            <li>Masukan Kode Bayar dengan 16 digit Nomor Virtual Account : <?php echo $trxid ?>.</li>
            <li>Masukan Password Internet Banking BRI</li>
            <li>Masukan mToken Internet Banking BRI</li>
            <li>Nasabah mendapat Notifikasi Pembayaran</li>
         </div>
         <button class="accordion" >Tata Cara Membayar Melalui ATM Bank Lain <i class="fa fa-arrow-down" style="float: right;"></i></button>
         <div class="panel">
            <li>Nasabah melakukan pembayaran melalui ATM Bank Lain yang dimiliki Nasabah melalui Menu Transfer Antar Bank</li>
            <li>Masukan Kode Bank Tujuan : BRI (Kode Bank : 002) + Nomor Virtual Account : <?php echo $trxid ?>.</li>
            <li>Masukan Jumlah Pembayaran sesuai Tagihan</li>
            <li>Proses Pembayaran (Ya/Tidak)</li>
            <li>Masukan Password Internet Banking BRI</li>
            <li>Masukan mToken Internet Banking BRI</li>
            <li>Harap Simpan Struk Transaksi yang anda dapatkan</li>
         </div>
        <?php
        break;
        case '303':
        ?>
        <button class="accordion">Tata Cara Membayar <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel" style="display: block;">
        <ol>
        <li>Ketik *123*120# dari handphone Anda, lalu tekan OK/YES</li>
        <li>Setelah muncul menu transaksi XL Tunai, ketik 4 (pilihan Belanja Online)</li>
        <li>Ketik 1 (Lanjut) lalu Kirim</li>
        <li>Masukkan kode merchant <?php echo $merchant ?> <b><u><?php echo $mid ?></u></b> lalu Kirim</li>
        <li>Masukkan Nomor Pesanan sesuai dengan yang tertera pada laman TERIMA KASIH, lalu Kirim.</li>
        <li>Jika pembayaran berhasil, Anda akan menerima notifikasi pembayaran berhasil melalui SMS.</b></li>
        </ol>
    </div>
        <?php
            break;
    	case '802':
    		?> 
    		<button class="accordion">Tata Cara Membayar Melalui ATM</button>
    <div class="panel" style="display: block;">
        <ol>
      	<li>Catat kode pembayaran yang anda dapat</li>
        <li>Gunakan ATM Mandiri untuk menyelesaikan pembayaran</li>
        <li>Masukkan PIN anda</li>
        <li>Pilih 'Bayar/Beli'</li>
        <li>Cari pilihan MULTI PAYMENT</li>
        <li>Masukkan Kode Perusahaan <b>88308</b></li>
        <li>Masukkan Kode Pelanggan <b><?php echo $trxid ?></b></li>
        <li>Masukkan Jumlah Pembayaran sesuai dengan Jumlah Tagihan anda kemudian tekan 'Benar'</li>
        <li>Pilih Tagihan Anda jika sudah sesuai tekan YA</li>
        <li>Konfirmasikan tagihan anda apakah sudah sesuai lalu tekan YA</li>
        <li>Harap Simpan Struk Transaksi yang anda dapatkan</li>
        </ol>
    </div>
    <button class="accordion" >Tata Cara Membayar Melalui Internet Banking Mandiri <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
            <li>Pada Halaman Utama pilih menu BAYAR</li>
            <li>Pilih submenu MULTI PAYMENT</li>
            <li>Cari Penyedia Jasa 'FASPAY'</li>
            <li>Masukkan Kode Pelanggan <b><?php echo $trxid ?></b></li>
            <li>Masukkan Jumlah Pembayaran sesuai dengan Jumlah Tagihan anda</li>
            <li>Pilih LANJUTKAN</li>
            <li>Pilih Tagihan Anda jika sudah sesuai tekan LANJUTKAN</li>
            <li>Transaksi selesai, jika perlu CETAK hasil transaksi anda</li>
    </div>
    <button class="accordion" >Pembayaran melalui ATM Prima <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Masukkan PIN</li>
            <li>Pilih menu TRANSAKSI LAINNYA</li>
            <li>Pilih menu KE REK BANK LAIN</li>
            <li>Masukkan kode sandi Bank Mandiri (008) kemudian tekan BENAR</li>
            <li>Masukkan nomor VIRTUAL ACCOUNT yang tertera pada halaman konfirmasi, dan tekan BENAR</li>
            <li>Masukkan jumlah pembayaran sesuai dengan yang ditagihkan dalam halaman konfirmasi</li>
            <li>Pilih BENAR untuk menyetujui transaksi tersebut</li>
        </ol>
    </div>
    <button class="accordion" >Pembayaran melalui ATM Bersama <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Masukkan PIN</li>
            <li>Pilih menu TRANSAKSI</li>
            <li>Pilih menu KE REK BANK LAIN</li>
            <li>Masukkan kode sandi Bank Mandiri (008) diikuti dengan nomor VIRTUAL ACCOUNT yang tertera pada halaman konfirmasi, dan tekan BENAR</li>
            <li>Masukkan jumlah pembayaran sesuai dengan yang ditagihkan dalam halaman konfirmasi</li>
            <li>Pilih BENAR untuk menyetujui transaksi tersebut</li>
        </ol>
    </div>
    <button class="accordion" >Pembayaran Mandiri Virtual Account dengan Mandiri Online <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Login Mandiri Online dengan memasukkan username dan password</li>
            <li>Pilih menu PEMBAYARAN</li>
            <li>Pilih menu MULTI PAYMENT </li>
            <li>Cari Penyedia Jasa 'FASPAY'</li>
            <li>Masukkan Nomor Virtual Account <b><?php echo $trxid ?></b> dan nominal yang akan dibayarkan, lalu pilih Lanjut</li>
            <li>Setelah muncul tagihan, pilih Konfirmasi</li>
            <li>Masukkan PIN/ challange code token</li>
            <li>Transaksi selesai, simpan bukti bayar anda</li>
        </ol>
    </div>
    		<?php
    		break;
    	case '402':
    		?>
    		<button class="accordion" >Pembayaran Melalui ATM Permata <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel" style="display: block;">
        <ol>
            <li>Masukkan PIN</li>
            <li>Pilih menu TRANSAKSI LAINNYA</li>
            <li>Pilih menu PEMBAYARAN</li>
            <li>Pilih menu PEMBAYARAN LAINNYA</li>
            <li>Pilih menu VIRTUAL ACCOUNT</li>
            <li>Masukkan nomor VIRTUAL ACCOUNT yang tertera pada halaman konfirmasi, dan tekan BENAR</li>
            <li>Pilih rekening yang menjadi sumber dana yang akan didebet, lalu tekan YA untuk konfirmasi transaksi</li>
        </ol>
    </div>

    <button class="accordion" >Pembayaran melalui ATM Prima <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Masukkan PIN</li>
            <li>Pilih menu TRANSAKSI LAINNYA</li>
            <li>Pilih menu KE REK BANK LAIN</li>
            <li>Masukkan kode sandi Bank Permata (013) kemudian tekan BENAR</li>
            <li>Masukkan nomor VIRTUAL ACCOUNT yang tertera pada halaman konfirmasi, dan tekan BENAR</li>
            <li>Masukkan jumlah pembayaran sesuai dengan yang ditagihkan dalam halaman konfirmasi</li>
            <li>Pilih BENAR untuk menyetujui transaksi tersebut</li>
        </ol>
    </div>

    <button class="accordion" >Pembayaran melalui ATM Bersama <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Masukkan PIN</li>
            <li>Pilih menu TRANSAKSI</li>
            <li>Pilih menu KE REK BANK LAIN</li>
            <li>Masukkan kode sandi Bank Permata (013) diikuti dengan nomor VIRTUAL ACCOUNT yang tertera pada halaman konfirmasi, dan tekan BENAR</li>
            <li>Masukkan jumlah pembayaran sesuai dengan yang ditagihkan dalam halaman konfirmasi</li>
            <li>Pilih BENAR untuk menyetujui transaksi tersebut</li>
        </ol>
    </div>

    <button class="accordion" >Pembayaran Melalui Permata Mobile <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Buka aplikasi PermataMobile Internet (Android/iPhone)</li>
            <li>Masukkan User ID & Password</li>
            <li>Pilih Pembayaran Tagihan</li>
            <li>Pilih Virtual Account</li>
            <li>Masukkan 16 digit nomor Virtual Account yang tertera pada halaman konfirmasi</li>
            <li>Masukkan nominal pembayaran sesuai dengan yang ditagihkan</li>
            <li>Muncul Konfirmasi pembayarann</li>
            <li>Masukkan otentikasi transaksi/token</li>
            <li>Transaksi selesai</li>
        </ol>
    </div>

    <button class="accordion" >Pembayaran Melalui Permata Net <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Buka website PermataNet: <a href="https://new.permatanet.com">https://new.permatanet.com</a></li>
            <li>Masukkan User ID & Password</li>
            <li>Pilih Pembayaran Tagihan</li>
            <li>Pilih Virtual Account</li>
            <li>Masukkan 16 digit nomor Virtual Account yang tertera pada halaman konfirmasi</li>
            <li>Masukkan nominal pembayaran sesuai dengan yang ditagihkan</li>
            <li>Muncul Konfirmasi pembayarann</li>
            <li>Masukkan otentikasi transaksi/token</li>
            <li>Transaksi selesai</li>
        </ol>
    </div>
    		<?php
    		case '801':
    			?>
    			<button class="accordion" >Tata Cara Membayar Melalui ATM BNI <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel" style="display: block;">
        <ol>
            <li>Nasabah melakukan pembayaran melalui ATM Bank BNI</li>
            <li>Pilih Menu Lainnya</li>
            <li>Pilih Menu Transfer</li>
            <li>Pilih Menu Rekening Tabungan</li>
            <li>Pilih Menu Ke Rekening BNI</li>
            <li>Masukan 16 digit Nomor Virtual Account <b><?php echo $trxid ?></b></li>
            <li>Masukan Nominal Transfer</li>
            <li>Konfirmasi Pemindahbukuan</li>
            <li>Transaksi Selesai. Harap Simpan Struk Transaksi yang anda dapatkan</li>
        </ol>
    </div>

    <button class="accordion" >Tata Cara Membayar Melalui SMS Banking BNI <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Nasabah melakukan pembayaran melalui SMS Banking BNI</li>
            <li>Pilih Menu Transfer</li>
            <li>Masukan 16 digit Nomor Virtual Account <b><?php echo $trxid ?></b></li>
            <li>Masukan Jumlah Pembayaran. Kemudian Proses</li>
            <li>Akan Muncul Popup dan kemudian Pilih Yes lalu Send</li>
            <li>Anda akan mendapatkan SMS konfirmasi dari BNI</li>
            <li>Reply SMS dengan ketik pin digit ke 2 & 3</li>
            <li>Transaksi Berhasil</li>
        </ol>
        <br>
            Atau bisa juga langsung mengetik sms dan kirim ke 3346 dengan format
        <br>
        <ol>
            <li><b>TRF[SPASI]NOMOR VA BNI[SPASI]NOMINAL</b></li>
            <li>Anda akan mendapatkan SMS konfirmasi dari BNI</li>
            <li>Reply SMS dengan ketik pin digit ke 2 & 3</li>
            <li>Transaksi Berhasil</li>
        </ol>
    </div>

    <button class="accordion" >Internet Banking BNI <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Nasabah melakukan pembayaran melalui Internet Banking BNI</li>
            <li>Ketik alamat <a href="https://ibank.bni.co.id">https://ibank.bni.co.id</a></li>
            <li>Masukkan User ID dan Password</li>
            <li>Klik menu <b>TRANSFER</b> kemudian pilih <b>TAMBAH REKENING FAVORIT</b>. Jika menggunakan Desktop/PC untuk menambah rekening pada menu <b>Transaksi</b> kemudian <b>Atur Rekening Tujuan</b> lalu <b>Tambah Rekening Tujuan</b></li>
            <li>Masukan Nama dan Kode Bayar dengan 16 digit Nomor Virtual Account <b><?php echo $trxid ?></b></li>
            <li>Masukan Kode Otentikasi Token</li>
            <li>Nomor Rekening Tujuan Berhasil Ditambahkan</li>
            <li>Kembali ke menu TRANSFER. Pilih TRANSFER ANTAR REKENING BNI, kemudian pilih rekening tujuan</li>
            <li>Pilih Rekening Debit dan ketik nominal, lalu masukkan kode otentikasi token</li>
            <li>Transfer Anda Telah Berhasil</li>
        </ol>
    </div>

    <button class="accordion" >Mobile Banking BNI <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Akses BNI Mobile Banking dari handphone kemudian masukkan User ID dan Password</li>
            <li>Pilih menu Transfer</li>
            <li>Pilih Antar Rekening BNI kemudian Input Rekening Baru</li>
            <li>Masukkan nomor Rekening Debit</li>
            <li>Masukkan nomor Rekening Tujuan dengan 16 digit Nomor Virtual Account <b><?php echo $trxid ?></b></li>
            <li>Masukkan jumlah pembayaran. Klik Benar</li>
            <li>Konfirmasi transaksi dan masukkan Password Transaksi</li>
            <li>Transaksi Anda Telah Berhasil</li>
        </ol>
    </div>

    <button class="accordion" >ATM Bank Lain <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Nasabah melakukan pembayaran melalui ATM Bank Lain</li>
            <li>Pilih menu Transaksi Lainnya</li>
            <li>Pilih menu Transfer</li>
            <li>Pilih Rekening BNI Lain</li>
            <li>Masukkan Kode Bank BNI (009) dan Pilih Benar</li>
            <li>Masukkan jumlah pembayaran</li>
            <li>Masukkan 16 digit Nomor Virtual Account <b><?php echo $trxid ?></b></li>
            <li>Pilih Rekening yang akan di debit</li>
            <li>Konfirmasi Pembayaran</li>
            <li>Transaksi Selesai. Harap Simpan Struk Transaksi yang anda dapatkan</li>
        </ol>
    </div>
    			<?php
    		break;
    	case '708':
    		?>
    		<button class="accordion" >Pembayaran Melalui ATM Danamon <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel" style="display: block;">
        <b>Pembayaran Melalui ATM Danamon</b>
        <ol>
            <li>Masuk ke menu Pembayaran -> Lainnya -> Virtual Account </li>
            <li>Masukkan 16 digit nomor Virtual Account </li>
            <li>Periksa jumlah tagihan dan konfirmasi pembayaran. </li>
        </ol>
    </div>

    <button class="accordion" >Transfer dari Bank Lain <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Transfer melalui Bank Lain yang tergabung dalam jaringan ATM Bersama, ALTO dan Prima.</li>
            <li>Masukkan kode Bank Danamon (011) dan 16 digit nomor Virtual Account di rekening tujuan.</li>
            <li>Masukkan nominal transfer sesuai tagihan.</li>
        </ol>
    </div>
    		<?php
    		break;
    	case '702':
    		?>
    <button class="accordion" style="display: block;">Tata Cara Membayar Melalui ATM/ANT/SETAR-Setor Tarik <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <b>Langkah-langkah transaksi BCA Virtual Account melalui ATM/ANT/SETAR-Setor Tarik:</b>
        <br>
        <ol>
            <li>Pilih menu Transfer – Ke Rek BCA Virtual Account</li>
            <li>Masukkan Nomor BCA Virtual Account, lalu pilih Benar</li>
            <li>Pilih menu “Ke Rek BCA Virtual Account”</li>
            <li>Layar ATM akan menampilkan konfirmasi transaksi:<br>
                <ul>
                    <li>Pilih Ya bila setuju, atau </li>
                    <li>Masukkan jumlah transfer, lalu pilih Benar. Layar ATM akan kembali menampilkan konfirmasi jumlah pembayaran, pilih Ya bila ingin membayar</li>
                </ul>
            </li>
            <li>Ikuti langkah selanjutnya sampai transaksi selesai</li>
        </ol>
    </div>

    <button class="accordion" >KlikBCA Individu (Full Site dan versi Smartphone) <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <b>Langkah-langkah transaksi BCA Virtual Account melalui KlikBCA Individu: </b>
        <br>
        <ol>
            <li>Pilih Menu Transfer Dana – Transfer ke BCA Virtual Account</li>
            <li>Masukkan nomor BCA Virtual Account, atau pilih Dari Daftar Transfer </li>
            <li>Akan tampil konfirmasi transaksi: <br>
                <ul>
                    <li>Masukkan jumlah nominal transfer dan berita, atau </li>
                    <li>Masukkan berita</li>
                </ul>
            </li>
            <li>Ikuti langkah selanjutnya sampai transaksi selesai </li>
        </ol>
    </div>

    <button class="accordion" >KlikBCA Bisnis <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <b>Langkah-langkah transaksi BCA Virtual Account melalui KlikBCA Bisnis: </b>
        <br>
        <ul>
            <li>Daftar Transfer: <br>
                <ol>
                    <li>Pilih menu Daftar Transfer - Tambah, pilih Ke BCA Virtual Account</li>
                    <li>Masukkan nomor BCA Virtual Account</li>
                    <li>Ikuti langkah selanjutnya sampai selesai </li>
                </ol>
            </li>
            <li>Transfer Dana: <br>
                <ol>
                    <li>Pilih menu Transfer Dana – ke BCA Virtual Account</li>
                    <li>Pilih nomor rekening yang akan didebet dan pilih nomor BCA Virtual Account, lalu lanjut </li>
                    <li>Akan tampil konfirmasi transaksi: <br>
                        <ul>
                            <li>Masukkan jumlah nominal transfer dan berita, atau</li>
                            <li>Masukkan berita</li>
                        </ul>
                    </li>
                    <li>Ikuti langkah selanjutnya sampai transaksi selesai</li>
                </ol>
            </li>
            <li>Otorisasi Transaksi: <br>
                <ul style="list-style-type:none">
                    <li>Pilih menu Transfer Dana - Otorisasi Transaksi Tergantung single/multi otorisasi</li>
                    <li>Untuk Single Otorisasi: <br>
                        <ul>
                            Login User Releaser
                            <li>Tandai transaksi pada tabel Transaksi Yang Belum Diotorisasi, pilih Setuju </li>
                            <li>Ikuti langkah selanjutnya sampai selesai</li>
                        </ul>
                    </li>

                    <li>Untuk Multi Otorisasi: <br>
                        <ul>
                            <li>Login User Approver
                                <ol>
                                    <li>Tandai transaksi pada tabel Approver, pilih Setuju </li>
                                </ol>
                            </li>
                            <li>Login User Releaser
                                <ol>
                                    <li>Tandai transaksi pada tabel Transaksi Yang Belum Diotorisasi, pilih Setuju</li>
                                    <li>Ikuti langkah selanjutnya sampai selesai</li>
                                </ol>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <button class="accordion" >m-BCA (BCA Mobile) <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <b>Langkah-langkah transaksi BCA Virtual Account melalui m-BCA (BCA Mobile):</b>
        <br>
        <ol>
            <li>Pilih m-Transfer</li>
            <li>Pilih Transfer – BCA Virtual Account</li>
            <li>Pilih nomor rekening yang akan didebet</li>
            <li>Masukkan nomor BCA Virtual Account, lalu pilih OK</li>
            <li>Tampil konfirmasi nomor BCA Virtual Account dan rekening pendebetan, lalu Kirim</li>
            <li>Tampil konfirmasi pembayaran, lalu pilih OK <br>
                <ul>
                    <li>Masukkan jumlah nominal transfer dan berita, atau </li>
                    <li>Masukkan berita</li>
                </ul>
            </li>
            <li>Ikuti langkah selanjutnya sampai transaksi selesai </li>
        </ol>
    </div>

    <button class="accordion" >m-BCA (STK) <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <b>Langkah-langkah transaksi BCA Virtual Account melalui m-BCA (STK):</b>
        <br>
        <ol>
            <li>Pilih m-BCA </li>
            <li>Pilih m-Payment</li>
            <li>Pilih Lainnya</li>
            <li>Masukkan TVA pada Nama PT, lalu OK</li>
            <li>Masukkan nomor BCA Virtual Account pada No. Pelanggan, lalu OK</li>
            <li>Masukkan PIN m-BCA, lalu OK</li>
            <li>Pilih Pilih nomor rekening yang akan didebet, lalu lanjut</li>
            <li>Akan muncul konfirmasi pembayaran, lalu pilih OK <br>
                <ul>
                    <li>Masukkan jumlah bayar dan berita, atau </li>
                    <li>Masukkan berita</li>
                </ul>
            </li>
            <li>Ikuti langkah selanjutnya sampai transaksi selesai </li>
        </ol>
    </div>
    		<?php
    		break;
    	case '408':
    		?>
    <button class="accordion" style="display: block;">Pembayaran VA Melalui Mesin ATM Maybank - Menu Pembayaran <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Pilih menu PEMBAYARAN/TOP UP PULSA</li>
            <li>Pilih menu VIRTUAL ACCOUNT</li>
            <li>Masukkan nomor VIRTUAL ACCOUNT yang tertera pada halaman konfirmasi</li>
            <li>Pilih YA untuk menyetujui pembayaran tersebut</li>
        </ol>
    </div>

    <button class="accordion" >Pembayaran VA Melalui Mesin ATM Maybank - Menu Transfer <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Pilih menu TRANSFER</li>
            <li>Pilih menu VIRTUAL ACCOUNT</li>
            <li>Masukkan nomor VIRTUAL ACCOUNT yang tertera pada halaman konfirmasi</li>
            <li>Masukkan jumlah pembayaran sesuai dengan yang ditagihkan dalam halaman konfirmasi</li>
            <li>Silahkan masukkan nomor referensi apabila diperlukan, lalu tekan BENAR</li>
            <li>Pilih YA untuk menyetujui pembayaran tersebut</li>
        </ol>
    </div>

    <button class="accordion" >Pembayaran VA Melalui Maybank Internet Banking <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Silahkan login Internet Banking dari Maybank</li>
            <li>Pilih menu Rekening dan Transaksi</li>
            <li>Kemudian pilih Maybank Virtual Account</li>
            <li>Masukkan nomor rekening dengan nomor Virtual Account Anda : <b><?php echo $trxid ?></b></li>
            <li>Masukkan jumlah pembayaran sesuai dengan yang ditagihkan dalam halaman konfirmasi</li>
            <li>Masukkan SMS Token (TAC) dan klik Setuju</li>
        </ol>
    </div>

    <button class="accordion" >Pembayaran VA Melalui ATM Bank lain <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Pilih menu TRANSFER ANTAR BANK </li>
            <li>Pilih Maybank sebagai bank tujuan atau dengan memasukkan kode bank Maybank “016” diikuti dengan 16 digit nomor VIRTUAL ACCOUNT yang tertera pada halaman konfirmasi</li>
            <li>Masukkan jumlah pembayaran sesuai dengan yang ditagihkan dalam halaman konfirmasi</li>
            <li>Konfirmasikan transaksi anda pada halaman berikutnya. Apabila benar tekan BENAR untuk mengeksekusi transaksi</li>
        </ol>
    </div>
    		<?php
    		case '400':
    			?>
    			<button class="accordion" >Pembayaran Via Aplikasi <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel" style="display: block;">
      <p>
        <img src='<?php echo $srv."/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/mocash.png" ; ?>' alt="How">
      </p>
    </div>

    <button class="accordion" >Pembayaran Via SMS <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <center>
            <p>
                Lakukan pembayaran dengan cara mengirim SMS ke 9123, ketik: BAYAR MD (spasi) STORE_ID
                (spasi) AMOUNT (spasi) ORDER_ID (spasi) PIN atau BAYAR MD 1902291 (spasi) 0 (spasi)
                <?php echo $trxid ?> (spasi) (PIN ANDA)
            </p>
        </center>
    </div>
    			<?php
    		break;
    		case '706':
    			?>
    			<button class="accordion" >Tata Cara Membayar Melalui Indomaret <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel" style="display: block;">
        <ol>
            <li>Catat dan simpan kode pembayaran Indomaret anda, yaitu : <b><?php echo $trxid?></b></li>
            <li>Tunjukan kode pembayaran ke kasir Indomaret terdekat dan lakukan pembayaran senilai <b><?php echo $currency." ".number_format($total).".00" ?></b></li>
            <li>Simpan bukti pembayaran yang sewaktu-waktu diperlukan jika terjadi kendala transaksi</li>
        </ol>
    </div>
    			<?php
    			break;
    		case '707':
    			?>
    			<button class="accordion menu-item-object-page" >Pembayaran Melalui Alfamart <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel" style="display: block;">
        <ol>
            <li>Catat dan simpan kode pembayaran Alfamart Anda, yaitu : <?php echo $trxid ?>.</li>
            <li>Datangi kasir Alfamart terdekat dan beritahukan pada kasir bahwa Anda ingin melakukan pembayaran "<?php echo $merchant ?>".</li>
            <li>Beritahukan kode pembayaran Alfamart Anda pada kasir dan silahkan lakukan pembayaran Anda senilai <?php echo $currency." ".number_format($total).".00" ?>.</li>
            <li>Simpan struk pembayaran Anda sebagai tanda bukti pembayaran yang sah.</li>
        </ol>
    </div>
    			<?php
    			break;
    	case '703':
    		?>
    <button class="accordion" style="display: block;">Tata Cara Membayar Melalui ATM <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Catat kode pembayaran yang anda dapat</li>
            <li>Gunakan ATM Mandiri untuk menyelesaikan pembayaran</li>
            <li>Masukkan PIN anda</li>
            <li>Pilih 'Bayar/Beli' lalu pilih 'Lainnya'</li>
            <li>Cari pilihan MULTI PAYMENT</li>
            <li>Masukkan Kode Perusahaan <b><?php echo $mid ?></b></li>
            <li>Masukkan Kode Pelanggan <b><?php echo $trxid ?></b></li>
            <li>Pilih Tagihan Anda jika sudah sesuai tekan YA</li>
            <li>Konfirmasikan tagihan anda apakah sudah sesuai lalu tekan YA</li>
            <li>Harap Simpan Struk Transaksi yang anda dapatkan</li>
        </ol>
    </div>

    <button class="accordion" >Tata Cara Membayar Melalui Mandiri Internet Banking <i class="fa fa-arrow-down" style="float: right;"></i></button>
    <div class="panel">
        <ol>
            <li>Pada Halaman Utama pilih submenu Lain-lain di bawah menu Pembayaran</li>
            <li>Cari Penyedia Jasa 70009 MitraPay</li>
            <li>Isi Nomor Pelanggan yang anda dapatkan</li>
            <li>Masukkan Jumlah Pembayaran sesuai dengan Jumlah Tagihan anda</li>
            <li>Pilih LANJUTKAN</li>
            <li>Transaksi selesai, jika perlu CETAK hasil transaksi anda</li>
        </ol>
    </div>
    		<?php
    		break;
        case '711':
            ?>
                <button class="accordion" style="display: block;">Pembayaran melalui ShopeePay <i class="fa fa-arrow-down" style="float: right;"></i></button>
                <div class="panel">
                    <ol>
                        <li>Buka aplikasi Shopee</li>
                        <li>Klik logo “Scan”</li>
                        <li>Scan QR Code</li>
                        <li>Klik tombol “Bayar Sekarang”</li>
                    </ol>
                </div>

                <button class="accordion" >Pembayaran melalui Mobile Banking atau E-Money lainnya <i class="fa fa-arrow-down" style="float: right;"></i></button>
                <div class="panel">
                    <ol>
                        <li>Buka aplikasi mobile banking atau e-money</li>
                        <li>Klik logo “Pay” atau “Scan”</li>
                        <li>Scan QR Code</li>
                        <li>Klik tombol “Pay” atau “Bayar”</li>
                    </ol>
                </div>
            <?php
            break;
    	default:
    		# code...
    		break;
    }
}
 ?>
