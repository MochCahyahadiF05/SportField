<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportField - Login</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
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
                                <span class="gradient-text">Selamat Datang</span>
                                <br>
                                <span class="normal-text">Kembali!</span>
                            </h2>
                            <p class="welcome-desc">
                                Login untuk mengakses akun Anda dan mulai booking lapangan olahraga favorit.
                            </p>
                            
                            <div class="features">
                                <div class="feature-card">
                                    <div class="feature-icon">⚡</div>
                                    <div class="feature-content">
                                        <h4 class="feature-title">Booking Cepat</h4>
                                        <p class="feature-desc">Proses booking hanya 30 detik</p>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="feature-icon">🏆</div>
                                    <div class="feature-content">
                                        <h4 class="feature-title">Lapangan Premium</h4>
                                        <p class="feature-desc">Fasilitas terbaik untuk Anda</p>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="feature-icon">🎁</div>
                                    <div class="feature-content">
                                        <h4 class="feature-title">Reward Points</h4>
                                        <p class="feature-desc">Dapatkan poin setiap booking</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="right-side">
                    <div class="login-card">
                        
                        <!-- Logo for mobile -->
                        <div class="mobile-logo">
                            <div class="logo-icon">
                                <span>SF</span>
                            </div>
                            <h1 class="logo-text">SportField</h1>
                        </div>

                        <div class="form-header">
                            <h3 class="form-title">Login</h3>
                            <p class="form-subtitle">Masuk ke akun Anda</p>
                        </div>

                        <div id="messageAlert"></div>

                        <form class="login-form" id="loginForm">
                            <div class="form-group">
                                <label>Email</label>
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <input type="email" name="email" id="email" class="form-input" placeholder="email@example.com" required>
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
                                    <input type="password" id="loginPassword" name="password" class="form-input" placeholder="••••••••" required>
                                    <button type="button" onclick="togglePassword('loginPassword')" class="toggle-password">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn-submit">
                                Login Sekarang
                            </button>

                            <div class="register-link">
                                <p>
                                    Belum punya akun? 
                                    <a href="register.php">Daftar Sekarang</a>
                                </p>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="../../assets/js/login.js"></script>