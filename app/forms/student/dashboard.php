<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'student') {
    header('Location: ' . url('create'));
    exit;
}

$user = $_SESSION['user'];

try {
    $db = db();
    
    $sql = "SELECT c.FirstName, c.MiddleName, c.LastName 
            FROM tblStudent s 
            LEFT JOIN tblCredentials c ON s.studID = c.StudID 
            WHERE s.userID = ?";
    
    $result = $db->raw($sql, [$user['userID']]);
    $studentInfo = $result->fetch();
    
    if ($studentInfo && !empty($studentInfo['FirstName'])) {
        $fullName = $studentInfo['FirstName'];
        if (!empty($studentInfo['MiddleName'])) {
            $fullName .= ' ' . $studentInfo['MiddleName'];
        }
        if (!empty($studentInfo['LastName'])) {
            $fullName .= ' ' . $studentInfo['LastName'];
        }
        $user['FullName'] = trim($fullName);
        $_SESSION['user']['FullName'] = $user['FullName'];
    } else {
        $user['FullName'] = !empty($user['Username']) ? $user['Username'] : $user['Email'];
    }
} catch (Exception $e) {
    $user['FullName'] = !empty($user['Username']) ? $user['Username'] : $user['Email'];
}

// Filter out expired scholarships before displaying
$activeScholarships = [];
if (!empty($scholarships)) {
    $today = date('Y-m-d');
    foreach ($scholarships as $scholarship) {
        if ($scholarship['ExpiryDate'] >= $today) {
            $activeScholarships[] = $scholarship;
        }
    }
}

