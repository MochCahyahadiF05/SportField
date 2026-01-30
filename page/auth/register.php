<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportField - Register</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../assets/img/SportFields.png">
    <link rel="shortcut icon" href="../../assets/img/SportFields.png">
    <link rel="stylesheet" href="../../assets/css/register.css">
</head>
<body>
    <!-- Main Content -->
    <div class="container-wrapper">
        <div class="container">
            <div class="content-grid">
                
                <!-- Left Side - Illustration -->
                <div class="left-side">
                    <div class="illustration-wrapper">
                        <div class="blur-circle blur-top"></div>
                        <div class="blur-circle blur-bottom"></div>
                        
                        <div class="content">
                            <div class="logo-section">
                                <div class="logo-icon">
                                    <span>SF</span>
                                </div>
                                <h1 class="logo-text">SportField</h1>
                            </div>

                            <h2 class="welcome-title">
                                <span class="gradient-text">Bergabung</span>
                                <br>
                                <span class="normal-text">Bersama Kami</span>
                            </h2>
                            <p class="welcome-desc">
                                Daftar sekarang dan nikmati kemudahan booking lapangan olahraga terbaik.
                            </p>
                            
                            <div class="features">
                                <div class="feature-card">
                                    <div class="feature-icon">✓</div>
                                    <div class="feature-content">
                                        <h4 class="feature-title">Gratis Selamanya</h4>
                                        <p class="feature-desc">Tanpa biaya pendaftaran</p>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="feature-icon">💳</div>
                                    <div class="feature-content">
                                        <h4 class="feature-title">Pembayaran Mudah</h4>
                                        <p class="feature-desc">Berbagai metode pembayaran</p>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="feature-icon">🎁</div>
                                    <div class="feature-content">
                                        <h4 class="feature-title">Bonus Member Baru</h4>
                                        <p class="feature-desc">Dapatkan welcome bonus</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Register Form -->
                <div class="right-side">
                    <div class="register-card">
                        
                        <!-- Logo for mobile -->
                        <div class="mobile-logo">
                            <div class="logo-icon">
                                <span>SF</span>
                            </div>
                            <h1 class="logo-text">SportField</h1>
                        </div>

                        <div class="form-header">
                            <h3 class="form-title">Daftar Akun</h3>
                            <p class="form-subtitle">Buat akun baru dan mulai booking</p>
                        </div>

                        <div id="messageAlert"></div>

                        <form class="register-form" id="registerForm">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="name" id="name" class="form-input with-icon" placeholder="John Doe" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="email" name="email" id="email" class="form-input with-icon" placeholder="john@example.com" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <input type="tel" name="phone" id="phone" class="form-input with-icon" placeholder="08xxxxxxxxxx">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input type="password" id="registerPassword" name="password" class="form-input with-icon" placeholder="••••••••" required>
                                    <button type="button" onclick="togglePassword('registerPassword')" class="toggle-password">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input type="password" id="confirmPassword" class="form-input with-icon" placeholder="••••••••" required>
                                    <button type="button" onclick="togglePassword('confirmPassword')" class="toggle-password">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="terms-checkbox">
                                <input type="checkbox" id="terms">
                                <label for="terms">
                                    Saya setuju dengan <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a>
                                </label>
                            </div>

                            <button type="submit" class="btn-submit">
                                Daftar Sekarang
                            </button>

                            <div class="login-link">
                                <p>
                                    Sudah punya akun? 
                                    <a href="login.php">Login di sini</a>
                                </p>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="../../assets/js/register.js"></script>
</body>
</html>