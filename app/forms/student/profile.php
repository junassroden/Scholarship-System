<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'student') {
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
    <title>My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">

    <style>
        :root {
            --green-deep:    #1a3a2a;
            --green-dark:    #1f4432;
            --green-mid:     #2d6a4f;
            --green-accent:  #40916c;
            --green-light:   #74c69d;
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

        /* Pull-tab */
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

        .sidebar nav a.active .nav-icon {
            opacity: 1;
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

        /* ── AVATAR ── */
        .avatar {
            width: 68px; height: 68px;
            border-radius: 50%;
            background: #d4e9dd;
            color: #1f4432;
            font-size: 24px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            border: 3px solid #1f4432;
        }

        /* ── FIELD LABELS ── */
        .field-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7280;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 14px;
            color: #111827;
        }
        .field-value.empty {
            color: #9ca3af;
            font-style: italic;
        }

        /* ── COMPLETION BAR ── */
        .completion-bar-bg {
            background: #e5e7eb;
            border-radius: 99px;
            height: 7px;
            overflow: hidden;
        }
        .completion-bar-fill {
            background: #1f4432;
            border-radius: 99px;
            height: 100%;
            transition: width 0.6s ease;
        }

        /* ── SECTION HEADER ── */
        .section-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
        }
        .section-header-icon {
            width: 36px; height: 36px;
            background: #d4e9dd;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        /* ── STATUS BADGE ── */
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-enrolled  { background: #d1fae5; color: #065f46; }
        .status-graduated { background: #dbeafe; color: #1e40af; }
        .status-irregular { background: #fef3c7; color: #92400e; }
        .status-transferee{ background: #ede9fe; color: #5b21b6; }

        /* ── DRAWER STYLES ── */
        .overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 40;
            backdrop-filter: blur(2px);
        }
        .overlay.show { display: block; }

        .drawer {
            position: fixed;
            top: 0; right: -780px;
            width: 720px; max-width: 100vw;
            height: 100%;
            background: white;
            z-index: 50;
            transition: right 0.35s cubic-bezier(0.4,0,0.2,1);
            overflow-y: auto;
            box-shadow: -8px 0 32px rgba(0,0,0,0.12);
        }
        .drawer.open { right: 0; }

        .drawer-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 13px;
            color: #111827;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .drawer-input:focus {
            border-color: #1f4432;
            box-shadow: 0 0 0 3px rgba(31,68,50,0.12);
            background: white;
        }
        .drawer-section-title {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1f4432;
            margin: 20px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        .empty-text { color: #9ca3af; font-style: italic; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .main-area {
                padding: 28px 24px 48px;
            }
        }

        @media (max-width: 768px) {
            .main-area {
                padding: 20px 16px 40px;
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
            <span class="sidebar-logo-text">Student Panel</span>
        </div>

        <nav>
            <a href="<?= url('student-dashboard') ?>">
                <span class="nav-icon">📚</span> Scholarships
            </a>
            <a href="<?= url('student-profile') ?>" class="active">
                <span class="nav-icon">👤</span> My Profile
            </a>
            <a href="<?= url('applications') ?>">
                <span class="nav-icon">📄</span> My Applications
            </a>
            <a href="<?= url('application-history') ?>">
                <span class="nav-icon">📊</span> History
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= url('logout') ?>" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-area">

        <!-- PAGE HEADER -->
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; gap:16px; flex-wrap:wrap;">
            <div>
                <h1 style="font-family:'Playfair Display',serif; font-size:28px; font-weight:700; color:#1f4432; margin:0 0 4px;">My Profile</h1>
                <p style="color:#6b7280; font-size:14px; margin:0;">View and update your personal &amp; education details.</p>
            </div>
            <button onclick="openDrawer()" 
                style="background:#1f4432; color:white; padding:10px 22px; border-radius:12px; font-weight:600; font-size:14px; border:none; cursor:pointer; display:flex; align-items:center; gap:8px; white-space:nowrap; transition:background 0.15s, transform 0.12s;"
                onmouseover="this.style.background='#163324'" onmouseout="this.style.background='#1f4432'">
                ✏️ Update Profile
            </button>
        </div>

        <!-- FLASH MESSAGES -->
        <?php if ($success = get_flash('success')): ?>
            <div style="background:#d1fae5; color:#065f46; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-size:14px; border:1px solid #a7f3d0;">
                ✅ <?= esc($success) ?>
            </div>
        <?php endif; ?>
        <?php if ($error = get_flash('error')): ?>
            <div style="background:#fee2e2; color:#991b1b; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-size:14px; border:1px solid #fca5a5;">
                ❌ <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($profile): ?>

        <!-- PROFILE HERO CARD -->
        <div style="background:white; border-radius:20px; box-shadow:0 2px 12px rgba(0,0,0,0.07); padding:24px 28px; margin-bottom:20px;">
            <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                <?php
                    $first  = strtoupper(substr($profile['FirstName'] ?? '', 0, 1));
                    $last   = strtoupper(substr($profile['LastName']  ?? '', 0, 1));
                    $initials = ($first || $last) ? $first . $last : '?';
                ?>
                <div class="avatar"><?= esc($initials) ?></div>
                <div style="flex:1; min-width:180px;">
                    <?php
                        $fullname = trim(($profile['FirstName'] ?? '') . ' ' . ($profile['MiddleName'] ?? '') . ' ' . ($profile['LastName'] ?? '') . ' ' . ($profile['Suffix'] ?? ''));
                    ?>
                    <div style="font-size:20px; font-weight:800; color:#1f4432; line-height:1.2;">
                        <?= $fullname ? esc($fullname) : '<span class="empty-text">Name not set</span>' ?>
                    </div>
                    <div style="font-size:13px; color:#6b7280; margin-top:4px;">
                        <?= esc($user['Email'] ?? '') ?>
                    </div>
                    <?php if (!empty($education['Status'])): ?>
                        <?php 
                            $statusClass = match($education['Status']) { 
                                'Enrolled' => 'status-enrolled', 
                                'Graduated' => 'status-graduated', 
                                'Irregular' => 'status-irregular', 
                                'Transferee' => 'status-transferee', 
                                default => 'status-enrolled' 
                            }; 
                        ?>
                        <span class="status-badge <?= $statusClass ?>" style="margin-top:8px;">
                            <?= esc($education['Status']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <?php
                    $fields = [
                        $profile['FirstName'] ?? '', $profile['LastName'] ?? '',
                        $profile['Gender'] ?? '', $profile['Birthdate'] ?? '',
                        $profile['ContactNumber'] ?? '', $profile['City'] ?? '',
                        $education['SchoolName'] ?? '', $education['CourseStrand'] ?? '',
                        $education['YearLevel'] ?? '', $education['Status'] ?? '',
                    ];
                    $filled = count(array_filter($fields, fn($f) => trim($f) !== ''));
                    $pct = round($filled / count($fields) * 100);
                ?>
                <div style="min-width:160px; text-align:right;">
                    <div style="font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                        Profile Completion
                    </div>
                    <div style="font-size:22px; font-weight:800; color:#1f4432;"><?= $pct ?>%</div>
                    <div class="completion-bar-bg" style="margin-top:6px;">
                        <div class="completion-bar-fill" style="width:<?= $pct ?>%;"></div>
                    </div>
                    <?php if ($pct < 100): ?>
                        <div style="font-size:11px; color:#9ca3af; margin-top:4px;">Fill in more details ↑</div>
                    <?php else: ?>
                        <div style="font-size:11px; color:#059669; margin-top:4px;">All complete ✓</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- PERSONAL INFORMATION CARD -->
        <div style="background:white; border-radius:20px; box-shadow:0 2px 12px rgba(0,0,0,0.07); padding:24px 28px; margin-bottom:20px;">
            <div class="section-header">
                <div class="section-header-icon">👤</div>
                <div>
                    <div style="font-size:16px; font-weight:700; color:#1f4432;">Personal Information</div>
                    <div style="font-size:12px; color:#9ca3af;">Contact &amp; identity details</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:18px;">
                <div>
                    <div class="field-label">Gender</div>
                    <div class="field-value <?= empty($profile['Gender']) ? 'empty' : '' ?>">
                        <?= !empty($profile['Gender']) ? esc($profile['Gender']) : 'Not provided' ?>
                    </div>
                </div>
                <div>
                    <div class="field-label">Birthdate</div>
                    <div class="field-value <?= empty($profile['Birthdate']) ? 'empty' : '' ?>">
                        <?= !empty($profile['Birthdate']) ? esc($profile['Birthdate']) : 'Not provided' ?>
                    </div>
                </div>
                <div>
                    <div class="field-label">Contact Number</div>
                    <div class="field-value <?= empty($profile['ContactNumber']) ? 'empty' : '' ?>">
                        <?= !empty($profile['ContactNumber']) ? esc($profile['ContactNumber']) : 'Not provided' ?>
                    </div>
                </div>
                <div>
                    <div class="field-label">Email</div>
                    <div class="field-value <?= empty($user['Email']) ? 'empty' : '' ?>">
                        <?= !empty($user['Email']) ? esc($user['Email']) : 'Not provided' ?>
                    </div>
                </div>
                <?php 
                    $address = trim(implode(', ', array_filter([
                        $profile['Barangay'] ?? '',
                        $profile['City'] ?? '',
                        $profile['Province'] ?? '',
                        $profile['Country'] ?? '',
                    ]))); 
                    if (!empty($profile['PostalCode'])) $address .= ' ' . ($profile['PostalCode'] ?? '');
                    $address = trim($address, ', ');
                ?>
                <div style="grid-column: 1 / -1;">
                    <div class="field-label">Address</div>
                    <div class="field-value <?= empty($address) ? 'empty' : '' ?>">
                        <?= !empty($address) ? esc($address) : 'Not provided' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDUCATION DETAILS CARD -->
        <div style="background:white; border-radius:20px; box-shadow:0 2px 12px rgba(0,0,0,0.07); padding:24px 28px;">
            <div class="section-header">
                <div class="section-header-icon">🎓</div>
                <div>
                    <div style="font-size:16px; font-weight:700; color:#1f4432;">Education Details</div>
                    <div style="font-size:12px; color:#9ca3af;">School &amp; academic information</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:18px;">
                <div style="grid-column: 1 / -1;">
                    <div class="field-label">School</div>
                    <div class="field-value <?= empty($education['SchoolName']) ? 'empty' : '' ?>" style="font-size:15px; font-weight:600;">
                        <?= !empty($education['SchoolName']) ? esc($education['SchoolName']) : 'Not provided' ?>
                    </div>
                </div>
                <div>
                    <div class="field-label">Course / Strand</div>
                    <div class="field-value <?= empty($education['CourseStrand']) ? 'empty' : '' ?>">
                        <?= !empty($education['CourseStrand']) ? esc($education['CourseStrand']) : 'Not provided' ?>
                    </div>
                </div>
                <div>
                    <div class="field-label">Year Level</div>
                    <div class="field-value <?= empty($education['YearLevel']) ? 'empty' : '' ?>">
                        <?= !empty($education['YearLevel']) ? esc($education['YearLevel']) : 'Not provided' ?>
                    </div>
                </div>
                <div>
                    <div class="field-label">School Year</div>
                    <div class="field-value <?= empty($education['SchoolYear']) ? 'empty' : '' ?>">
                        <?= !empty($education['SchoolYear']) ? esc($education['SchoolYear']) : 'Not provided' ?>
                    </div>
                </div>
                <div>
                    <div class="field-label">Semester</div>
                    <div class="field-value <?= empty($education['Semester']) ? 'empty' : '' ?>">
                        <?= !empty($education['Semester']) ? esc($education['Semester']) : 'Not provided' ?>
                    </div>
                </div>
                <div>
                    <div class="field-label">Status</div>
                    <?php if (!empty($education['Status'])): ?>
                        <span class="status-badge <?= $statusClass ?>"><?= esc($education['Status']) ?></span>
                    <?php else: ?>
                        <div class="field-value empty">Not provided</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </main>
</div>

<!-- OVERLAY -->
<div class="overlay" id="overlay" onclick="closeDrawer()"></div>

<!-- DRAWER -->
<div class="drawer" id="drawer">
    <div style="position:sticky; top:0; background:white; z-index:10; padding:20px 24px 16px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div style="font-size:17px; font-weight:800; color:#1f4432;">Update Profile</div>
            <div style="font-size:12px; color:#9ca3af; margin-top:2px;">Your changes are saved immediately.</div>
        </div>
        <button onclick="closeDrawer()" style="width:34px; height:34px; border-radius:50%; border:1px solid #e5e7eb; background:white; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center;">✕</button>
    </div>

    <form method="POST" action="<?= url('save-profile') ?>" style="padding:20px 24px 40px;">
        <div class="drawer-section-title">👤 Personal Information</div>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <input class="drawer-input" name="FirstName" value="<?= esc($profile['FirstName'] ?? '') ?>" placeholder="First Name">
            <input class="drawer-input" name="LastName" value="<?= esc($profile['LastName'] ?? '') ?>" placeholder="Last Name">
        </div>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <input class="drawer-input" name="MiddleName" value="<?= esc($profile['MiddleName'] ?? '') ?>" placeholder="Middle Name">
            <select class="drawer-input" name="Suffix">
                <option value="">Suffix (optional)</option>
                <?php foreach (['Jr','Sr','I','II','III'] as $s): ?>
                    <option value="<?= $s ?>" <?= (($profile['Suffix'] ?? '') == $s) ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <select class="drawer-input" name="Gender">
                <option value="">Gender</option>
                <?php foreach (['Male','Female','Other'] as $g): ?>
                    <option value="<?= $g ?>" <?= (($profile['Gender'] ?? '') == $g) ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
            <input class="drawer-input" type="date" name="Birthdate" value="<?= esc($profile['Birthdate'] ?? '') ?>">
        </div>
        
        <input class="drawer-input" name="ContactNumber" value="<?= esc($profile['ContactNumber'] ?? '') ?>" 
               placeholder="Contact Number" maxlength="11" 
               onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
               style="margin-bottom:10px;">
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <input class="drawer-input" name="Barangay" value="<?= esc($profile['Barangay'] ?? '') ?>" placeholder="Barangay">
            <input class="drawer-input" name="City" value="<?= esc($profile['City'] ?? '') ?>" placeholder="City">
        </div>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <input class="drawer-input" name="Province" value="<?= esc($profile['Province'] ?? '') ?>" placeholder="Province">
            <input class="drawer-input" name="Country" value="<?= esc($profile['Country'] ?? '') ?>" placeholder="Country">
        </div>
        
        <input class="drawer-input" name="PostalCode" value="<?= esc($profile['PostalCode'] ?? '') ?>" placeholder="Postal Code" style="margin-bottom:4px;">
        
        <div class="drawer-section-title">🎓 Education Information</div>
        
        <input class="drawer-input" name="SchoolName" value="<?= esc($education['SchoolName'] ?? '') ?>" placeholder="School Name" style="margin-bottom:10px;">
        <input class="drawer-input" name="CourseStrand" value="<?= esc($education['CourseStrand'] ?? '') ?>" placeholder="Course / Strand" style="margin-bottom:10px;">
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <input class="drawer-input" name="YearLevel" value="<?= esc($education['YearLevel'] ?? '') ?>" placeholder="Year Level">
            <input class="drawer-input" name="SchoolYear" value="<?= esc($education['SchoolYear'] ?? '') ?>" placeholder="School Year">
        </div>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <input class="drawer-input" name="Semester" value="<?= esc($education['Semester'] ?? '') ?>" placeholder="Semester">
            <select class="drawer-input" name="Status">
                <option value="">Status</option>
                <?php foreach (['Enrolled','Graduated','Irregular','Transferee'] as $st): ?>
                    <option value="<?= $st ?>" <?= (($education['Status'] ?? '') == $st) ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" 
            style="width:100%; background:#1f4432; color:white; padding:13px; border-radius:12px; font-weight:700; font-size:15px; border:none; cursor:pointer; margin-top:10px; transition:background 0.2s, transform 0.12s;"
            onmouseover="this.style.background='#163324'" 
            onmouseout="this.style.background='#1f4432'">
            Save Changes
        </button>
    </form>
</div>

<script>
function openDrawer() {
    document.getElementById('drawer').classList.add('open');
    document.getElementById('overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDrawer() {
    document.getElementById('drawer').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDrawer();
});
</script>

</body>
</html>