<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header('Location: ' . url('create'));
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">

    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        :root {
            --green-deep:    #1a3a2a;
            --green-dark:    #1f4432;
            --green-mid:     #2d6a4f;
            --green-accent:  #40916c;
            --yellow-gold:   #facc15;
            --bg-page:       #eef3f0;
            --bg-card:       #ffffff;
            --text-main:     #1a2e22;
            --text-muted:    #6b7c6f;
            --border:        #d8e6de;
            --sidebar-width: 260px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width); height: 100%;
            background: var(--green-dark);
            display: flex;
            flex-direction: column;
            padding: 28px 20px;
            transform: translateX(-100%);
            transition: transform 0.32s cubic-bezier(.4,0,.2,1);
            z-index: 50;
            box-shadow: 4px 0 32px rgba(0,0,0,0.18);
        }

        .sidebar:hover {
            transform: translateX(0);
        }

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
        }

        .sidebar-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            color: white;
            letter-spacing: 0.02em;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

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
            letter-spacing: 0.01em;
        }

        .sidebar nav a .nav-icon {
            width: 22px;
            text-align: center;
            font-size: 16px;
            opacity: 0.85;
        }

        .sidebar nav a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

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
            letter-spacing: 0.02em;
            transition: opacity 0.2s, transform 0.15s;
        }

        .logout-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* ── MAIN ── */
        .main-area {
            flex: 1;
            padding: 40px 48px 60px;
            max-width: 100%;
            margin: 0 auto;
        }

        /* ── CARDS ── */
        .info-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 24px 28px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(31,68,50,0.1);
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--green-accent);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .card-icon {
            width: 36px; height: 36px;
            background: #d4e9dd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--green-deep);
        }

        .card-subtitle {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-top: 8px;
        }

        .info-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            color: #111827;
        }

        .info-value.empty {
            color: #9ca3af;
            font-style: italic;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            color: var(--green-dark);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            transition: background 0.15s;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #f5faf7;
        }

        @media (max-width: 768px) {
            .main-area {
                padding: 20px 16px 40px;
            }
            .info-card {
                padding: 18px 20px;
            }
            .info-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>
</head>

<body>
<div style="display:flex; min-height:100vh;">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🎓</div>
            <span class="sidebar-logo-text">Admin Panel</span>
        </div>

        <nav>
            <a href="<?= url('admin-dashboard') ?>">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="<?= url('admin-students') ?>">
                <span class="nav-icon">👨‍🎓</span> Students
            </a>
            <a href="<?= url('admin-organizers') ?>">
                <span class="nav-icon">🏢</span> Organizers
            </a>
            <a href="<?= url('admin-scholarships') ?>">
                <span class="nav-icon">📖</span> Scholarships
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= url('logout') ?>" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-area">

        <!-- PAGE HEADER -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-family:'Playfair Display',serif; font-size:28px; font-weight:700; color:#1f4432; margin:0 0 4px;">Student Details</h1>
                <p style="color:#6b7280; font-size:14px; margin:0;">View complete student information</p>
            </div>
            <a href="<?= url('admin-students') ?>" class="btn-back">⬅ Back to Students</a>
        </div>

        <!-- ACCOUNT INFO CARD -->
        <div class="info-card">
            <div class="card-header">
                <div class="card-icon">👤</div>
                <div>
                    <div class="card-title">Account Information</div>
                    <div class="card-subtitle">Login credentials & account status</div>
                </div>
            </div>

            <?php if (!empty($studentLogin)): ?>
            <div class="info-row">
                <div>
                    <div class="info-label">Username</div>
                    <div class="info-value"><?= esc($studentLogin[0]['Username'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= esc($studentLogin[0]['Email'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge" style="display:inline-block; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; background:<?= ($studentLogin[0]['Status'] ?? '') === 'accepted' ? '#d1fae5' : '#fee2e2' ?>; color:<?= ($studentLogin[0]['Status'] ?? '') === 'accepted' ? '#065f46' : '#991b1b' ?>;">
                            <?= esc(ucfirst($studentLogin[0]['Status'] ?? 'N/A')) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <p class="info-value empty">No account information found.</p>
            <?php endif; ?>
        </div>

        <!-- PERSONAL INFO CARD -->
        <div class="info-card">
            <div class="card-header">
                <div class="card-icon">📋</div>
                <div>
                    <div class="card-title">Personal Information</div>
                    <div class="card-subtitle">Student's personal details</div>
                </div>
            </div>

            <?php if ($profile): ?>
            <div class="info-row">
                <div>
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= esc($profile['FirstName'] ?? '') ?> <?= esc($profile['MiddleName'] ?? '') ?> <?= esc($profile['LastName'] ?? '') ?> <?= esc($profile['Suffix'] ?? '') ?></div>
                </div>
                <div>
                    <div class="info-label">Gender</div>
                    <div class="info-value"><?= esc($profile['Gender'] ?? 'Not provided') ?></div>
                </div>
                <div>
                    <div class="info-label">Birthdate</div>
                    <div class="info-value"><?= esc($profile['Birthdate'] ?? 'Not provided') ?></div>
                </div>
                <div>
                    <div class="info-label">Contact Number</div>
                    <div class="info-value"><?= esc($profile['ContactNumber'] ?? 'Not provided') ?></div>
                </div>
                <div>
                    <div class="info-label">Address</div>
                    <div class="info-value">
                        <?php 
                        $address = implode(', ', array_filter([
                            $profile['Barangay'] ?? '',
                            $profile['City'] ?? '',
                            $profile['Province'] ?? '',
                            $profile['Country'] ?? ''
                        ]));
                        echo !empty($address) ? esc($address) : 'Not provided';
                        ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <p class="info-value empty">No personal information available.</p>
            <?php endif; ?>
        </div>

        <!-- EDUCATION CARD -->
        <div class="info-card">
            <div class="card-header">
                <div class="card-icon">🎓</div>
                <div>
                    <div class="card-title">Education Information</div>
                    <div class="card-subtitle">Academic background</div>
                </div>
            </div>

            <?php if ($education): ?>
            <div class="info-row">
                <div>
                    <div class="info-label">School</div>
                    <div class="info-value"><?= esc($education['SchoolName'] ?? 'Not provided') ?></div>
                </div>
                <div>
                    <div class="info-label">Course / Strand</div>
                    <div class="info-value"><?= esc($education['CourseStrand'] ?? 'Not provided') ?></div>
                </div>
                <div>
                    <div class="info-label">Year Level</div>
                    <div class="info-value"><?= esc($education['YearLevel'] ?? 'Not provided') ?></div>
                </div>
                <div>
                    <div class="info-label">School Year</div>
                    <div class="info-value"><?= esc($education['SchoolYear'] ?? 'Not provided') ?></div>
                </div>
                <div>
                    <div class="info-label">Semester</div>
                    <div class="info-value"><?= esc($education['Semester'] ?? 'Not provided') ?></div>
                </div>
                <div>
                    <div class="info-label">Status</div>
                    <div class="info-value"><?= esc($education['Status'] ?? 'Not provided') ?></div>
                </div>
            </div>
            <?php else: ?>
                <p class="info-value empty">No education information available.</p>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>