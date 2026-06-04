<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

if (!$user || strtolower(trim($user['Role'])) !== 'admin') {
    header('Location:' . url('create'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students — Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --green-deep:   #1a3a2a;
            --green-dark:   #1f4432;
            --green-mid:    #2d6a4f;
            --green-accent: #40916c;
            --green-light:  #74c69d;
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

        /* ── SEARCH BAR ── */
        .search-wrap {
            position: relative;
            max-width: 320px;
            margin-bottom: 20px;
        }

        .search-wrap span {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: var(--text-muted);
        }

        .search-wrap input {
            width: 100%;
            padding: 9px 14px 9px 38px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-card);
            color: var(--text-main);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-wrap input:focus {
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
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

        tbody tr {
            transition: background 0.15s;
            cursor: pointer;
        }

        tbody tr:hover { background: #f5faf7; }

        /* Avatar */
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

        .student-cell { display: flex; align-items: center; }

        .student-info .name { font-weight: 600; font-size: 13.5px; }
        .student-info .email { font-size: 12px; color: var(--text-muted); margin-top: 1px; }

        /* Delete button */
        .btn-delete {
            display: inline-block;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid #fca5a5;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-delete:hover { background: #fecaca; transform: translateY(-1px); }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 56px 24px;
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .empty-state .icon { font-size: 44px; margin-bottom: 14px; opacity: 0.4; }
        .empty-state p { color: var(--text-muted); font-size: 15px; }

        /* Hidden row for search */
        .student-row.hidden-row { display: none; }
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
            <a href="<?= url('admin-students') ?>" class="active">
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

    <!-- MAIN -->
    <main class="main">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Students</h1>
                <p>View and manage all registered students on the platform.</p>
            </div>
            <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Administrator</p>
        </div>

        <!-- Search -->
        <div class="search-wrap">
            <span>🔍</span>
            <input type="text" id="searchInput" placeholder="Search students..." oninput="filterStudents()">
        </div>

        <!-- Section Header -->
        <div class="section-header">
            <div class="section-title">
                🎓 Student List
                <?php if (!empty($students)): ?>
                    <span class="count-pill"><?= count($students) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($students)): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="studentTable">
                        <?php foreach ($students as $s): ?>
                            <tr class="student-row"
                                data-name="<?= strtolower(esc($s['Username'])) ?>"
                                data-email="<?= strtolower(esc($s['Email'])) ?>"
                                onclick="window.location='<?= url('admin-student-view/' . $s['userID']) ?>'">

                                <td>
                                    <div class="student-cell">
                                        <div class="avatar"><?= strtoupper(substr($s['Username'], 0, 1)) ?></div>
                                        <div class="student-info">
                                            <div class="name"><?= esc($s['Username']) ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td style="color:var(--text-muted); font-size:13px;">
                                    <?= esc($s['Email']) ?>
                                </td>

                                <td onclick="event.stopPropagation();">
                                    <form method="POST" action="<?= url('delete-student') ?>"
                                          onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        <input type="hidden" name="userID" value="<?= $s['userID'] ?>">
                                        <button type="submit" class="btn-delete">✕ Delete</button>
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div id="noResults" style="display:none;" class="empty-state" style="margin-top:16px;">
                <div class="icon">🔍</div>
                <p>No students match your search.</p>
            </div>

        <?php else: ?>
            <div class="empty-state">
                <div class="icon">🎓</div>
                <p>No students registered yet.</p>
            </div>
        <?php endif; ?>

    </main>
</div>

<script>
    function filterStudents() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.student-row');
        let visible = 0;

        rows.forEach(row => {
            const match = (row.dataset.name + row.dataset.email).includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        const noRes = document.getElementById('noResults');
        if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
    }
</script>

</body>
</html>