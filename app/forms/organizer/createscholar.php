<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

$user = $_SESSION['user'] ?? null;

if (!$user || $user['Role'] !== 'organizers') {
    header('Location: ' . url('/create'));
    exit;
}

$organizerName = $user['Username'] ?? 'Organizer';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Scholarship</title>
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">

    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
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
            background: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        /* ══════════════════════════════
           OVERLAY
        ══════════════════════════════ */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 90;
        }
        .overlay.show { display: block; }

        /* ══════════════════════════════
           SIDEBAR
        ══════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 260px; height: 100%;
            background: var(--green-dark);
            display: flex;
            flex-direction: column;
            padding: 28px 20px;
            transform: translateX(-100%);
            transition: transform 0.32s cubic-bezier(.4,0,.2,1);
            z-index: 100;
            box-shadow: 4px 0 32px rgba(0,0,0,0.18);
        }

        @media (hover: hover) and (min-width: 769px) {
            .sidebar:hover { transform: translateX(0); }
        }

        .sidebar.open { transform: translateX(0); }

        @media (min-width: 769px) {
            .sidebar::after {
                content: "☰";
                position: absolute;
                right: -44px;
                top: 50%;
                transform: translateY(-50%);
                background: var(--green-dark);
                color: white;
                width: 44px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 0 12px 12px 0;
                cursor: pointer;
                font-size: 18px;
                box-shadow: 4px 0 12px rgba(0,0,0,0.25);
            }
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 36px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .sidebar-logo-icon {
            width: 36px; height: 36px;
            background: var(--yellow-gold);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sidebar-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            color: white;
            letter-spacing: 0.02em;
        }

        .sidebar nav { display: flex; flex-direction: column; gap: 4px; }

        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar nav a:hover { background: rgba(255,255,255,0.1); color: white; }

        .sidebar nav a.active {
            background: rgba(250,204,21,0.18);
            color: var(--yellow-gold);
            font-weight: 600;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.12);
        }

        .logout-btn {
            display: block;
            text-align: center;
            background: var(--yellow-gold);
            color: #111;
            padding: 11px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            transition: opacity 0.2s, transform 0.15s;
        }

        .logout-btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* ══════════════════════════════
           MAIN WRAPPER
        ══════════════════════════════ */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
            overflow-x: hidden;
        }

        /* ══════════════════════════════
           TOP HEADER
        ══════════════════════════════ */
        .top-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 14px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        }

        .hamburger {
            display: none;
            background: none;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            color: var(--green-dark);
            padding: 6px 10px;
            flex-shrink: 0;
            line-height: 1;
            align-items: center;
        }

        .top-header-left { flex: 1; min-width: 0; }

        .top-header-left h1 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--green-deep);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .top-header-left p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .top-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .avatar-circle {
            width: 38px; height: 38px;
            min-width: 38px;
            border-radius: 10px;
            background: var(--green-accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            flex-shrink: 0;
        }

        .avatar-info { display: flex; flex-direction: column; }
        .avatar-info .name { font-size: 13px; font-weight: 600; color: var(--text-main); white-space: nowrap; }
        .avatar-info .role { font-size: 11px; color: var(--text-muted); }

        /* ══════════════════════════════
           PAGE BODY
        ══════════════════════════════ */
        .page-body {
            flex: 1;
            padding: 36px 40px;
            display: flex;
            justify-content: center;
        }

        .form-container { width: 100%; max-width: 640px; }

        /* ══════════════════════════════
           FORM CARD
        ══════════════════════════════ */
        .form-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-stripe {
            height: 4px;
            background: var(--green-dark);
        }

        .card-inner { padding: 32px; }

        .card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .card-header-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            background: rgba(64,145,108,0.1);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .card-header-title {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--green-deep);
            line-height: 1.3;
        }

        .card-header-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ══════════════════════════════
           FORM FIELDS
        ══════════════════════════════ */
        .form-group { display: flex; flex-direction: column; gap: 24px; }

        .field { display: flex; flex-direction: column; }

        .field label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .field input,
        .field textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: #f9fcfa;
            color: var(--text-main);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field input:focus,
        .field textarea:focus {
            outline: none;
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
            background: var(--bg-card);
        }

        .field input::placeholder,
        .field textarea::placeholder { color: var(--text-muted); }

        .field textarea { resize: none; }

        .field-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .form-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 4px 0;
        }

        /* ══════════════════════════════
           INFO PILL
        ══════════════════════════════ */
        .info-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(64,145,108,0.08);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: var(--green-mid);
        }

        /* ══════════════════════════════
           ACTION BUTTONS
        ══════════════════════════════ */
        .form-actions {
            display: flex;
            gap: 12px;
            padding-top: 4px;
        }

        .btn-cancel {
            flex: 1;
            text-align: center;
            border: 1px solid var(--border);
            color: var(--text-muted);
            padding: 11px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-cancel:hover { background: #f5faf7; }

        .btn-publish {
            flex: 1;
            background: var(--green-dark);
            color: white;
            border: none;
            padding: 11px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-publish:hover { background: var(--green-mid); transform: translateY(-1px); }

        /* ══════════════════════════════
           FOOTER
        ══════════════════════════════ */
        .page-footer {
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            padding: 16px;
            border-top: 1px solid var(--border);
            background: var(--bg-card);
        }

        /* ══════════════════════════════
           BREAKPOINTS
        ══════════════════════════════ */
        @media (max-width: 1024px) {
            .top-header { padding: 14px 24px; }
            .page-body  { padding: 28px 24px; }
        }

        @media (max-width: 768px) {
            .hamburger  { display: flex; }
            .top-header { padding: 12px 16px; }
            .page-body  { padding: 24px 16px; }
            .top-header-left h1 { font-size: 17px; }
        }

        @media (max-width: 480px) {
            .top-header-left h1 { font-size: 15px; }
            .top-header-left p  { font-size: 11px; }
            .page-body  { padding: 16px 12px; }
            .card-inner { padding: 20px; }
        }

        @media (max-width: 360px) {
            .top-header-left p { display: none; }
            .top-header-left h1 { font-size: 13px; }
        }
    </style>
</head>

<body>

    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🎓</div>
            <span class="sidebar-logo-text">Organizer Panel</span>
        </div>

        <nav>
            <a href="<?= url('organizer-dashboard') ?>">🏠 Dashboard</a>
            <a href="<?= url('create-scholarship-page') ?>" class="active">➕ Create Scholarship</a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= url('logout') ?>" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main-wrapper">

        <header class="top-header">
            <button class="hamburger" onclick="openSidebar()" aria-label="Open menu">☰</button>

            <div class="top-header-left">
                <h1>Create Scholarship ➕</h1>
                <p>Fill out the details to publish a new scholarship opportunity.</p>
            </div>

            <div class="top-header-right">
                <div class="avatar-circle"><?= strtoupper(substr($organizerName, 0, 1)) ?></div>
                <div class="avatar-info">
                    <span class="name"><?= esc($organizerName) ?></span>
                    <span class="role">Organizer</span>
                </div>
            </div>
        </header>

        <div class="page-body">
            <div class="form-container">

                <div class="form-card">
                    <div class="card-stripe"></div>
                    <div class="card-inner">

                        <div class="card-header">
                            <div class="card-header-icon">📋</div>
                            <div>
                                <div class="card-header-title">Scholarship Details</div>
                                <div class="card-header-sub">All fields are required</div>
                            </div>
                        </div>

                        <form method="POST" action="<?= url('create-scholarship') ?>">
                            <div class="form-group">

                                <!-- Scholarship Name -->
                                <div class="field">
                                    <label for="title">Scholarship Name</label>
                                    <input type="text" id="title" name="title" required
                                           placeholder="e.g. Academic Excellence Scholarship">
                                </div>

                                <!-- Description -->
                                <div class="field">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" required rows="5"
                                              placeholder="Describe eligibility, requirements, and scholarship details..."></textarea>
                                    <span class="field-hint">Be clear about who qualifies and what documents are needed.</span>
                                </div>

                                <!-- Deadline -->
                                <div class="field">
                                    <label for="deadline">📅 Application Deadline</label>
                                    <input type="date" id="deadline" name="deadline" required>
                                    <span class="field-hint">Students will not be able to apply after this date.</span>
                                </div>

                                <hr class="form-divider">

                                <!-- Info pill -->
                                <div class="info-pill">
                                    <span>ℹ️</span>
                                    <span>This scholarship will be <strong>immediately visible</strong> to all students once published.</span>
                                </div>

                                <!-- Actions -->
                                <div class="form-actions">
                                    <a href="<?= url('organizer-dashboard') ?>" class="btn-cancel">← Cancel</a>
                                    <button type="submit" class="btn-publish">Publish Scholarship</button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>

        <footer class="page-footer">
            © <?= date('Y') ?> Scholarship Portal — Organizer Dashboard
        </footer>

    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('overlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('show');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
    </script>

</body>
</html>