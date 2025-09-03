<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Bank Sampah</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS LENGKAP UNTUK TAMPILAN DENGAN JARAK YANG LEBIH RAPAT */

        /* Definisi Variabel Warna dan Font */
        :root {
            --primary-green: #2e8b57;
            --dark-green: #388E3C;
            --accent-green: #4CAF50;
            --bg-light-green: #E8F5E9;
            --bg-light-gray: #f8f8f8;
            --text-dark: #2c3e50;
            --text-medium: #34495e;
            --text-light: #7f8c8d;
            --white: #ffffff;
            --shadow-subtle: rgba(0, 0, 0, 0.05);
            --shadow-medium: rgba(0, 0, 0, 0.1);
        }

        /* RESET & Gaya Umum */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-light-gray);
            color: var(--text-dark);
            line-height: 1.6;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 25px;
        }

        /* --- Navbar & Footer (Tetap) --- */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background: var(--white);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .navbar img {
            height: 50px;
        }
        .navbar h2 {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-green);
        }
        .navbar .navbar-links {
            display: flex;
            gap: 25px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .navbar a {
            text-decoration: none;
            color: var(--accent-green);
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s;
        }
        .navbar a:hover, .navbar a.active {
            color: var(--dark-green);
            transform: translateY(-2px);
        }
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--dark-green);
        }

        .footer {
            background: #fafffaff;
            color: #333;
            padding: 55px 20px 30px;
            text-align: center;
        }
        .footer-top {
            margin-bottom: 40px;
        }
        .footer-logo {
            width: 110px;
            margin-bottom: 10px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        .footer h3 {
            font-size: 28px;
            margin: 5px 0 10px;
            font-family: 'Montserrat', sans-serif;
            color: #1f6e2f;
        }
        .footer-desc {
            font-size: 15px;
            color: #555;
            max-width: 550px;
            margin: auto;
        }
        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin-bottom: 50px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }
        .footer-col {
            flex: 1;
            min-width: 250px;
            text-align: left;
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .footer-col:hover {
            transform: translateY(-5px);
        }
        .footer h4 {
            font-size: 20px;
            margin-bottom: 25px;
            color: #28a745;
            position: relative;
        }
        .footer h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 40px;
            height: 3px;
            background: #28a745;
            border-radius: 2px;
        }
        .footer ul {
            list-style: none;
            padding: 0;
        }
        .footer ul li {
            margin-bottom: 15px;
        }
        .footer ul li a {
            text-decoration: none;
            color: #333;
            font-size: 15px;
            transition: color 0.3s;
            display: inline-block;
        }
        .footer ul li a:hover {
            color: #28a745;
        }
        .footer p {
            font-size: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .footer .socials {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }
        .footer .socials a {
            width: 40px;
            height: 40px;
            background: #28a745;
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
        }
        .footer .socials a:hover {
            background: #1e7e34;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .footer-bottom {
            border-top: 1px solid #dcdcdc;
            padding-top: 20px;
            font-size: 14px;
            color: #888;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .footer-bottom p {
            margin: 0;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 350px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(46,139,87,0.6), rgba(70,130,180,0.6)), url('aset/luar.jpg') center/cover no-repeat;
            color: #fff;
            text-align: center;
        }
        .hero-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            z-index: 0;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.8));
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            padding: 20px;
            animation: fadeIn 1s ease-out;
        }
        .hero-content h1 {
            font-size: 67px;
            margin: 0 0 15px;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
            font-weight: 700;
        }
        .hero-content p {
            font-size: 1.6em;
            font-style: italic;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
            font-weight: 500;
        }
        
        /* --- General Section Style & Alternating Background --- */
        .content-section {
            padding: 5px 0; /* Padding vertikal dikurangi dari 100px */
            position: relative;
            z-index: 1;
        }
        .content-section:nth-of-type(odd) {
            background-color: var(--bg-light-gray);
        }
        .content-section:nth-of-type(even) {
            background-color: var(--bg-light-green);
        }

        /* --- Style Umum untuk Text-Only Section --- */
        .text-section {
            padding: 60px 0; /* Padding vertikal dikurangi dari 80px */
            text-align: center;
        }
        .text-section h2 {
            font-size: 2.5em;
            color: var(--primary-green);
            font-weight: 700;
            margin-bottom: 15px; /* Margin bawah dikurangi */
            position: relative;
            display: inline-block;
        }
        .text-section h2::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background-color: var(--accent-green);
            margin: 8px auto 0 auto; /* Margin atas dan bawah dikurangi */
        }
        .text-section p {
            font-size: 1.1em;
            color: var(--text-medium);
            max-width: 800px;
            margin: 0 auto 30px auto; /* Margin bawah dikurangi dari 50px */
            line-height: 1.8;
        }

        /* --- Kartu Misi dan Visi --- */
        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px; /* Jarak antar kartu dikurangi dari 25px */
            margin-top: 30px; /* Margin atas dikurangi dari 50px */
        }
        .mission-card {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 8px 25px var(--shadow-subtle);
            padding: 20px 30px;
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .mission-card::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 100px;
            height: 100px;
            background-color: var(--accent-green);
            opacity: 0.1;
            border-radius: 50%;
            transition: all 0.5s ease;
        }
        .mission-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px var(--shadow-medium);
        }
        .mission-card:hover::after {
            transform: scale(1.5);
            opacity: 0.2;
        }
        .mission-card i {
            font-size: 2.2em;
            color: var(--accent-green);
            margin-bottom: 8px; /* Margin dikurangi */
            position: relative;
            z-index: 2;
        }
        .mission-card h4 {
            font-size: 1.1em;
            color: var(--text-dark);
            margin-bottom: 5px; /* Margin dikurangi */
            font-weight: 700;
            position: relative;
            z-index: 2;
        }
        .mission-card p {
            font-size: 0.9em;
            color: var(--text-light);
            line-height: 1.6;
            position: relative;
            z-index: 2;
            flex-grow: 1;
        }

        /* --- Kartu Proses Bekerja --- */
        .how-it-works-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px; /* Jarak antar kartu dikurangi dari 25px */
            margin-top: 30px; /* Margin atas dikurangi dari 50px */
        }
        .step-card {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px var(--shadow-subtle);
            padding: 25px 30px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .step-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px var(--shadow-medium);
        }
        .step-card .step-number {
            font-size: 2.2em;
            font-weight: 700;
            color: var(--accent-green);
            margin-bottom: 8px; /* Margin dikurangi */
            transition: transform 0.3s ease;
            display: inline-block;
        }
        .step-card:hover .step-number {
            transform: scale(1.1);
        }
        .step-card h4 {
            font-size: 1.3em;
            color: var(--primary-green);
            margin-bottom: 8px;
            font-weight: 700;
        }
        .step-card p {
            font-size: 0.9em;
            color: var(--text-medium);
            flex-grow: 1;
        }

        /* --- Media Queries --- */
        @media (max-width: 768px) {
            /* Navbar Responsiveness */
            .navbar {
                padding: 15px 20px;
                align-items: center;
                flex-wrap: nowrap;
                justify-content: space-between;
            }
            .navbar .logo {
                flex: 1;
            }
            .navbar .navbar-links {
                display: none;
                flex-direction: column;
                width: 100%;
                gap: 10px;
                margin-top: 15px;
                position: absolute;
                top: 85px;
                left: 0;
                background: var(--white);
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                padding: 20px 0;
                text-align: center;
                z-index: 9;
            }
            .navbar .navbar-links.active {
                display: flex;
            }
            .menu-toggle {
                display: block;
            }

            /* General responsive adjustments */
            .hero-section {
                height: 250px;
            }
            .hero-content h1 {
                font-size: 40px;
            }
            .hero-content p {
                font-size: 16px;
            }
            .content-section {
                padding: 40px 0;
            }
            .text-section {
                padding: 40px 0;
            }
            .text-section h2 {
                font-size: 28px;
            }
            .text-section p {
                font-size: 15px;
            }
            .mission-grid, .how-it-works-grid {
                grid-template-columns: 1fr;
            }
            .mission-card, .step-card {
                padding: 20px;
                text-align: center;
            }
            .mission-card i {
                margin-bottom: 12px;
            }
            .mission-card h4::after {
                left: 50%;
                transform: translateX(-50%);
            }
            .mission-card:hover {
                transform: none;
            }

            /* Footer Responsiveness */
            .footer-col {
                min-width: 80%;
                text-align: center;
            }
            .footer-col h4::after {
                left: 50%;
                transform: translateX(-50%);
            }
            .footer p, .footer .socials {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    
    <nav class="navbar">
        <div class="logo">
            <img src="aset/logop.png" alt="Logo Bank Sampah">
            <h2>Bank Sampah Indonesia</h2>
        </div>
        <ul class="navbar-links">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="tentangkami.php" class="active">Tentang Kami</a></li>
        </ul>
        <div class="menu-toggle" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <div class="hero-section">
    <div class="hero-content">
        <h1>Tentang Kami</h1>
        <p>Bank Sampah Indonesia - Sampah Bukan Masalah, Tapi Berkah</p>
    </div>
</div>

    <section class="content-section">
        <div class="container text-section">
            <h2>Siapa Kami?</h2>
            <p>Selamat datang di Bank Sampah Indonesia! Kami adalah sebuah inisiatif komunitas yang berdedikasi untuk mengubah cara pandang masyarakat terhadap sampah. Berdiri sejak tahun **2025**, kami hadir sebagai solusi inovatif untuk masalah sampah di **Banguntapan, Daerah Istimewa Yogyakarta**.</p>
            <p>Kami percaya bahwa dengan pengelolaan yang tepat, sampah yang selama ini dianggap sebagai limbah bisa menjadi aset yang berharga, baik bagi lingkungan maupun ekonomi lokal. Kami bertekad untuk menciptakan lingkungan yang lebih bersih, sehat, dan berkelanjutan untuk generasi mendatang.</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container text-section">
            <h2>Visi & Misi Kami</h2>
            <p>Kami memiliki tujuan mulia untuk menciptakan dampak positif yang berkelanjutan di masyarakat. Visi kami menjadi panduan, sementara misi kami adalah langkah-langkah nyata untuk mencapainya.</p>
            
            <div class="mission-grid">
                <div class="mission-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h4>Mengedukasi Masyarakat</h4>
                    <p>Meningkatkan kesadaran dan pemahaman tentang pentingnya memilah sampah dari sumbernya.</p>
                </div>
                <div class="mission-card">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h4>Memberdayakan Ekonomi</h4>
                    <p>Memberikan nilai ekonomi dari sampah yang dipilah.</p>
                </div>
                <div class="mission-card">
                    <i class="fas fa-leaf"></i>
                    <h4>Lingkungan Berkelanjutan</h4>
                    <p>Mengurangi volume sampah yang berakhir di TPA dan mendukung gerakan daur ulang.</p>
                </div>
                <div class="mission-card">
                    <i class="fas fa-handshake"></i>
                    <h4>Ekosistem Kolaboratif</h4>
                    <p>Berkomunikasi dan bekerja sama dengan berbagai pihak.</p>
                </div>
                <div class="mission-card">
                    <i class="fas fa-users-cog"></i>
                    <h4>Sistem Inovatif</h4>
                    <p>Menerapkan teknologi dan metode baru untuk meningkatkan efisiensi operasional.</p>
                </div>
                <div class="mission-card">
                    <i class="fas fa-map-marked-alt"></i>
                    <h4>Memperluas Jangkauan</h4>
                    <p>Membuka unit-unit baru di Banguntapan.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="container text-section">
            <h2>Bagaimana Kami Bekerja?</h2>
            <p>Bank Sampah kami beroperasi dengan alur yang sederhana dan efisien, dirancang untuk memudahkan setiap orang berpartisipasi dalam perubahan positif.</p>
            
            <div class="how-it-works-grid">
                <div class="step-card">
                    <div class="step-number">01</div>
                    <h4>Menjadi Nasabah</h4>
                    <p>Warga mendaftar sebagai nasabah di kantor Bank Sampah.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">02</div>
                    <h4>Menabung Sampah</h4>
                    <p>Bawa sampah yang sudah dipilah ke Bank Sampah sesuai jadwal.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">03</div>
                    <h4>Penimbangan & Pencatatan</h4>
                    <p>Sampah dinilai dan dicatat dalam buku tabungan.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">04</div>
                    <h4>Penarikan Saldo</h4>
                    <p>Saldo tabungan dapat ditarik dalam bentuk uang tunai.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">05</div>
                    <h4>Penyaluran Sampah</h4>
                    <p>Sampah disalurkan kepada pengepul atau pabrik daur ulang.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-top">
            <img src="aset/logop.png" alt="Logo Bank Sampah" class="footer-logo">
            <h3>Bank Sampah Indonesia</h3>
            <p class="footer-desc">Mengelola sampah jadi lebih bernilai, menuju Indonesia yang hijau dan bersih.</p>
        </div>
        <div class="footer-container">
            <div class="footer-col">
                <h4>Menu</h4>
                <ul>
                    <li><a href="#">Beranda</a></li>
                    <li><a href="register.php">Pendaftaran</a></li>
                    <li><a href="#education">Edukasi</a></li>
                    <li><a href="#contact">Kontak</a></li>
                </ul>
            </div>
            <div class="footer-col" id="contact">
                <h4>Kontak</h4>
                <p><i class="fas fa-phone"></i> +62 812 3456 7890</p>
                <p><i class="fas fa-envelope"></i> info@banksampah.id</p>
                <p><i class="fas fa-map-marker-alt"></i> Jl. Merdeka No. 123, Jakarta</p>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Bank Sampah Indonesia – Semua Hak Dilindungi</p>
        </div>
    </footer>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            var navLinks = document.querySelector('.navbar-links');
            navLinks.classList.toggle('active');
        });
    </script>
</body>
</html>