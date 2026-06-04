<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/CSSngCREATE.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        :root {
            --green-deep:   #1a3a2a;
            --green-dark:   #1f4432;
            --green-mid:    #2d6a4f;
            --green-accent: #40916c;
            --yellow-gold:  #facc15;
            --bg-page:      #eef3f0;
            --bg-card:      #ffffff;
            --text-main:    #1a2e22;
            --text-muted:   #6b7c6f;
            --border:       #d8e6de;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2b20 0%, #163d2c 50%, #1f4432 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* ── CARD ── */
        .login-card {
            width: 100%;
            max-width: 900px;
            background: var(--bg-card);
            border-radius: 20px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.3);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            background: var(--green-dark);
            padding: 52px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel-glow {
            position: absolute;
            width: 320px; height: 320px;
            background: rgba(250,204,21,0.15);
            filter: blur(80px);
            border-radius: 50%;
            bottom: -80px; left: -80px;
            pointer-events: none;
        }

        .left-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .left-logo-icon {
            width: 38px; height: 38px;
            background: var(--yellow-gold);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .left-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            color: white;
            letter-spacing: 0.02em;
        }

        .left-panel h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: white;
            line-height: 1.2;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .left-panel p {
            font-size: 15px;
            color: rgba(255,255,255,0.7);
            line-height: 1.65;
            position: relative;
            z-index: 1;
        }

        /* Feature list */
        .feature-list {
            margin-top: 36px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: rgba(255,255,255,0.75);
        }

        .feature-dot {
            width: 7px; height: 7px;
            background: var(--yellow-gold);
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            padding: 48px 40px;
            position: relative;
            background: var(--bg-card);
            overflow-y: auto;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 28px;
            font-weight: 500;
            transition: color 0.15s;
        }

        .btn-back:hover { color: var(--green-dark); }

        .right-panel h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--green-deep);
            margin-bottom: 6px;
        }

        .right-panel .subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        /* ── FLASH MESSAGES ── */
        .flash-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .flash-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #15803d;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        /* ── FORM ── */
        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: #f9fcfa;
            color: var(--text-main);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
            background: var(--bg-card);
        }

        .form-group input::placeholder { color: var(--text-muted); }

        /* Password wrapper */
        .password-wrap { position: relative; }

        .password-wrap input { padding-right: 60px; }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: color 0.15s;
        }

        .toggle-pw:hover { color: var(--green-dark); }

        /* ── LOGIN BUTTON ── */
        .btn-login {
            width: 100%;
            background: var(--yellow-gold);
            color: #111;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.12s, box-shadow 0.15s;
            box-shadow: 0 4px 14px rgba(250,204,21,0.35);
            margin-top: 4px;
        }

        .btn-login:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(250,204,21,0.4);
        }

        .btn-login:active { transform: translateY(0); }

        /* ── DIVIDER ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }

        .divider-line { flex: 1; height: 1px; background: var(--border); }

        .divider-text { font-size: 12px; color: var(--text-muted); font-weight: 500; }

        /* ── REGISTER SECTION ── */
        .register-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 12px;
        }

        .register-btns { display: flex; flex-direction: column; gap: 10px; }

        /* Slide-fill button base */
        .btn-register {
            display: block;
            width: 100%;
            text-align: center;
            padding: 11px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: color 0.3s;
        }

        .btn-register span {
            position: relative;
            z-index: 1;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            inset: 0;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .btn-register:hover::before { transform: scaleX(1); }

        /* Student */
        .btn-register.student {
            border: 1.5px solid var(--green-dark);
            color: var(--green-dark);
        }

        .btn-register.student::before { background: var(--green-dark); }
        .btn-register.student:hover { color: white; }

        /* Organizer */
        .btn-register.organizer {
            border: 1.5px solid var(--yellow-gold);
            color: #9a6f00;
        }

        .btn-register.organizer::before { background: var(--yellow-gold); }
        .btn-register.organizer:hover { color: #111; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .login-card { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 36px 24px; }
        }

        @media (max-width: 420px) {
            body { padding: 12px; }
            .right-panel { padding: 28px 20px; }
            .right-panel h2 { font-size: 22px; }
        }
    </style>
    
</head>

<body>

    <div class="login-card">

        <!-- LEFT PANEL -->
        <div class="left-panel">
            <div class="left-logo">
                
            </div>

            <h2>Welcome Back 👋</h2>
            <p>Continue your journey and access your scholarship dashboard with ease.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    Browse available scholarships
                </div>
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    Track your applications
                </div>
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    Manage and post opportunities
                </div>
            </div>

            <div class="left-panel-glow"></div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">

            <a href="<?= url('/') ?>" class="btn-back">← Back</a>

            <h2>Login Account</h2>
            <p class="subtitle">Please enter your credentials to continue</p>

            <!-- SUCCESS MESSAGE (for new registrations) -->
            <?php if ($success = get_flash('success')): ?>
                <div class="flash-success">✓ <?= $success ?></div>
            <?php endif; ?>

            <!-- ERROR MESSAGE -->
            <?php if ($error = get_flash('error')): ?>
                <div class="flash-error">⚠️ <?= $error ?></div>
            <?php endif; ?>

            <!-- FORM -->
            <form action="<?= url('login') ?>" method="POST">
                <?php csrf_field(); ?>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrap">
                        <input type="password" name="password" id="password" required placeholder="Enter your password">
                        <button type="button" class="toggle-pw" onclick="togglePassword()">Show</button>
                    </div>
                </div>

                <button type="submit" class="btn-login">Login</button>

            </form>

            <!-- DIVIDER -->
            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">OR</span>
                <div class="divider-line"></div>
            </div>

            <!-- REGISTER -->
            <p class="register-label">Don't have an account?</p>

            <div class="register-btns">
                <a href="<?= url('account') ?>?role=student" class="btn-register student">
                    <span>Register as Student</span>
                </a>
                <a href="<?= url('account') ?>?role=organizers" class="btn-register organizer">
                    <span>Register as Organizer</span>
                </a>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const btn = input.nextElementSibling;
            const isHidden = input.type === "password";
            input.type = isHidden ? "text" : "password";
            btn.textContent = isHidden ? "Hide" : "Show";
        }
    </script>

</body>
</html>