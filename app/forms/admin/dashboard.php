<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

if (!$user || strtolower(trim($user['Role'])) !== 'admin') {
    header('Location:' . url('create'));
    exit;
}

// Get all data for analytics
$totalUsers = count(db()->table('tblLogin')->get_all());
$totalStudents = count(db()->table('tblLogin')->where('Role', 'student')->get_all());
$totalOrganizers = count(db()->table('tblLogin')->where('Role', 'organizers')->get_all());
$totalScholarships = count(db()->table('tblScholarshipForm')->get_all());

// Get applications data by status
$applications = db()->table('tblApplication')->get_all();
$approvedCount = 0;
$ongoingCount = 0;
$rejectedCount = 0;

foreach ($applications as $app) {
    $status = strtolower($app['application_status'] ?? 'ongoing');
    if ($status === 'approved') $approvedCount++;
    elseif ($status === 'rejected') $rejectedCount++;
    else $ongoingCount++;
}

// Get applications per month for the last 6 months
$monthlyData = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('F', strtotime("-$i months"));
    $year = date('Y', strtotime("-$i months"));
    $monthNum = date('m', strtotime("-$i months"));
    $monthKey = $month . ' ' . $year;
    
    $count = db()->raw("SELECT COUNT(*) as count FROM tblApplication WHERE MONTH(application_date) = ? AND YEAR(application_date) = ?", [$monthNum, $year])->fetch();
    $monthlyData[$monthKey] = $count['count'] ?? 0;
}

// Get organizer approval stats
$pendingOrganizers = count(db()->table('tblLogin')->where('Role', 'organizers')->where('Status', 'pending')->get_all());
$approvedOrganizers = count(db()->table('tblLogin')->where('Role', 'organizers')->where('Status', 'accepted')->get_all());
$rejectedOrganizers = count(db()->table('tblLogin')->where('Role', 'organizers')->where('Status', 'rejected')->get_all());

// Get active vs expired scholarships
$today = date('Y-m-d');
$activeScholarships = 0;
$expiredScholarships = 0;
$allScholarships = db()->table('tblScholarshipForm')->get_all();
foreach ($allScholarships as $s) {
    if ($s['ExpiryDate'] >= $today) $activeScholarships++;
    else $expiredScholarships++;
}

