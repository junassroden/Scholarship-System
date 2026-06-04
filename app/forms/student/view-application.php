<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'student') {
    header('Location: ' . url('create'));
    exit;
}

$user = $_SESSION['user'];

// IMPORTANT: Get the ID from the route parameter, not $_GET
$applicationID = $id ?? 0;

// Get student ID
$student = db()->table('tblStudent')
    ->where('userID', $user['userID'])
    ->get_all();

if (empty($student)) {
    header('Location: ' . url('applications'));
    exit;
}

$studID = $student[0]['studID'];

// Get application details
$application = db()->table('tblApplication')
    ->where('applicationID', $applicationID)
    ->where('studID', $studID)
    ->get_all();

if (empty($application)) {
    header('Location: ' . url('applications'));
    exit;
}

$application = $application[0];

// Get scholarship details
$scholarship = db()->table('tblScholarshipForm')
    ->where('ScholarshipID', $application['scholarshipID'])
    ->get_all();

$scholarship = !empty($scholarship) ? $scholarship[0] : null;

if (!$scholarship) {
    header('Location: ' . url('applications'));
    exit;
}

// Get organizer info for the scholarship
$organizer = db()->table('tblOrganizer')
    ->where('OrganizerID', $scholarship['OrganizerID'])
    ->get_all();

$organizerName = '';
if (!empty($organizer)) {
    $orgLogin = db()->table('tblLogin')
        ->where('userID', $organizer[0]['userID'])
        ->get_all();
    $organizerName = !empty($orgLogin) ? $orgLogin[0]['Username'] : 'Organizer';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application - <?= esc($scholarship['ScholarshipName']) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

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
            cursor: pointer;
        }

        .btn-back:hover {
            background: #f5faf7;
        }

        .doc-link {
            display: inline-block;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 8px 12px;
            text-align: center;
            color: var(--green-accent);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
        }

        .doc-link:hover {
            background: #d1fae5;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-approved {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .status-rejected {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .status-ongoing {
            background: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde68a;
        }

        .announcement-banner {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .announcement-icon {
            font-size: 24px;
        }

        .announcement-title {
            font-weight: 700;
            font-size: 14px;
            color: #92400e;
            margin-bottom: 4px;
        }

        .announcement-message {
            font-size: 13px;
            color: #78350f;
            line-height: 1.5;
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
            <span class="sidebar-logo-text">Student Panel</span>
        </div>

        <nav>
            <a href="<?= url('student-dashboard') ?>">
                <span class="nav-icon">📚</span> Scholarships
            </a>
            <a href="<?= url('student-profile') ?>">
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-family:'Playfair Display',serif; font-size:28px; font-weight:700; color:#1f4432; margin:0 0 4px;">Application Details</h1>
                <p style="color:#6b7280; font-size:14px; margin:0;">View your submitted application and scholarship information</p>
            </div>
            <button onclick="goBack()" class="btn-back">⬅ Back</button>
        </div>

        <!-- Organizer Announcement (MOTD) -->
        <?php if (!empty($scholarship['Announcement'])): ?>
            <div class="announcement-banner">
                <div class="announcement-icon">📢</div>
                <div style="flex:1;">
                    <div class="announcement-title">Message from <?= esc($organizerName) ?></div>
                    <div class="announcement-message"><?= nl2br(esc($scholarship['Announcement'])) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- SCHOLARSHIP INFO CARD -->
        <div class="info-card">
            <div class="card-header">
                <div class="card-icon">🎓</div>
                <div>
                    <div class="card-title">Scholarship Information</div>
                    <div class="card-subtitle">Details about the scholarship you applied for</div>
                </div>
            </div>

            <div class="info-row">
                <div>
                    <div class="info-label">Scholarship Title</div>
                    <div class="info-value" style="font-size:16px; font-weight:700; color:var(--green-deep);">
                        <?= esc($scholarship['ScholarshipName']) ?>
                    </div>
                </div>
                <div>
                    <div class="info-label">Organizer</div>
                    <div class="info-value"><?= esc($organizerName) ?></div>
                </div>
                <div>
                    <div class="info-label">Deadline</div>
                    <div class="info-value"><?= esc($scholarship['ExpiryDate']) ?></div>
                </div>
                <div style="grid-column: 1 / -1;">
                    <div class="info-label">Description</div>
                    <div class="info-value" style="white-space: pre-line; line-height:1.6;">
                        <?= nl2br(esc($scholarship['Description'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- APPLICATION STATUS CARD -->
        <div class="info-card">
            <div class="card-header">
                <div class="card-icon">📋</div>
                <div>
                    <div class="card-title">Application Status</div>
                    <div class="card-subtitle">Current status of your application</div>
                </div>
            </div>

            <div class="info-row">
                <div>
                    <div class="info-label">Application Date</div>
                    <div class="info-value"><?= esc($application['application_date']) ?></div>
                </div>
                <div>
                    <div class="info-label">Current Status</div>
                    <div class="info-value">
                        <span class="status-badge <?= 
                            $application['application_status'] === 'approved' ? 'status-approved' : 
                            ($application['application_status'] === 'rejected' ? 'status-rejected' : 'status-ongoing')
                        ?>">
                            <?= esc(ucfirst($application['application_status'] ?? 'ongoing')) ?>
                        </span>
                    </div>
                </div>
                <div>
                    <div class="info-label">Remarks</div>
                    <div class="info-value"><?= esc($application['remarks'] ?? 'No remarks yet') ?></div>
                </div>
            </div>
        </div>

        <!-- SUBMITTED DOCUMENTS CARD -->
        <div class="info-card">
            <div class="card-header">
                <div class="card-icon">📁</div>
                <div>
                    <div class="card-title">Submitted Documents</div>
                    <div class="card-subtitle">Files you submitted with your application</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                <?php
                $docs = [
                    'Birth Certificate' => $application['BirthCertificateFile'] ?? '',
                    'Certificate of Grades' => $application['COGFile'] ?? '',
                    'Enrollment Form' => $application['EnrollmentFile'] ?? '',
                    'Certificate of Enrollment' => $application['CertificateFile'] ?? '',
                ];
                foreach ($docs as $label => $filePath):
                    if (!empty($filePath)):
                ?>
                    <a href="<?= url('view-file?path=' . urlencode($filePath)) ?>" class="doc-link" target="_blank">
                        📄 <?= esc($label) ?>
                    </a>
                    <?php else: ?>
                    <div class="doc-link" style="background:#f5faf7; color:var(--text-muted); border-color:var(--border); cursor:default;">
                        📄 <?= esc($label) ?> (Not submitted)
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>

    </main>
</div>

<script>
function goBack() {
    // Check if there's a previous page in history
    if (document.referrer) {
        window.history.back();
    } else {
        // Fallback to dashboard if no history
        window.location.href = '<?= url("student-dashboard") ?>';
    }
}
</script>

</body>
</html>