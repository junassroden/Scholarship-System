<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'student') {
    header('Location: ' . url('create'));
    exit;
}

$user = $_SESSION['user'];

// Map scholarships for name lookup
$scholarshipMap = [];
if (!empty($scholarships)) {
    foreach ($scholarships as $s) {
        $scholarshipMap[$s['ScholarshipID']] = $s['ScholarshipName'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application History</title>
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/responsivehistory.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
        .main-area {
            flex: 1;
            padding: 40px 48px 60px;
            max-width: 100%;
            margin: 0 auto;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            margin-bottom: 28px;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--green-deep);
            margin: 0 0 4px;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 13px;
            margin: 0;
        }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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

        .stat-card.approved::before { background: #059669; }
        .stat-card.ongoing::before  { background: #2563eb; }
        .stat-card.rejected::before { background: #dc2626; }

        .stat-icon {
            width: 42px; height: 42px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: var(--green-deep);
            line-height: 1;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* ── TABLE WRAP ── */
        .table-wrap {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .search-input {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            color: var(--text-main);
            background: var(--bg-card);
            outline: none;
            width: 260px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input:focus {
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
        }

        /* ── TABLE ── */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        .history-table thead {
            background: var(--green-dark);
        }

        .history-table th {
            padding: 13px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: rgba(255,255,255,0.85);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .history-table td {
            padding: 16px 20px;
            font-size: 13.5px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }

        .history-table tr:last-child td {
            border-bottom: none;
        }

        .history-table tbody tr {
            transition: background 0.15s;
        }

        .history-table tbody tr:hover {
            background: #f5faf7;
        }

        /* ── STATUS BADGES ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            letter-spacing: 0.03em;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-approved .badge-dot { background: #059669; }

        .badge-ongoing {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-ongoing .badge-dot {
            background: #2563eb;
            animation: blink 1.4s infinite;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-rejected .badge-dot { background: #dc2626; }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-pending .badge-dot {
            background: #d97706;
            animation: blink 1.4s infinite;
        }

        .badge-default {
            background: #f3f4f6;
            color: #374151;
        }
        .badge-default .badge-dot { background: #9ca3af; }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 56px 24px;
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--green-deep);
            margin: 0 0 8px;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 14px;
        }

        /* ── NO RESULTS ── */
        .no-results {
            display: none;
            text-align: center;
            padding: 32px;
            color: var(--text-muted);
            font-size: 14px;
        }

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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-card {
                padding: 12px 14px;
            }

            .stat-value {
                font-size: 18px;
            }

            .stat-icon {
                width: 34px;
                height: 34px;
                font-size: 15px;
            }

            .search-input {
                width: 100%;
            }

            .history-table th,
            .history-table td {
                padding: 12px 14px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .main-area {
                padding: 14px 12px 32px;
            }

            .stats-grid {
                gap: 8px;
            }

            .stat-card {
                padding: 10px 12px;
            }

            .stat-value {
                font-size: 16px;
            }

            .history-table th,
            .history-table td {
                padding: 10px 12px;
                font-size: 11px;
            }

            /* Hide Remarks column on mobile */
            .history-table th:last-child,
            .history-table td:last-child {
                display: none;
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
            <a href="<?= url('application-history') ?>" class="active">
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
        <div class="page-header">
            <h1>Application History</h1>
            <p>View all your submitted scholarship applications and their outcomes.</p>
        </div>

        <?php if (!empty($applications)):

            // Pre-compute counts for stat cards
            $total    = count($applications);
            $approved = 0;
            $rejected = 0;
            $ongoing  = 0;
            $pending  = 0;

            foreach ($applications as $app) {
                $s = strtolower($app['application_status'] ?? 'pending');
                if ($s === 'approved')      $approved++;
                elseif ($s === 'rejected')  $rejected++;
                elseif ($s === 'ongoing')   $ongoing++;
                elseif ($s === 'pending')   $pending++;
            }
        ?>

        <!-- STAT CARDS (My Applications style) -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ecfdf5;">📊</div>
                <div>
                    <div class="stat-value"><?= $total ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>

            <div class="stat-card approved">
                <div class="stat-icon" style="background:#d1fae5;">✅</div>
                <div>
                    <div class="stat-value" style="color:#065f46;"><?= $approved ?></div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>

            <?php if ($ongoing > 0): ?>
            <div class="stat-card ongoing">
                <div class="stat-icon" style="background:#dbeafe;">🔄</div>
                <div>
                    <div class="stat-value" style="color:#1e40af;"><?= $ongoing ?></div>
                    <div class="stat-label">Ongoing</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($pending > 0): ?>
            <div class="stat-card">
                <div class="stat-icon" style="background:#fef3c7;">⏳</div>
                <div>
                    <div class="stat-value" style="color:#92400e;"><?= $pending ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <?php endif; ?>

            <div class="stat-card rejected">
                <div class="stat-icon" style="background:#fee2e2;">❌</div>
                <div>
                    <div class="stat-value" style="color:#991b1b;"><?= $rejected ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
            <input type="text" id="historySearch" class="search-input"
                   placeholder="🔍 Search scholarship..."
                   onkeyup="filterHistory()">
        </div>

        <!-- TABLE -->
        <div class="table-wrap">
            <div style="overflow-x: auto;">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Scholarship</th>
                            <th>Application Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="historyTbody">
                        <?php foreach ($applications as $app):
                            $status = strtolower($app['application_status'] ?? 'pending');
                            $badgeClass = in_array($status, ['approved', 'rejected', 'ongoing', 'pending'])
                                ? 'badge-' . $status
                                : 'badge-default';
                            $scholarshipName = $scholarshipMap[$app['scholarshipID']] ?? 'Unknown';
                        ?>
                        <tr data-name="<?= strtolower(esc($scholarshipName)) ?>">
                            <td>
                                <div style="font-weight:700; color:var(--green-deep);">
                                    <?= esc($scholarshipName) ?>
                                </div>
                            </td>
                            <td>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    <?= esc($app['application_date']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?= $badgeClass ?>">
                                    <span class="badge-dot"></span>
                                    <?= esc(ucfirst($status)) ?>
                                </span>
                            </td>
                            <td>
                                <div style="color:var(--text-muted); font-size:13px; max-width:260px;">
                                    <?= !empty($app['remarks'])
                                        ? esc($app['remarks'])
                                        : '<span style="color:#d1d5db; font-style:italic;">No remarks</span>' ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div id="noHistoryResults" class="no-results">
                No applications match your search.
            </div>
        </div>

        <?php else: ?>

        <!-- EMPTY STATE -->
        <div class="empty-state">
            <div class="icon">📭</div>
            <h3>No Application History</h3>
            <p>You haven't submitted any scholarship applications yet.</p>
        </div>

        <?php endif; ?>

    </main>
</div>

<script>
function filterHistory() {
    const query = document.getElementById('historySearch').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#historyTbody tr');
    let visible = 0;

    rows.forEach(row => {
        const match = (row.dataset.name || '').includes(query);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    const noRes = document.getElementById('noHistoryResults');
    if (noRes) {
        noRes.style.display = visible === 0 ? 'block' : 'none';
    }
}
</script>

</body>
</html>