// Get top scholarships by applications
$topScholarships = db()->raw("
    SELECT s.ScholarshipName, COUNT(a.applicationID) as app_count 
    FROM tblScholarshipForm s 
    LEFT JOIN tblApplication a ON s.ScholarshipID = a.scholarshipID 
    GROUP BY s.ScholarshipID 
    ORDER BY app_count DESC 
    LIMIT 5
")->fetchAll();

$credentials = [];

// Pending organizers
$organizers = db()->table('tblLogin')
    ->where('Role', 'organizers')
    ->where('Status', 'pending')
    ->get_all();

foreach ($organizers as $org) {
    $userID = $org['userID'];
    $orgData = db()->table('tblOrganizer')->where('userID', $userID)->get_all();
    if (empty($orgData)) continue;

    $OrganizerID = $orgData[0]['OrganizerID'];
    $cred = db()->table('tblOrganizerCredentials')->where('OrganizerID', $OrganizerID)->get_all();
    $c = $cred[0] ?? [];

    $credentials[] = [
        'userID' => $userID,
        'Username' => $org['Username'] ?? 'N/A',
        'Email' => $org['Email'] ?? 'N/A',
        'OrganizationName' => $c['OrganizationName'] ?? 'No submission yet',
        'OrganizationType' => $c['OrganizationType'] ?? 'N/A',
        'ContactEmail' => $c['ContactEmail'] ?? 'N/A',
        'ContactNumber' => $c['ContactNumber'] ?? 'N/A',
        'DocumentType' => $c['DocumentType'] ?? 'N/A',
        'FilePath' => $c['FilePath'] ?? null,
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            width: 260px; height: 100%;
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
            padding: 40px 48px;
            min-height: 100vh;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--green-deep);
        }

        .page-header p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 20px 20px;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(31,68,50,0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--green-accent);
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--green-deep);
            line-height: 1;
        }

        .stat-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 28px;
            opacity: 0.1;
        }

        /* ── CHART GRID ── */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .chart-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 20px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .chart-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(31,68,50,0.1);
        }

        .chart-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--green-deep);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-title-icon {
            font-size: 18px;
        }

        canvas {
            max-height: 250px;
            width: 100%;
        }

        /* ── TOP SCHOLARSHIPS TABLE ── */
        .top-scholarships {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 20px;
            margin-bottom: 28px;
        }

        .top-scholarships h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--green-deep);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rank-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .rank-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .rank-number {
            width: 30px;
            height: 30px;
            background: var(--green-dark);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }

        .rank-info {
            flex: 1;
        }

        .rank-name {
            font-weight: 600;
            font-size: 13px;
        }

        .rank-count {
            font-size: 12px;
            color: var(--text-muted);
        }

        .rank-bar {
            width: 150px;
            height: 6px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
        }

        .rank-bar-fill {
            height: 100%;
            background: var(--green-accent);
            border-radius: 3px;
        }

        /* ── FLASH MESSAGES ── */
        .flash-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .flash-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* ── SECTION HEADER ── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
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

        /* ── TABLE ── */
        .table-wrap {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--green-dark);
        }

        th {
            padding: 13px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: rgba(255,255,255,0.85);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 13.5px;
            color: var(--text-main);
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr {
            transition: background 0.15s;
        }

        tbody tr:hover {
            background: #f5faf7;
        }

        .avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--green-accent);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .applicant-cell {
            display: flex;
            align-items: center;
        }

        .applicant-info .name {
            font-weight: 600;
            font-size: 13.5px;
        }

        .applicant-info .email {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .btn-approve {
            display: inline-block;
            background: var(--green-dark);
            color: white;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-approve:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
        }

        .btn-reject {
            display: inline-block;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid #fca5a5;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-reject:hover {
            background: #fecaca;
            transform: translateY(-1px);
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

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

        @media (max-width: 1024px) {
            .main { padding: 30px 32px; }
            .charts-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .main { padding: 20px 16px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>

<body>

<div style="display:flex; min-height:100vh;">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🛠</div> 
            <span class="sidebar-logo-text">Admin Panel</span>
        </div>

        <nav>
            <a href="<?= url('admin-dashboard') ?>" class="active">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="<?= url('admin-students') ?>">
                <span class="nav-icon">🎓</span> Students
            </a>
            <a href="<?= url('admin-organizers') ?>">
                <span class="nav-icon">🏢</span> Organizers
            </a>
            <a href="<?= url('admin-scholarships') ?>">
                <span class="nav-icon">📚</span> Scholarships
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= url('logout') ?>" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Welcome back, <?= htmlspecialchars($user['Username']) ?></h1>
                <p>Here's what's happening on your platform today.</p>
            </div>
            <div class="badge-admin">Administrator</div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-value"><?= $totalUsers ?></div>
                <span class="stat-icon">👥</span>
            </div>
            <div class="stat-card">
                <div class="stat-label">Students</div>
                <div class="stat-value"><?= $totalStudents ?></div>
                <span class="stat-icon">🎓</span>
            </div>
            <div class="stat-card">
                <div class="stat-label">Organizers</div>
                <div class="stat-value"><?= $totalOrganizers ?></div>
                <span class="stat-icon">🏢</span>
            </div>
            <div class="stat-card">
                <div class="stat-label">Scholarships</div>
                <div class="stat-value"><?= $totalScholarships ?></div>
                <span class="stat-icon">📚</span>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <!-- Application Status Chart -->
            <div class="chart-card">
                <div class="chart-title">
                    <span class="chart-title-icon">📊</span>
                    Application Status Overview
                </div>
                <canvas id="statusChart"></canvas>
            </div>

            <!-- Monthly Applications Chart -->
            <div class="chart-card">
                <div class="chart-title">
                    <span class="chart-title-icon">📈</span>
                    Applications Per Month
                </div>
                <canvas id="monthlyChart"></canvas>
            </div>

            <!-- Organizer Approval Chart -->
            <div class="chart-card">
                <div class="chart-title">
                    <span class="chart-title-icon">🏢</span>
                    Organizer Verification Status
                </div>
                <canvas id="organizerChart"></canvas>
            </div>

            <!-- Scholarship Status Chart -->
            <div class="chart-card">
                <div class="chart-title">
                    <span class="chart-title-icon">⏰</span>
                    Scholarship Status
                </div>
                <canvas id="scholarshipChart"></canvas>
            </div>
        </div>

        <!-- Top Scholarships -->
        <div class="top-scholarships">
            <h3>
                <span>🏆</span>
                Most Applied Scholarships
            </h3>
            <div class="rank-list">
                <?php 
                $maxCount = !empty($topScholarships) ? max(array_column($topScholarships, 'app_count')) : 1;
                $rank = 1;
                foreach ($topScholarships as $s): 
                    $percentage = ($s['app_count'] / $maxCount) * 100;
                ?>
                <div class="rank-item">
                    <div class="rank-number"><?= $rank++ ?></div>
                    <div class="rank-info">
                        <div class="rank-name"><?= htmlspecialchars($s['ScholarshipName']) ?></div>
                        <div class="rank-count"><?= $s['app_count'] ?> applicant(s)</div>
                    </div>
                    <div class="rank-bar">
                        <div class="rank-bar-fill" style="width: <?= $percentage ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($topScholarships)): ?>
                    <div class="empty-state" style="padding: 20px;">
                        <p>No scholarship applications yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($success = get_flash('success')): ?>
            <div class="flash-success">✅ <?= $success ?></div>
        <?php endif; ?>

        <?php if ($error = get_flash('error')): ?>
            <div class="flash-error">❌ <?= $error ?></div>
        <?php endif; ?>

        <!-- Pending Organizers Table -->
        <div class="section-header">
            <div class="section-title">
                Pending Organizer Credential Submissions
                <?php if (!empty($credentials)): ?>
                    <span class="count-pill"><?= count($credentials) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($credentials)): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Organization</th>
                            <th>Contact</th>
                            <th>Document</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($credentials as $row): ?>
                        <tr>
                            <td>
                                <div class="applicant-cell">
                                    <div class="avatar"><?= strtoupper(substr($row['Username'], 0, 1)) ?></div>
                                    <div class="applicant-info">
                                        <div class="name"><?= htmlspecialchars($row['Username']) ?></div>
                                        <div class="email"><?= htmlspecialchars($row['Email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="org-name"><?= htmlspecialchars($row['OrganizationName']) ?></div>
                                <div class="org-type"><?= htmlspecialchars($row['OrganizationType']) ?></div>
                            </td>
                            <td>
                                <div style="font-size:13px;"><?= htmlspecialchars($row['ContactEmail']) ?></div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;"><?= htmlspecialchars($row['ContactNumber']) ?></div>
                            </td>
                            <td>
                                <span class="doc-badge"><?= htmlspecialchars($row['DocumentType']) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($row['FilePath'])): ?>
                                    <a class="view-link" href="<?= url('view-file?path=' . urlencode($row['FilePath'])) ?>">📎 View File</a>
                                <?php else: ?>
                                    <span class="no-file">No file</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a class="btn-approve" href="<?= url('/approve-organizer/' . $row['userID']) ?>">✓ Approve</a>
                                    <a class="btn-reject" href="<?= url('/reject-organizer/' . $row['userID']) ?>">✕ Reject</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon">📋</div>
                <p>No pending organizer credential submissions.</p>
            </div>
        <?php endif; ?>

    </main>

</div>

<script>
// Application Status Chart (Doughnut)
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Ongoing', 'Rejected'],
        datasets: [{
            data: [<?= $approvedCount ?>, <?= $ongoingCount ?>, <?= $rejectedCount ?>],
            backgroundColor: ['#059669', '#2563eb', '#dc2626'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Monthly Applications Chart (Line)
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_keys($monthlyData)) ?>,
        datasets: [{
            label: 'Applications',
            data: <?= json_encode(array_values($monthlyData)) ?>,
            borderColor: '#1f4432',
            backgroundColor: 'rgba(31,68,50,0.1)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#1f4432',
            pointBorderColor: 'white',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Organizer Status Chart (Bar)
const orgCtx = document.getElementById('organizerChart').getContext('2d');
new Chart(orgCtx, {
    type: 'bar',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            label: 'Organizers',
            data: [<?= $pendingOrganizers ?>, <?= $approvedOrganizers ?>, <?= $rejectedOrganizers ?>],
            backgroundColor: ['#f59e0b', '#059669', '#dc2626'],
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Scholarship Status Chart (Pie)
const scholCtx = document.getElementById('scholarshipChart').getContext('2d');
new Chart(scholCtx, {
    type: 'pie',
    data: {
        labels: ['Active', 'Expired'],
        datasets: [{
            data: [<?= $activeScholarships ?>, <?= $expiredScholarships ?>],
            backgroundColor: ['#059669', '#9ca3af'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>

</body>
</html>