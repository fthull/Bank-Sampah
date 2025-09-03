<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Sampah Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Variables for better maintenance */
        :root {
            --primary-green: #4CAF50;
            --dark-green: #388E3C;
            --light-green: #E8F5E9;
            --light-background: #F7FBF7;
            --white: #FFFFFF;
            --text-color: #2E7D32;
        }

        /* General body styles */
        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light-background);
            color: var(--text-color);
            line-height: 1.6;
        }

        /* --- Navigasi --- */
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
            color: var(--primary-green);
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s;
        }
        .navbar a:hover, .active {
            color: var(--dark-green);
            transform: translateY(-2px);
        }

        /* Menu Toggle Button (Hamburger) */
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--dark-green);
        }

        /* --- Hero Section --- */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("aset/bg.jpg") no-repeat center center/cover;
            text-align: center;
            color: var(--white);
            min-height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
            position: relative;
        }
        .hero * {
            position: relative;
            z-index: 1;
        }
        .hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 67px;
            margin: 10px 0;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.6);
        }
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.6);
            max-width: 600px;
        }
        .btn-register {
            display: inline-block;
            padding: 14px 30px;
            border-radius: 50px;
            background: var(--white);
            color: var(--dark-green);
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: transform 0.3s, background 0.3s;
        }
        .btn-register:hover {
            background: var(--light-green);
            transform: translateY(-3px);
        }

        /* --- Bagian Fitur --- */
        .features {
            display: flex;
            justify-content: center;
            gap: 30px;
            max-width: 1200px;
            margin: -80px auto 60px;
            padding: 0 20px;
            text-align: center;
            position: relative;
            z-index: 5;
            flex-wrap: wrap;
        }
        .feature-card {
            flex: 1;
            min-width: 250px;
            max-width: 300px;
            background: var(--white);
            border-radius: 15px;
            padding: 30px 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: transform 0.3s, box-shadow 0.3s;
            margin: 15px 0;
            border-bottom: 5px solid var(--primary-green);
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }
        .feature-card i {
            font-size: 50px;
            color: var(--primary-green);
            margin-bottom: 15px;
        }
        .feature-card h3 {
            margin: 10px 0;
            font-size: 20px;
            color: var(--dark-green);
            font-family: 'Montserrat', sans-serif;
        }
        .feature-card p {
            font-size: 15px;
            color: #555;
        }

        /* --- Mengapa Harus Bank Sampah --- */
        .why {
            padding: 80px 20px;
            background: var(--light-green);
            text-align: center;
        }
        .why h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            margin-bottom: 60px;
            color: var(--dark-green);
            position: relative;
            display: inline-block;
        }
        .why h2::after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary-green);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        .why-circles {
            display: flex;
            justify-content: center;
            gap: 35px;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: auto;
        }
        .circle-card {
            flex: 1;
            min-width: 250px;
            max-width: 350px;
            background: var(--white);
            padding: 30px 25px;
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin: 15px 0;
        }
        .circle-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .circle-icon {
            width: 90px;
            height: 90px;
            background: var(--primary-green);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
            transition: background 0.3s;
        }
        .circle-card:hover .circle-icon {
            background: var(--dark-green);
        }
        .circle-card h3 {
            font-size: 20px;
            color: var(--dark-green);
            margin-bottom: 12px;
            font-family: 'Montserrat', sans-serif;
        }
        .circle-card p {
            font-size: 16px;
            color: #555;
        }

        /* --- Bagian Edukasi (Timeline) --- */
        .education {
            padding: 80px 20px;
            background: #f8f8f8ff;
            text-align: center;
        }
        .education h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 38px;
            margin-bottom: 60px;
            color: var(--dark-green);
            position: relative;
            display: inline-block;
        }
        .education h2::after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary-green);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        .timeline-edu {
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
            text-align: left;
        }
        .timeline-edu::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: var(--dark-green);
            transform: translateX(-50%);
        }
        .timeline-item-edu {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 60px;
            position: relative;
        }
        .timeline-item-edu:nth-child(even) {
            flex-direction: row-reverse;
        }
        .timeline-item-edu .content-edu {
            width: 45%;
            padding: 30px;
            background: var(--light-background);
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: left;
        }
        .timeline-item-edu:nth-child(odd) .content-edu {
            margin-right: 55%;
        }
        .timeline-item-edu:nth-child(even) .content-edu {
            margin-left: 55%;
        }
        .timeline-item-edu .content-edu:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .timeline-item-edu .circle-edu {
            width: 60px;
            height: 60px;
            background: var(--primary-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 28px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }
        .timeline-item-edu h3 {
            font-size: 1.4em;
            color: var(--dark-green);
            margin-bottom: 10px;
            font-family: 'Montserrat', sans-serif;
        }
        .timeline-item-edu p {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 0;
        }
        
        /* --- Call to Action (CTA) --- */
        .cta {
            padding: 100px 20px;
            background: #cff1d2ff;
            text-align: center;
            color: var(--dark-green);
        }
        .cta-content h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        .cta-content p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        .cta-btn {
            display: inline-block;
            padding: 16px 40px;
            border-radius: 50px;
            background: var(--white);
            color: var(--primary-green);
            font-weight: bold;
            text-decoration: none;
            font-size: 18px;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .cta-btn:hover {
            background: #F0F0F0;
            color: var(--dark-green);
            transform: scale(1.05);
        }

        /* --- Footer --- */
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
        
        /* --- Responsivitas (Media Queries) --- */
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
            .hero {
                padding: 40px 15px;
                min-height: 60vh;
            }
            .hero h1 {
                font-size: 40px;
            }
            .hero p {
                font-size: 16px;
            }
            .features, .why-circles, .footer-container {
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
            .feature-card, .circle-card {
                max-width: 90%;
                min-width: auto;
            }
            .why, .education, .cta {
                padding: 40px 15px;
            }
            .why h2, .education h2, .cta-content h2 {
                font-size: 28px;
                margin-bottom: 30px;
            }
            .why h2::after, .education h2::after {
                margin: 10px auto 0;
            }

            /* Perbaikan timeline untuk tampilan lurus dan sejajar */
            .timeline-edu {
                width: 100%;
                padding: 0 10px;
                position: static;
                box-sizing: border-box;
            }
            .timeline-edu::before {
                display: none;
            }
            .timeline-item-edu {
                flex-direction: column;
                align-items: center;
                margin: 0 auto 30px;
                width: 100%;
            }
            .timeline-item-edu:nth-child(even) {
                flex-direction: column;
            }
            .timeline-item-edu .content-edu {
                width: 90%;
                margin: 0 auto; 
                padding: 20px;
                text-align: center;
                font-size: 14px;
            }
            .timeline-item-edu:nth-child(odd) .content-edu,
            .timeline-item-edu:nth-child(even) .content-edu {
                margin: 0 auto;
            }
            .timeline-item-edu .content-edu h3 {
                font-size: 1.2em;
            }
            .timeline-item-edu .circle-edu {
                position: static;
                transform: none;
                margin-bottom: 15px;
            }

            /* Perbaikan Footer responsiveness */
            .footer-col {
                /* Perbaikan: Mengatur min-width agar card footer lebih besar */
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
            <li><a href="#" class="active">Beranda</a></li>
            <li><a href="tentangkami.php">Tentang Kami</a></li>
        </ul>
        <div class="menu-toggle" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <section class="hero">
        <h1>Bank Sampah Indonesia</h1>
        <p>Jadikan sampah lebih bernilai dan dukung lingkungan yang lebih bersih.</p>
        <div class="buttons">
            <a href="register.php" class="btn-register">Gabung Sekarang</a>
        </div>
    </section>

    <section class="features">
        <div class="feature-card">
            <i class="fas fa-recycle"></i>
            <h3>Kelola Sampah</h3>
            <p>Sistem kami mempermudah pengelolaan berbagai jenis sampah.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-piggy-bank"></i>
            <h3>Tabungan Digital</h3>
            <p>Sampah Anda diubah menjadi saldo digital yang dapat dicairkan.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-leaf"></i>
            <h3>Dampak Positif</h3>
            <p>Kontribusi nyata Anda dalam menciptakan lingkungan yang lebih hijau.</p>
        </div>
    </section>

    <section class="why">
        <h2>Mengapa Bank Sampah Indonesia?</h2>
        <div class="why-circles">
            <div class="circle-card">
                <div class="circle-icon"><i class="fas fa-hand-holding-heart"></i></div>
                <h3>Peduli Lingkungan</h3>
                <p>Melestarikan alam dengan mengurangi sampah dan daur ulang.</p>
            </div>
            <div class="circle-card">
                <div class="circle-icon"><i class="fas fa-coins"></i></div>
                <h3>Nilai Ekonomi</h3>
                <p>Ubah sampah menjadi sumber pendapatan tambahan.</p>
            </div>
            <div class="circle-card">
                <div class="circle-icon"><i class="fas fa-users"></i></div>
                <h3>Komunitas Hijau</h3>
                <p>Bergabung dengan gerakan peduli lingkungan yang berdampak besar.</p>
            </div>
            <div class="circle-card">
                <div class="circle-icon"><i class="fas fa-globe-asia"></i></div>
                <h3>Dukungan Global</h3>
                <p>Ambil bagian dalam aksi global untuk bumi yang lebih bersih.</p>
            </div>
            <div class="circle-card">
                <div class="circle-icon"><i class="fas fa-lightbulb"></i></div>
                <h3>Edukasi & Inovasi</h3>
                <p>Dapatkan informasi terbaru seputar daur ulang dan inovasi.</p>
            </div>
            <div class="circle-card">
                <div class="circle-icon"><i class="fas fa-handshake"></i></div>
                <h3>Kemitraan Luas</h3>
                <p>Bekerja sama dengan berbagai pihak untuk hasil maksimal.</p>
            </div>
        </div>
    </section>

    <section class="education">
        <h2>Edukasi Pengelolaan Sampah</h2>
        <div class="timeline-edu">
            <div class="timeline-item-edu">
                <div class="circle-edu"><i class="fas fa-ban"></i></div>
                <div class="content-edu">
                    <h3>Langkah 1: Stop Buang Sembarangan</h3>
                    <p>Mulai dari hal kecil, pastikan sampah tidak mencemari lingkungan. Buanglah sampah pada tempatnya.</p>
                </div>
            </div>
            <div class="timeline-item-edu">
                <div class="circle-edu"><i class="fas fa-recycle"></i></div>
                <div class="content-edu">
                    <h3>Langkah 2: Pilah Sampah</h3>
                    <p>Pisahkan sampah organik dan anorganik. Hal ini memudahkan proses daur ulang dan pengolahan lebih lanjut.</p>
                </div>
            </div>
            <div class="timeline-item-edu">
                <div class="circle-edu"><i class="fas fa-shopping-bag"></i></div>
                <div class="content-edu">
                    <h3>Langkah 3: Kurangi Penggunaan Plastik</h3>
                    <p>Gunakan tas belanja ramah lingkungan dan botol minum isi ulang untuk mengurangi sampah plastik sekali pakai.</p>
                </div>
            </div>
            <div class="timeline-item-edu">
                <div class="circle-edu"><i class="fas fa-sync-alt"></i></div>
                <div class="content-edu">
                    <h3>Langkah 4: Daur Ulang & Manfaatkan</h3>
                    <p>Ubah barang bekas menjadi sesuatu yang baru dan bernilai ekonomi, atau setorkan ke Bank Sampah terdekat.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="cta-content">
            <h2>Ayo, Jadi Bagian dari Perubahan!</h2>
            <p>Bersama Bank Sampah Indonesia, kita wujudkan lingkungan yang lebih sehat dan sejahtera.</p>
            <a href="register.php" class="cta-btn">Daftar Sekarang</a>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-top">
            <img src="aset/logop.png" alt="Logo Bank Sampah" class="footer-logo">
            <h3>Bank Sampah Indonesia</h3>
            <p class="footer-desc">
                Mengelola sampah jadi lebih bernilai, menuju Indonesia yang hijau dan bersih.
            </p>
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
        // Kode JavaScript untuk membuat menu hamburger berfungsi
        document.getElementById('menu-toggle').addEventListener('click', function() {
            var navLinks = document.querySelector('.navbar-links');
            navLinks.classList.toggle('active');
        });
    </script>
</body>
</html>