// Create a map of scholarshipID => ApplicationID from the applications array
$applicationMap = [];
if (!empty($applications)) {
    foreach ($applications as $app) {
        $applicationMap[$app['scholarshipID']] = $app['ApplicationID'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">

    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/responsivedashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    

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
        .main {
            flex: 1;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOP HEADER ── */
        .top-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 20px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .top-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--green-deep);
        }

        .top-header p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--green-accent);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-chip .user-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-main);
        }

        .user-chip .user-role {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* ── PAGE BODY ── */
        .page-body {
            flex: 1;
            padding: 36px 48px;
        }

        /* ── SEARCH BAR ── */
        .search-wrap {
            position: relative;
            margin-bottom: 28px;
            max-width: 400px;
        }

        .search-wrap .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 36px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            color: var(--text-main);
            background: var(--bg-card);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
        }

        /* ── SECTION HEADER ── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--green-deep);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .count-pill {
            background: var(--green-dark);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 99px;
        }

        /* ── SCHOLARSHIP GRID ── */
        .scholarship-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        /* ── SCHOLARSHIP CARD ── */
        .scholarship-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .scholarship-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(31,68,50,0.1);
        }

        .card-bar {
            height: 4px;
            background: var(--green-accent);
        }

        .card-bar.applied { background: #60a5fa; }

        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .card-title {
            font-weight: 700;
            font-size: 14.5px;
            color: var(--green-deep);
            line-height: 1.35;
        }

        .card-badge {
            display: inline-block;
            background: #ecfdf5;
            color: var(--green-accent);
            border: 1px solid #a7f3d0;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .card-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.55;
            flex: 1;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-muted);
            background: var(--bg-page);
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid var(--border);
            margin-bottom: 14px;
        }

        .card-meta .expiry {
            font-weight: 600;
            color: var(--text-main);
        }

        .days-left {
            font-weight: 600;
            color: var(--green-accent);
        }

        .days-left.urgent { color: #ef4444; }

        /* ── APPLIED BADGE (small indicator) ── */
        .applied-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 99px;
            border: 1px solid #bfdbfe;
        }

        /* ── CARD ACTIONS ── */
        .btn-apply {
            display: block;
            text-align: center;
            background: var(--green-dark);
            color: white;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-apply:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
        }

        .btn-view-details {
            display: block;
            text-align: center;
            background: var(--green-accent);
            color: white;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-view-details:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
        }

        /* ── EMPTY STATES ── */
        .empty-state {
            text-align: center;
            padding: 56px 24px;
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .empty-state .icon {
            font-size: 44px;
            margin-bottom: 14px;
            opacity: 0.4;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 15px;
        }

        .empty-state p.sub {
            font-size: 13px;
            margin-top: 6px;
        }

        /* ── NO RESULTS ── */
        .no-results {
            display: none;
        }

        /* ── FOOTER ── */
        .page-footer {
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            padding: 16px;
            border-top: 1px solid var(--border);
            background: var(--bg-card);
        }

        /* ══════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
        ══════════════════════════════════════════ */

        @media (max-width: 1100px) {
            .scholarship-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .top-header {
                padding: 14px 20px;
                gap: 12px;
                flex-wrap: wrap;
            }

            .top-header h1 {
                font-size: 18px;
            }

            .user-chip .user-name,
            .user-chip .user-role {
                display: none;
            }

            .page-body {
                padding: 20px 16px;
            }

            .search-wrap {
                max-width: 100%;
            }

            .scholarship-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
        }

        @media (max-width: 480px) {
            .top-header {
                padding: 12px 16px;
            }

            .top-header h1 {
                font-size: 16px;
            }

            .top-header p {
                display: none;
            }

            .page-body {
                padding: 16px 12px;
            }

            .scholarship-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .section-title {
                font-size: 15px;
            }

            .card-body {
                padding: 14px;
            }
        }

        @media (max-width: 360px) {
            .scholarship-grid {
                grid-template-columns: 1fr;
            }

            .page-body {
                padding: 12px 10px;
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
            <a href="<?= url('student-dashboard') ?>" class="active">
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
    <main class="main">

        <!-- Top Header -->
        <div class="top-header">
            <div>
                <h1>Welcome back, <?= esc(explode(' ', $user['FullName'])[0]) ?> 👋</h1>
                <p>Browse active scholarships and apply before the deadlines.</p>
            </div>
            <a href="<?= url('student-profile') ?>" style="text-decoration: none;">
                <div class="user-chip">
                    <div class="avatar">
                        <?php 
                        $fullName = $user['FullName'];
                        $nameParts = explode(' ', $fullName);
                        $firstName = $nameParts[0] ?? '';
                        $lastName = end($nameParts) ?? '';

                        $initial1 = !empty($firstName) ? strtoupper(substr($firstName, 0, 1)) : '';
                        $initial2 = !empty($lastName) && $lastName !== $firstName ? strtoupper(substr($lastName, 0, 1)) : '';

                        $initials = $initial1 . $initial2;
                        echo !empty($initials) ? esc($initials) : '?';
                        ?>
                    </div>
                    <div>
                        <div class="user-name"><?= esc(explode(' ', $user['FullName'])[0]) ?></div>
                        <div class="user-role">Student</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Page Body -->
        <div class="page-body">

            <!-- Search Bar Only -->
            <div class="search-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput"
                    placeholder="Search scholarships..."
                    class="search-input"
                    onkeyup="filterCards()">
            </div>

            <!-- Section Header -->
            <div class="section-header">
                <div class="section-title">
                    📚 Active Scholarships
                    <?php if (!empty($activeScholarships)): ?>
                        <span class="count-pill"><?= count($activeScholarships) ?> Available</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($activeScholarships)): ?>

                <div id="scholarshipGrid" class="scholarship-grid">

                    <?php foreach ($activeScholarships as $scholarship): ?>
                        <?php
                        // Check if this scholarship has been applied for using the map
                        $alreadyApplied = isset($applicationMap[$scholarship['ScholarshipID']]);
                        $applicationID = $applicationMap[$scholarship['ScholarshipID']] ?? null;
                        $daysLeft = (int) ceil((strtotime($scholarship['ExpiryDate']) - time()) / 86400);
                        ?>

                        <div class="scholarship-card"
                             data-name="<?= strtolower(esc($scholarship['ScholarshipName'])) ?>"
                             data-desc="<?= strtolower(esc($scholarship['Description'])) ?>">

                            <div class="card-bar <?= $alreadyApplied ? 'applied' : '' ?>"></div>

                            <div class="card-body">

                                <div class="card-top">
                                    <div class="card-title"><?= esc($scholarship['ScholarshipName']) ?></div>
                                    <span class="card-badge">🎓 Scholarship</span>
                                </div>

                                <!-- Small applied badge instead of full button -->
                                <?php if ($alreadyApplied): ?>
                                    <div style="text-align: right; margin-bottom: 8px;">
                                        <span class="applied-badge">✅ Applied</span>
                                    </div>
                                <?php endif; ?>

                                <p class="card-desc"><?= esc($scholarship['Description']) ?></p>

                                <div class="card-meta">
                                    <span>📅 <span class="expiry"><?= esc($scholarship['ExpiryDate']) ?></span></span>
                                    <?php if ($daysLeft > 0): ?>
                                        <span class="days-left <?= $daysLeft <= 7 ? 'urgent' : '' ?>">
                                            <?= $daysLeft ?>d left
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($alreadyApplied && $applicationID): ?>
                                    <a href="<?= url('view-application/' . $applicationID) ?>" class="btn-view-details">
                                        👁 View Application
                                    </a>
                                <?php elseif (!$alreadyApplied): ?>
                                    <a href="<?= url('apply-scholarship/' . $scholarship['ScholarshipID']) ?>" class="btn-apply">
                                        Apply Now
                                    </a>
                                <?php endif; ?>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

                <div id="noResults" class="no-results">
                    <div class="empty-state" style="margin-top:20px;">
                        <div class="icon">🔍</div>
                        <p>No scholarships match your search.</p>
                        <p class="sub">Try adjusting your search term.</p>
                    </div>
                </div>

            <?php else: ?>

                <div class="empty-state">
                    <div class="icon">📭</div>
                    <p>No active scholarships available at the moment.</p>
                    <p class="sub">Please check back later for new opportunities.</p>
                </div>

            <?php endif; ?>

        </div>

        <!-- Footer -->
        <div class="page-footer">
            © <?= date('Y') ?> Scholarship Portal — Student Dashboard
        </div>

    </main>

</div>

<script>
    function filterCards() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.scholarship-card');
        const noRes = document.getElementById('noResults');
        let visible = 0;

        cards.forEach(card => {
            const match = (card.dataset.name + card.dataset.desc).includes(query);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        if (noRes) noRes.style.display = visible > 0 ? 'none' : 'block';
    }
</script>

</body>
</html>