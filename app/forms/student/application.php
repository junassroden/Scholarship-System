<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'];

$scholarshipMap = [];
$scholarshipExpiry = [];
if (!empty($scholarships)) {
    foreach ($scholarships as $s) {
        $scholarshipMap[$s['ScholarshipID']] = $s['ScholarshipName'];
        $scholarshipExpiry[$s['ScholarshipID']] = $s['ExpiryDate'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/responsiveMYapplications.css">
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

        /* ── Status badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .badge-approved  { background: #d1fae5; color: #065f46; }
        .badge-approved  .badge-dot { background: #059669; }
        .badge-pending   { background: #fef3c7; color: #92400e; }
        .badge-pending   .badge-dot { background: #d97706; animation: blink 1.4s infinite; }
        .badge-rejected  { background: #fee2e2; color: #991b1b; }
        .badge-rejected  .badge-dot { background: #dc2626; }
        .badge-ongoing   { background: #dbeafe; color: #1e40af; }
        .badge-ongoing   .badge-dot { background: #2563eb; animation: blink 1.4s infinite; }
        .badge-expired   { background: #f3f4f6; color: #6b7280; }
        .badge-expired   .badge-dot { background: #9ca3af; }
        .badge-default   { background: #f3f4f6; color: #374151; }
        .badge-default   .badge-dot { background: #6b7280; }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* ── Stat cards ── */
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

        /* ── Table ── */
        .table-wrap {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .app-table { width: 100%; border-collapse: collapse; min-width: 500px; }

        .app-table thead { background: var(--green-dark); }

        .app-table th {
            padding: 13px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: rgba(255,255,255,0.85);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .app-table td {
            padding: 16px 20px;
            font-size: 13.5px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }

        .app-table tr:last-child td { border-bottom: none; }

        .app-table tbody tr { transition: background 0.15s; }
        .app-table tbody tr:hover { background: #f5faf7; }

        /* ── Filter tabs ── */
        .filter-tab {
            padding: 6px 16px;
            border-radius: 99px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            border: 1.5px solid var(--border);
            background: var(--bg-card);
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.18s;
            letter-spacing: 0.01em;
        }

        .filter-tab:hover  { border-color: var(--green-dark); color: var(--green-dark); }
        .filter-tab.active { background: var(--green-dark); color: white; border-color: var(--green-dark); }

        /* ── Search input ── */
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

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 56px 24px;
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .empty-state .icon { font-size: 48px; margin-bottom: 16px; opacity: 0.5; }

        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--green-deep);
            margin: 0 0 8px;
        }

        .empty-state p { color: var(--text-muted); font-size: 14px; margin: 0 0 24px; }

        .btn-browse {
            display: inline-block;
            background: var(--green-dark);
            color: white;
            padding: 11px 28px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-browse:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
        }

        /* ── No results inline ── */
        .no-results-row {
            padding: 40px;
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
        }

        /* ── Main area ── */
        .main-area {
            flex: 1;
            padding: 40px 48px 60px;
            max-width: 100%;
            margin: 0 auto;
        }

        /* ── Stats grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }

        /* ── Filter toolbar ── */
        .filter-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .filter-tabs-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* ══════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
        ══════════════════════════════════════════ */

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
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }

            .filter-toolbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-input {
                width: 100%;
            }

            .app-table th,
            .app-table td {
                padding: 12px 14px;
                font-size: 12.5px;
            }

            /* Hide Remarks column on tablets */
            .app-table th:last-child,
            .app-table td:last-child {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .main-area {
                padding: 14px 10px 32px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
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

            .filter-tabs-group {
                gap: 6px;
            }

            .filter-tab {
                padding: 5px 12px;
                font-size: 12px;
            }

            .app-table th,
            .app-table td {
                padding: 10px 12px;
                font-size: 12px;
            }

            /* Hide Date column on mobile too */
            .app-table th:nth-child(2),
            .app-table td:nth-child(2) {
                display: none;
            }

            .table-wrap {
                border-radius: 12px;
            }
        }

        @media (max-width: 360px) {
            .main-area {
                padding: 10px 8px 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
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
            <a href="<?= url('applications') ?>" class="active">
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

    <!-- MAIN -->
    <main class="main-area">

        <!-- PAGE HEADER -->
        <div style="margin-bottom:28px;">
            <h1 style="font-family:'Playfair Display',serif; font-size:28px; color:var(--green-deep); margin:0 0 4px;">My Applications</h1>
            <p style="color:var(--text-muted); font-size:13px; margin:0;">Track the status of your scholarship applications.</p>
        </div>

        <?php if (!empty($applications)):

            /* ── Pre-compute statuses ── */
            $counts = ['approved' => 0, 'pending' => 0, 'rejected' => 0, 'ongoing' => 0, 'expired' => 0, 'other' => 0];
            $processedApps = [];

            foreach ($applications as $app) {
                $status = strtolower($app['application_status']);
                $scholarshipName = $scholarshipMap[$app['scholarshipID']] ?? 'Unknown';
                $expiryDate      = $scholarshipExpiry[$app['scholarshipID']] ?? null;
                $remarks         = $app['remarks'];

                if ($expiryDate && date('Y-m-d') > $expiryDate) {
                    $status  = 'expired';
                    $remarks = 'This scholarship has expired.';
                }

                if (isset($counts[$status])) { $counts[$status]++; } else { $counts['other']++; }

                $processedApps[] = [
                    'name'    => $scholarshipName,
                    'date'    => $app['application_date'],
                    'status'  => $status,
                    'remarks' => $remarks,
                ];
            }
            $total = count($processedApps);
        ?>

        <!-- SUMMARY STAT CARDS -->
        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-icon" style="background:#ecfdf5;">📄</div>
                <div>
                    <div class="stat-value"><?= $total ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#d1fae5;">✅</div>
                <div>
                    <div class="stat-value" style="color:#065f46;"><?= $counts['approved'] ?></div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>


            <div class="stat-card">
                <div class="stat-icon" style="background:#dbeafe;">🔄</div>
                <div>
                    <div class="stat-value" style="color:#1e40af;"><?= $counts['ongoing'] ?></div>
                    <div class="stat-label">Ongoing</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#fee2e2;">❌</div>
                <div>
                    <div class="stat-value" style="color:#991b1b;"><?= $counts['rejected'] ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>

        </div>

        <!-- FILTER TABS + SEARCH -->
        <div class="filter-toolbar">

            <div class="filter-tabs-group">
                <button class="filter-tab active" onclick="filterApps('all', this)">All (<?= $total ?>)</button>
                <?php if ($counts['approved']): ?>
                    <button class="filter-tab" onclick="filterApps('approved', this)">Approved (<?= $counts['approved'] ?>)</button>
                <?php endif; ?>
                <?php if ($counts['pending']): ?>
                    <button class="filter-tab" onclick="filterApps('pending', this)">Pending (<?= $counts['pending'] ?>)</button>
                <?php endif; ?>
                <?php if ($counts['ongoing']): ?>
                    <button class="filter-tab" onclick="filterApps('ongoing', this)">Ongoing (<?= $counts['ongoing'] ?>)</button>
                <?php endif; ?>
                <?php if ($counts['rejected']): ?>
                    <button class="filter-tab" onclick="filterApps('rejected', this)">Rejected (<?= $counts['rejected'] ?>)</button>
                <?php endif; ?>
                <?php if ($counts['expired']): ?>
                    <button class="filter-tab" onclick="filterApps('expired', this)">Expired (<?= $counts['expired'] ?>)</button>
                <?php endif; ?>
            </div>

            <input type="text" id="search-input" oninput="filterApps(currentFilter, null)"
                placeholder="🔍 Search scholarship..."
                class="search-input">
        </div>

        <!-- TABLE -->
        <div class="table-wrap">
            <div style="overflow-x:auto;">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Scholarship</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="app-tbody">
                        <?php foreach ($processedApps as $app):
                            $s = $app['status'];
                            $badgeClass = in_array($s, ['approved','pending','rejected','ongoing','expired']) ? "badge-$s" : 'badge-default';
                        ?>
                        <tr data-status="<?= esc($s) ?>" data-name="<?= strtolower(esc($app['name'])) ?>">
                            <td>
                                <div style="font-weight:700; color:var(--green-deep); font-size:14px;"><?= esc($app['name']) ?></div>
                            </td>
                            <td>
                                <div style="color:var(--text-muted); font-size:13px;"><?= esc($app['date']) ?></div>
                            </td>
                            <td>
                                <span class="badge <?= $badgeClass ?>">
                                    <span class="badge-dot"></span>
                                    <?= ucfirst($s) ?>
                                </span>
                            </td>
                            <td>
                                <div style="color:var(--text-muted); font-size:13px; max-width:280px;">
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

            <!-- No results message -->
            <div id="no-results" style="display:none;" class="no-results-row">
                No applications match your filter or search.
            </div>
        </div>

        <?php else: ?>

        <!-- EMPTY STATE -->
        <div class="empty-state">
            <div class="icon">📭</div>
            <h3>No Applications Yet</h3>
            <p>You haven't applied for any scholarships yet. Browse available scholarships and submit your first application!</p>
            <a href="<?= url('student-dashboard') ?>" class="btn-browse">Browse Scholarships →</a>
        </div>

        <?php endif; ?>

    </main>
</div>

<script>
let currentFilter = 'all';

function filterApps(status, clickedBtn) {
    if (clickedBtn) {
        currentFilter = status;
        document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
        clickedBtn.classList.add('active');
    }

    const search = (document.getElementById('search-input')?.value || '').toLowerCase().trim();
    const rows   = document.querySelectorAll('#app-tbody tr');
    let visible  = 0;

    rows.forEach(row => {
        const matchStatus = currentFilter === 'all' || row.dataset.status === currentFilter;
        const matchSearch = !search || (row.dataset.name || '').includes(search);
        if (matchStatus && matchSearch) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    const noResults = document.getElementById('no-results');
    if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
}
</script>

</body>
</html>