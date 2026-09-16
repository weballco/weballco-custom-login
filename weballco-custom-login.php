<?php
/**
 * Plugin Name: Custom Glassmorphism Login for WebAllCo
 * Description: جایگزینی صفحه ورود پیش‌فرض وردپرس با طراحی مدرن و شیشه‌ای اختصاصی وب‌آل‌کو
 * Author: WebAllCo Team
 * Version: 1.0
 */

// ۱. ریدایرکت صفحه ورود پیش‌فرض به صفحه دلخواه یا مدیریت قالب لاگین
function weballco_custom_login_init() {
    global $pagenow;
    
    // اگر کاربر درخواست خروج داده باشد
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        check_admin_referer('log-out');
        $user = wp_get_current_user();
        wp_logout();
        
        // بازگشت به صفحه لاگین پس از خروج موفقیت‌آمیز همراه با پارامتر پیام
        $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : home_url();
        wp_safe_redirect(add_query_arg('logged_out', 'true', $redirect_to));
        exit;
    }
}
add_action('init', 'weballco_custom_login_init');

// ۲. جایگزینی کامل HTML صفحه ورود وردپرس
function weballco_render_custom_login_page() {
    // اگر کاربر لاگین کرده باشد و درخواست خروج یا پنل نداشته باشد، به صفحه اصلی یا پنل هدایت شود
    if (is_user_logged_in() && !isset($_GET['action'])) {
        return;
    }

    // گرفتن خطاهای ورود وردپرس
    $error_message = '';
    if (isset($_GET['login']) && $_GET['login'] == 'failed') {
        $error_message = 'اطلاعات وارد شده نامعتبر است. لطفاً دوباره تلاش کنید.';
    } elseif (isset($_GET['logged_out']) && $_GET['logged_out'] == 'true') {
        $error_message = 'با موفقیت از حساب کاربری خارج شدید.';
    }

    $login_url = wp_login_url();
    $lostpassword_url = wp_lostpassword_url();
    $home_url = home_url();
    
    status_header(200);
    ?>
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ورود — WebAllCo</title>
        <style>
            @font-face {
                font-family: 'Vazirmatn';
                src: url('<?php echo get_template_directory_uri(); ?>/assets/fonts/Vazirmatn-Medium.woff2') format('woff2');
                font-weight: 500;
                font-style: normal;
                font-display: swap;
            }

            :root {
                --bg-color: #050508;
                --card-bg: rgba(18, 18, 26, 0.65);
                --card-border: rgba(255, 255, 255, 0.08);
                --card-border-glow: rgba(99, 102, 241, 0.3);
                --text-main: #f8fafc;
                --text-muted: #94a3b8;
                --primary: #6366f1;
                --primary-hover: #4f46e5;
                --accent-glow: rgba(99, 102, 241, 0.15);
                --purple-glow: rgba(168, 85, 247, 0.15);
                --input-bg: rgba(255, 255, 255, 0.03);
                --input-border: rgba(255, 255, 255, 0.08);
                --input-border-focus: #6366f1;
                --radius-lg: 24px;
                --radius-md: 12px;
                --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }

            body {
                background-color: var(--bg-color);
                color: var(--text-main);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                position: relative;
            }

            .bg-mesh {
                position: absolute;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background-image: 
                    radial-gradient(circle at 15% 20%, var(--accent-glow) 0%, transparent 40%),
                    radial-gradient(circle at 85% 80%, var(--purple-glow) 0%, transparent 40%);
                z-index: -2;
                pointer-events: none;
            }

            .bg-grid {
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                background-size: 40px 40px;
                background-image: 
                    linear-gradient(to right, rgba(255, 255, 255, 0.015) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
                z-index: -1;
                pointer-events: none;
            }

            .particles {
                position: absolute; width: 100%; height: 100%; top: 0; left: 0; overflow: hidden; z-index: -1; pointer-events: none;
            }

            .particle {
                position: absolute; background: rgba(255, 255, 255, 0.15); border-radius: 50%; animation: floatParticle linear infinite;
            }

            @keyframes floatParticle {
                0% { transform: translateY(105vh) translateX(0); opacity: 0; }
                20%, 80% { opacity: 0.6; }
                100% { transform: translateY(-5vh) translateX(50px); opacity: 0; }
            }

            .login-card {
                width: 900px; max-width: 90vw; height: 580px;
                background: var(--card-bg);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid var(--card-border);
                border-radius: var(--radius-lg);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 40px var(--card-border-glow);
                display: flex; overflow: hidden; position: relative;
                animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            @keyframes cardEntrance {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .login-form-col {
                flex: 1; padding: 48px; display: flex; flex-direction: column; justify-content: center; z-index: 2; background: rgba(12, 12, 18, 0.4); position: relative;
            }

            .form-header { margin-bottom: 24px; }
            .form-header h1 { font-size: 26px; font-weight: 500; color: var(--text-main); margin-bottom: 8px; }
            .form-header p { color: var(--text-muted); font-size: 13px; }

            .error-notice {
                background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; padding: 10px 14px; border-radius: var(--radius-md); font-size: 12px; margin-bottom: 16px; text-align: right;
            }

            .input-group { margin-bottom: 16px; position: relative; }
            .input-group label { display: block; font-size: 11px; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; }
            .input-wrapper { position: relative; display: flex; align-items: center; }
            .input-wrapper svg { position: absolute; right: 14px; width: 18px; height: 18px; stroke: var(--text-muted); pointer-events: none; }
            
            .input-field {
                width: 100%; background: var(--input-bg); border: 1px solid var(--input-border); border-radius: var(--radius-md); padding: 12px 44px 12px 16px; color: var(--text-main); font-size: 14px; outline: none; transition: var(--transition); direction: ltr; text-align: right;
            }
            .input-field::placeholder { text-align: right; color: rgba(255,255,255,0.2); }
            .input-field:focus { border-color: var(--input-border-focus); background: rgba(255, 255, 255, 0.05); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); }

            .password-toggle {
                position: absolute; left: 14px; background: none; border: none; cursor: pointer; display: flex; align-items: center; padding: 0;
            }
            .password-toggle svg { position: static; stroke: var(--text-muted); }

            .form-actions { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: 12px; }
            .checkbox-container { display: flex; align-items: center; cursor: pointer; color: var(--text-muted); user-select: none; }
            .checkbox-container input { position: absolute; opacity: 0; cursor: pointer; }
            .checkmark { width: 16px; height: 16px; background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 4px; margin-left: 8px; display: flex; align-items: center; justify-content: center; transition: var(--transition); }
            .checkbox-container input:checked ~ .checkmark { background: var(--primary); border-color: var(--primary); }
            .checkmark::after { content: ""; width: 4px; height: 8px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); display: none; }
            .checkbox-container input:checked ~ .checkmark::after { display: block; }

            .forgot-link { color: var(--text-muted); text-decoration: none; transition: var(--transition); }
            .forgot-link:hover { color: var(--text-main); }

            .submit-btn {
                width: 100%; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; border: none; border-radius: var(--radius-md); padding: 12px; font-size: 14px; font-weight: 500; cursor: pointer; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4); transition: var(--transition);
            }
            .submit-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6); }

            .form-footer-text { margin-top: 20px; text-align: center; font-size: 11px; color: var(--text-muted); }

            .brand-showcase-col {
                flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; background: radial-gradient(circle at center, rgba(30, 27, 75, 0.2) 0%, rgba(5, 5, 8, 0.6) 100%); overflow: hidden; border-right: 1px solid var(--card-border);
            }

            .shape { position: absolute; border-radius: 50%; filter: blur(40px); z-index: 1; pointer-events: none; }
            .shape-1 { width: 180px; height: 180px; background: rgba(99, 102, 241, 0.25); top: 20%; right: 20%; }
            .shape-2 { width: 220px; height: 220px; background: rgba(168, 85, 247, 0.2); bottom: 20%; left: 20%; }

            .logo-container { 
                position: relative; 
                z-index: 2; 
                display: flex; 
                flex-direction: column; 
                align-items: center; 
                text-decoration: none;
            }
            .logo-glow { 
                position: absolute; 
                width: 180px; 
                height: 180px; 
                background: var(--primary); 
                border-radius: 50%; 
                filter: blur(40px); 
                opacity: 0.6; 
                z-index: -1; 
            }
            
            /* استایل لوگو */
            .logo-image {
                width: 90px;
                height: 90px;
                object-fit: contain;
                margin-bottom: 12px;
                border-radius: 16px;
                background: rgba(255,255,255,0.05);
                padding: 8px;
                border: 1px solid rgba(255,255,255,0.05);
                transition: transform 0.3s ease;
            }
            .logo-image:hover {
                transform: scale(1.05);
            }
            
            .brand-logo-text { 
                font-size: 32px; 
                font-weight: 700; 
                color: var(--text-main); 
                background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ffffff 100%); 
                -webkit-background-clip: text; 
                -webkit-text-fill-color: transparent; 
                direction: ltr; 
                letter-spacing: 1px;
                margin-bottom: 4px;
            }
            .brand-subtext { 
                font-size: 13px; 
                font-weight: 400; 
                letter-spacing: 1px; 
                color: var(--text-muted);
                background: none;
                -webkit-text-fill-color: var(--text-muted);
            }
            
            /* لینک سایت در فوتر */
            .site-link {
                color: var(--text-muted);
                text-decoration: none;
                transition: color 0.3s ease;
                font-size: 12px;
            }
            .site-link:hover {
                color: var(--text-main);
            }

            @media (max-width: 768px) {
                .login-card { flex-direction: column; height: auto; width: 92vw; max-width: 420px; }
                .brand-showcase-col { display: none; }
                .login-form-col { padding: 32px 24px; }
            }
        </style>
    </head>
    <body>
        <div class="bg-mesh" id="bgMesh"></div>
        <div class="bg-grid"></div>
        <div class="particles" id="particlesContainer"></div>

        <div class="login-card">
            <div class="login-form-col">
                <div class="form-header">
                    <h1>به پنل وردپرس خوش آمدید</h1>
                    <p>لطفاً برای ورود به حساب کاربری اطلاعات را وارد کنید</p>
                </div>

                <?php if (!empty($error_message)) : ?>
                    <div class="error-notice"><?php echo esc_html($error_message); ?></div>
                <?php endif; ?>

                <form name="loginform" id="loginForm" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post">
                    
                    <div class="input-group">
                        <label for="user_login">نام کاربری یا پست الکترونیک</label>
                        <div class="input-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <input type="text" name="log" id="user_login" class="input-field" placeholder="weballco@" required autocomplete="username">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="user_pass">گذرواژه</label>
                        <div class="input-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <input type="password" name="pwd" id="user_pass" class="input-field" placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="password-toggle" id="togglePassword" aria-label="نمایش/پنهان کردن گذرواژه">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" width="18" height="18">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <label class="checkbox-container">
                            <input type="checkbox" name="rememberme" id="rememberme" value="forever">
                            <span class="checkmark"></span>
                            مرا به خاطر بسپار
                        </label>
                        <a href="<?php echo esc_url($lostpassword_url); ?>" class="forgot-link">فراموشی گذرواژه؟</a>
                    </div>

                    <input type="hidden" name="redirect_to" value="<?php echo esc_url(admin_url()); ?>">
                    <button type="submit" name="wp-submit" id="submitBtn" class="submit-btn">ورود به حساب</button>
                </form>

                <div class="form-footer-text">
                    <a href="https://WebAllCo.ir" target="_blank" class="site-link">WebAllCo.ir</a>
                    &nbsp;•&nbsp;
                    تمامی حقوق محفوظ است &copy; WebAllCo
                </div>
            </div>

            <div class="brand-showcase-col">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
               <div class="logo-glow"></div>
                    <!-- لوگوی شما با آدرس مستقیم -->
                    <img src="https://countriessaga.ir/wp-content/uploads/2026/08/Picsart_26-07-21_00-43-25-231.png" 
                         alt="WebAllCo Logo" 
                         class="logo-image"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="brand-logo-text">WebAllCo</div>
                    <div class="brand-subtext">طراحی سایت حرفه‌ای</div>
                    <a href="https://WebAllCo.ir" target="_blank" class="logo-container">
          
                </a>
            </div>
        </div>

        <script>
            // مدیریت نمایش/پنهان کردن رمز عبور
            const passwordInput = document.getElementById('user_pass');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            const eyeOpenSVG = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
            const eyeClosedSVG = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243l4.242 4.242z" />`;

            togglePasswordBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.innerHTML = type === 'password' ? eyeOpenSVG : eyeClosedSVG;
            });

            // ذرات معلق (Particles)
            const particlesContainer = document.getElementById('particlesContainer');
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                particle.style.width = `${Math.random() * 3 + 1}px`;
                particle.style.height = particle.style.width;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.animationDuration = `${Math.random() * 10 + 8}s`;
                particle.style.animationDelay = `${Math.random() * 5}s`;
                particlesContainer.appendChild(particle);
            }
        </script>
    </body>
    </html>
    <?php
    exit;
}
add_action('login_head', 'weballco_render_custom_login_page', 1);

// ۳. رفع خطای ریدایرکت ناموفق وردپرس پس از ورود اشتباه
function weballco_login_failed($username) {
    $referrer = wp_get_referer();
    if ($referrer && !strstr($referrer, 'wp-login.php') && !strstr($referrer, 'wp-admin')) {
        wp_safe_redirect(add_query_arg('login', 'failed', $referrer));
        exit;
    }
    wp_safe_redirect(site_url('wp-login.php?login=failed', 'login_post'));
    exit;
}
add_action('wp_login_failed', 'weballco_login_failed');