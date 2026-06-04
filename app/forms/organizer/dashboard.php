<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

$user = $_SESSION['user'] ?? null;

if (!$user || $user['Role'] !== 'organizers') {
    header('Location: ' . url('/create'));
    exit;
}

$today = date('Y-m-d');
$organizerName = $user['Username'] ?? 'Organizer';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard</title>
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

        .top-header-left {
            flex: 1;
            min-width: 0;
        }

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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .top-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-create {
            background: var(--green-dark);
            color: white;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-create:hover { background: var(--green-mid); transform: translateY(-1px); }

        .avatar-circle {
            width: 38px; height: 38px;
            min-width: 38px; min-height: 38px;
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

        .avatar-info .name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            white-space: nowrap;
        }

        .avatar-info .role { font-size: 11px; color: var(--text-muted); }

        /* ══════════════════════════════
           PAGE BODY
        ══════════════════════════════ */
        .page-body { flex: 1; padding: 36px 40px; }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 8px;
        }

        .section-title { font-size: 17px; font-weight: 700; color: var(--green-deep); }

        .count-pill {
            background: var(--green-dark);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 99px;
            white-space: nowrap;
        }

        /* ══════════════════════════════
           GRID
        ══════════════════════════════ */
        .scholarship-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        /* ══════════════════════════════
           CARDS
        ══════════════════════════════ */
        .schol-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .schol-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(31,68,50,0.1);
        }

        .card-stripe        { height: 4px; }
        .card-stripe.active  { background: var(--green-dark); }
        .card-stripe.expired { background: #f87171; }

        .card-body { padding: 20px; display: flex; flex-direction: column; flex: 1; }

        .card-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .card-title { font-size: 14px; font-weight: 700; color: var(--green-deep); line-height: 1.35; }

        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 99px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .status-badge.active  { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .status-badge.expired { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        .card-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.55;
            flex: 1;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-deadline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f5faf7;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .card-deadline strong { color: var(--text-main); }

        .btn-manage {
            display: block;
            text-align: center;
            background: var(--green-dark);
            color: white;
            padding: 10px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-manage:hover { background: var(--green-mid); transform: translateY(-1px); }

        /* ══════════════════════════════
           EMPTY STATE
        ══════════════════════════════ */
        .empty-state {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 56px 24px;
            text-align: center;
        }

        .empty-state .icon  { font-size: 40px; margin-bottom: 12px; }
        .empty-state .label { font-size: 15px; font-weight: 600; color: var(--text-main); }
        .empty-state .hint  { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

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

        /* Large tablet / small laptop ≤1280px */
        @media (max-width: 1280px) {
            .scholarship-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* Tablet landscape ≤1024px */
        @media (max-width: 1024px) {
            .top-header { padding: 14px 24px; }
            .page-body  { padding: 28px 24px; }
        }

        /* Tablet portrait ≤768px */
        @media (max-width: 768px) {
            .hamburger  { display: flex; }
            .top-header { padding: 12px 16px; }
            .page-body  { padding: 24px 16px; }
            .scholarship-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
            .top-header-left h1 { font-size: 17px; }
        }

        /* Large phone ≤600px */
        @media (max-width: 600px) {
            .scholarship-grid { grid-template-columns: 1fr; }
        }

        /* Small phone ≤480px */
        @media (max-width: 480px) {
            .top-header-left h1 { font-size: 15px; }
            .top-header-left p  { font-size: 11px; }
            .btn-create { padding: 8px 12px; font-size: 12px; }
            .page-body  { padding: 16px 12px; }
        }

        /* Extra small ≤360px */
        @media (max-width: 360px) {
            .top-header-left p { display: none; }
            .top-header-left h1 { font-size: 13px; }
            .btn-create { padding: 7px 10px; font-size: 11px; }
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
            <a href="<?= url('organizer-dashboard') ?>" class="active">🏠 Dashboard</a>
            <a href="<?= url('create-scholarship-page') ?>">➕ Create Scholarship</a>
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
                <h1>Welcome back, <?= esc($organizerName) ?> 👋</h1>
                <p>Manage your scholarships and review applicants.</p>
            </div>

            <div class="top-header-right">
                <a href="<?= url('create-scholarship-page') ?>" class="btn-create">+ Create Scholarship</a>
                <div class="avatar-circle"><?= strtoupper(substr($organizerName, 0, 1)) ?></div>
                <div class="avatar-info">
                    <span class="name"><?= esc($organizerName) ?></span>
                    <span class="role">Organizer</span>
                </div>
            </div>
        </header>

        <div class="page-body">

            <div class="section-header">
                <span class="section-title">📋 Your Scholarships</span>
                <?php if (!empty($scholarships)): ?>
                    <span class="count-pill"><?= count($scholarships) ?> Total</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($scholarships)): ?>

                <div class="scholarship-grid">
                    <?php foreach ($scholarships as $s): ?>
                        <?php $isExpired = ($today > $s['ExpiryDate']); ?>

                        <div class="schol-card">
                            <div class="card-stripe <?= $isExpired ? 'expired' : 'active' ?>"></div>
                            <div class="card-body">

                                <div class="card-title-row">
                                    <span class="card-title"><?= esc($s['ScholarshipName']) ?></span>
                                    <span class="status-badge <?= $isExpired ? 'expired' : 'active' ?>">
                                        <?= $isExpired ? '⛔ Expired' : '✅ Active' ?>
                                    </span>
                                </div>

                                <p class="card-desc"><?= esc($s['Description']) ?></p>

                                <div class="card-deadline">
                                    <span>📅 Deadline</span>
                                    <strong><?= esc($s['ExpiryDate']) ?></strong>
                                </div>

                                <a href="<?= url('view-applicants/' . $s['ScholarshipID']) ?>" class="btn-manage">
                                    Manage Scholarship
                                </a>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>

                <div class="empty-state">
                    <div class="icon">📭</div>
                    <div class="label">No scholarships created yet.</div>
                    <div class="hint">Click <strong>+ Create Scholarship</strong> to get started.</div>
                </div>

            <?php endif; ?>

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