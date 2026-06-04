<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

if (!$user || strtolower(trim($user['Role'])) !== 'admin') {
    header('Location:' . url('create'));
    exit;
}

// Fetch organizer names for each scholarship
foreach ($scholarships as &$s) {
    // Get organizer login info
    $organizerData = db()->table('tblOrganizer')
        ->where('OrganizerID', $s['OrganizerID'])
        ->get_all();
    
    if (!empty($organizerData)) {
        $loginData = db()->table('tblLogin')
            ->where('userID', $organizerData[0]['userID'])
            ->get_all();
        $s['OrganizerName'] = !empty($loginData) ? $loginData[0]['Username'] : 'Unknown';
    } else {
        $s['OrganizerName'] = 'Unknown';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarships — Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>

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

        .sidebar:hover { transform: translateX(0); }

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

        .sidebar nav a.active .nav-icon { opacity: 1; }

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

        .logout-btn:hover { opacity: 0.9; transform: translateY(-1px); }

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

        /* ── TOOLBAR ── */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
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

        .search-input {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: white;
            color: var(--text-main);
            outline: none;
            width: 240px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input:focus {
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
        }

        /* ── TABLE ── */
        .table-wrap {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        table { width: 100%; border-collapse: collapse; }

        thead { background: var(--green-dark); }

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

        tbody tr:last-child td { border-bottom: none; }

        tbody tr { transition: background 0.15s; }

        tbody tr:hover { background: #f5faf7; }

        /* Scholarship name cell */
        .scholarship-name {
            font-weight: 600;
            font-size: 13.5px;
            color: var(--green-deep);
        }

        .scholarship-id {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-page);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            letter-spacing: 0.04em;
        }

        .desc-cell {
            font-size: 12.5px;
            color: var(--text-muted);
            max-width: 280px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .expiry-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 8px;
            background: #ecfdf5;
            color: var(--green-mid);
            border: 1px solid #a7f3d0;
        }

        .expiry-badge.expired {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        .organizer-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 8px;
            background: rgba(250,204,21,0.12);
            color: #78450a;
            border: 1px solid rgba(250,204,21,0.4);
        }

        /* Delete button */
        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            letter-spacing: 0.02em;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-delete:hover {
            background: #fecaca;
            transform: translateY(-1px);
        }

        /* Empty state */
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
            <a href="<?= url('admin-dashboard') ?>">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="<?= url('admin-students') ?>">
                <span class="nav-icon">🎓</span> Students
            </a>
            <a href="<?= url('admin-organizers') ?>">
                <span class="nav-icon">🏢</span> Organizers
            </a>
            <a href="<?= url('admin-scholarships') ?>" class="active">
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
                <h1>Scholarships</h1>
                <p>View and manage all scholarship listings on the platform.</p>
            </div>
            <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Administrator</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($success = get_flash('success')): ?>
            <div class="flash-success">✅ <?= $success ?></div>
        <?php endif; ?>

        <?php if ($error = get_flash('error')): ?>
            <div class="flash-error">❌ <?= $error ?></div>
        <?php endif; ?>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="section-title">
                All Scholarships
                <?php if (!empty($scholarships)): ?>
                    <span class="count-pill"><?= count($scholarships) ?></span>
                <?php endif; ?>
            </div>

            <input
                type="text"
                class="search-input"
                placeholder="🔍  Search scholarships…"
                id="searchInput"
                onkeyup="filterTable()"
            >
        </div>

        <!-- Table -->
        <?php if (!empty($scholarships)): ?>
        <div class="table-wrap">
            <table id="scholarshipTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Scholarship</th>
                        <th>Description</th>
                        <th>Expiry Date</th>
                        <th>Organizer</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scholarships as $s): ?>
                    <?php
                        $expired = !empty($s['ExpiryDate']) && strtotime($s['ExpiryDate']) < time();
                    ?>
                    <tr data-search="<?= strtolower(htmlspecialchars($s['ScholarshipName'] . ' ' . $s['Description'])) ?>">

                        <td>
                            <span class="scholarship-id">#<?= htmlspecialchars($s['ScholarshipID']) ?></span>
                        </td>

                        <td>
                            <span class="scholarship-name"><?= htmlspecialchars($s['ScholarshipName']) ?></span>
                        </td>

                        <td class="desc-cell" title="<?= htmlspecialchars($s['Description']) ?>">
                            <?= htmlspecialchars(strlen($s['Description']) > 100 ? substr($s['Description'], 0, 100) . '…' : $s['Description']) ?>
                        </td>

                        <td>
                            <span class="expiry-badge <?= $expired ? 'expired' : '' ?>">
                                <?= $expired ? '⚠️' : '📅' ?> <?= htmlspecialchars($s['ExpiryDate']) ?>
                            </span>
                        </td>

                        <td>
                            <span class="organizer-chip">
                                🏢 <?= htmlspecialchars($s['OrganizerName'] ?? 'Unknown') ?>
                            </span>
                        </td>

                        <td>
                            <form action="<?= url('admin-delete-scholarship') ?>" method="POST"
                                  onsubmit="return confirm('Delete this scholarship?');"
                                  style="margin:0;">
                                <input type="hidden" name="ScholarshipID" value="<?= $s['ScholarshipID'] ?>">
                                <button type="submit" class="btn-delete">
                                    🗑 Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <div class="icon">📚</div>
            <p>No scholarships found on the platform yet.</p>
        </div>
        <?php endif; ?>

    </main>

</div>

<script>
    function filterTable() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#scholarshipTable tbody tr');
        rows.forEach(row => {
            const text = row.dataset.search || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }
</script>

</body>
</html>