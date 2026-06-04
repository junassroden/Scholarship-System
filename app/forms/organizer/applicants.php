<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'organizers') {
    header('Location: ' . url('create'));
    exit;
}

// Get scholarship name and announcement from database using the $id
$scholarshipData = db()->table('tblScholarshipForm')
    ->where('ScholarshipID', $id)
    ->get_all();

if (!empty($scholarshipData)) {
    $scholarshipName = $scholarshipData[0]['ScholarshipName'];
    $currentAnnouncement = $scholarshipData[0]['Announcement'] ?? '';
} else {
    $scholarshipName = 'Scholarship Applicants';
    $currentAnnouncement = '';
}

// Get flash messages
$success = get_flash('success');
$error = get_flash('error');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= esc($scholarshipName) ?> - Applicants</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    
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
        .main {
            flex: 1;
            padding: 40px 48px;
            min-height: 100vh;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--green-deep);
        }

        /* ── ANNOUNCEMENT BANNER ── */
        .announcement-banner {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .announcement-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            flex: 1;
        }

        .announcement-icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .announcement-text {
            flex: 1;
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

        .announcement-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-edit-announcement {
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 12px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-edit-announcement:hover {
            background: #d97706;
        }

        .btn-delete-announcement {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 12px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-delete-announcement:hover {
            background: #fecaca;
        }

        /* Announcement Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            overflow: hidden;
            animation: modalFadeIn 0.2s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--green-deep);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-muted);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-body p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .modal-body textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            resize: vertical;
        }

        .modal-body textarea:focus {
            outline: none;
            border-color: var(--green-accent);
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-save {
            background: var(--green-dark);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-save:hover {
            background: var(--green-mid);
        }

        .btn-cancel {
            background: #e5e7eb;
            color: #374151;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            cursor: pointer;
        }

        .flash-success {
            background: #d1fae5;
            color: #065f46;
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid #a7f3d0;
        }

        .flash-error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid #fca5a5;
        }

        .btn-delete {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            transition: background 0.15s, transform 0.12s;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #fecaca;
            transform: translateY(-1px);
        }

        .btn-announcement {
            background: var(--green-dark);
            color: white;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            transition: background 0.15s, transform 0.12s;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-announcement:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
        }

        .btn-back {
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

        .btn-print {
            background: #1e40af;
            color: white;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            transition: background 0.15s, transform 0.12s;
            text-decoration: none;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
        }

        /* ── APPLICANT CARDS ── */
        .applicant-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .applicant-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--green-accent);
        }

        .applicant-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(31,68,50,0.1);
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 10px;
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

        /* Document links */
        .doc-link {
            display: block;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 8px;
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

        .doc-placeholder {
            text-align: center;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px;
            background: #f5faf7;
            color: var(--text-muted);
            font-size: 12px;
        }

        /* Action buttons */
        .btn-accept {
            background: var(--green-dark);
            color: white;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            transition: background 0.15s, transform 0.12s;
            cursor: pointer;
            border: none;
        }

        .btn-accept:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
        }

        .btn-reject {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            transition: background 0.15s, transform 0.12s;
            cursor: pointer;
        }

        .btn-reject:hover {
            background: #fecaca;
            transform: translateY(-1px);
        }

        .btn-email {
            background: var(--green-accent);
            color: white;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            transition: background 0.15s, transform 0.12s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-email:hover {
            background: var(--green-mid);
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

        /* Print styles */
        @media print {
            .sidebar,
            .sidebar::after,
            .btn-delete,
            .btn-announcement,
            .btn-back,
            .btn-print,
            .btn-accept,
            .btn-reject,
            .btn-email,
            .doc-link,
            .doc-placeholder,
            .card-header-buttons,
            .announcement-banner {
                display: none !important;
            }

            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .main {
                padding: 20px;
                margin: 0;
            }

            .applicant-card {
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ddd;
                margin-bottom: 20px;
            }

            .applicant-card::before {
                background: #1f4432;
                print-color-adjust: exact;
            }

            .status-badge {
                print-color-adjust: exact;
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main { padding: 30px 32px; }
            .grid-cols-2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .main { padding: 20px 16px; }
        }
    </style>
</head>

<body>

<div style="display: flex; min-height: 100vh;">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🎓</div>
            <span class="sidebar-logo-text">Organizer Panel</span>
        </div>

        <nav>
            <a href="<?= url('organizer-dashboard') ?>">
                <span class="nav-icon">🏠</span> Dashboard
            </a>
            <a href="<?= url('create-scholarship-page') ?>">
                <span class="nav-icon">➕</span> Create Scholarship
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= url('logout') ?>" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main">
        <div style="max-width: 1400px; margin: 0 auto;">

            <!-- Header -->
            <div class="page-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <h1><?= esc($scholarshipName) ?> - Applicants 📄</h1>

                    <!-- Delete Scholarship -->
                    <form method="POST" action="<?= url('delete-scholarship') ?>" style="display: inline;">
                        <input type="hidden" name="scholarshipID" value="<?= $id ?>">
                        <button type="submit" class="btn-delete">✖ Delete Scholarship</button>
                    </form>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;" class="card-header-buttons">
                    <button onclick="printApplicants('<?= esc($scholarshipName) ?>')" class="btn-print">🖨️ Print List</button>
                    <button onclick="openAnnouncementModal()" class="btn-announcement">📢 Announcement</button>
                    <a href="<?= url('email-applicants/' . $id) ?>" class="btn-announcement">📧 Email All</a>
                    <a href="<?= url('organizer-dashboard') ?>" class="btn-back">⬅ Back</a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($success): ?>
                <div class="flash-success">✅ <?= esc($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="flash-error">❌ <?= esc($error) ?></div>
            <?php endif; ?>

            <!-- Announcement Banner -->
            <?php if (!empty($currentAnnouncement)): ?>
                <div class="announcement-banner">
                    <div class="announcement-content">
                        <div class="announcement-icon">📢</div>
                        <div class="announcement-text">
                            <div class="announcement-title">Message from Organizer</div>
                            <div class="announcement-message"><?= nl2br(esc($currentAnnouncement)) ?></div>
                        </div>
                    </div>
                    <div class="announcement-actions">
                        <button onclick="openEditAnnouncementModal()" class="btn-edit-announcement">✏️ Edit</button>
                        <form method="POST" action="<?= url('delete-announcement/' . $id) ?>" style="display: inline;">
                            <button type="submit" class="btn-delete-announcement">🗑 Remove</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Applicants Grid -->
            <?php if (!empty($applications)): ?>
                <div class="grid grid-cols-2 gap-6" id="applicantsGrid">
                    <?php foreach ($applications as $a): ?>
                        <div class="applicant-card" 
                             data-name="<?= esc(($a['FirstName'] ?? '') . ' ' . ($a['LastName'] ?? '')) ?>"
                             data-email="<?= esc($a['Email'] ?? '') ?>"
                             data-contact="<?= esc($a['ContactNumber'] ?? '') ?>"
                             data-gender="<?= esc($a['Gender'] ?? '') ?>"
                             data-birthdate="<?= esc($a['Birthdate'] ?? '') ?>"
                             data-applied="<?= esc($a['application_date'] ?? '') ?>"
                             data-status="<?= esc($a['application_status'] ?? 'ongoing') ?>"
                             data-remarks="<?= esc($a['remarks'] ?? '') ?>">
                            <div style="padding: 20px;">

                                <!-- Student Header -->
                                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 600; color: #1f4432; text-transform: uppercase;">
                                        <?= strtoupper(substr(($a['FirstName'] ?? '')[0], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h3 style="font-weight: 700; font-size: 16px; color: var(--green-deep);"><?= esc($a['FirstName'] ?? '') ?> <?= esc($a['LastName'] ?? '') ?></h3>
                                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;"><?= esc($a['Email'] ?? 'N/A') ?></p>
                                    </div>
                                </div>

                                <!-- Info Grid -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; font-size: 13px;">
                                    <div><strong style="color: var(--text-muted);">Contact:</strong> <span class="applicant-contact"><?= esc($a['ContactNumber'] ?? 'N/A') ?></span></div>
                                    <div><strong style="color: var(--text-muted);">Gender:</strong> <?= esc($a['Gender'] ?? 'N/A') ?></div>
                                    <div><strong style="color: var(--text-muted);">Birthdate:</strong> <?= esc($a['Birthdate'] ?? 'N/A') ?></div>
                                    <div><strong style="color: var(--text-muted);">Applied:</strong> <span class="applicant-date"><?= esc($a['application_date'] ?? 'N/A') ?></span></div>
                                    <div><strong style="color: var(--text-muted);">Status:</strong>
                                        <span class="status-badge <?= 
                                            ($a['application_status'] ?? '') === 'approved' ? 'status-approved' : 
                                            (($a['application_status'] ?? '') === 'rejected' ? 'status-rejected' : 'status-ongoing')
                                        ?>">
                                            <?= esc(ucfirst($a['application_status'] ?? 'ongoing')) ?>
                                        </span>
                                    </div>
                                    <div><strong style="color: var(--text-muted);">Remarks:</strong> <?= esc($a['remarks'] ?? 'N/A') ?></div>
                                </div>

                                <!-- Documents -->
                                <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-bottom: 16px;">
                                    <h4 style="font-weight: 600; font-size: 12px; color: var(--green-deep); margin-bottom: 10px;">Submitted Documents</h4>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                        <?php
                                        $docs = [
                                            'Birth Certificate' => $a['BirthCertificateFile'] ?? '',
                                            'Certificate of Grades' => $a['COGFile'] ?? '',
                                            'Enrollment Form' => $a['EnrollmentFile'] ?? '',
                                            'Certificate of Enrollment' => $a['CertificateFile'] ?? '',
                                        ];
                                        foreach ($docs as $label => $filePath):
                                            if (!empty($filePath)):
                                        ?>
                                            <a href="<?= url('view-file?path=' . urlencode($filePath)) ?>" class="doc-link" target="_blank">📄 <?= esc($label) ?></a>
                                        <?php else: ?>
                                            <div class="doc-placeholder"><?= esc($label) ?><br>(Not submitted)</div>
                                        <?php endif; endforeach; ?>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div style="border-top: 1px solid var(--border); padding-top: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
                                    <?php if (($a['application_status'] ?? 'ongoing') === 'ongoing'): ?>
                                        <form method="POST" action="<?= url('update-application-status') ?>" style="display: inline;">
                                            <input type="hidden" name="application_id" value="<?= $a['applicationID'] ?>">
                                            <input type="hidden" name="scholarshipID" value="<?= $id ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn-accept">✅ Accept</button>
                                        </form>
                                        <form method="POST" action="<?= url('update-application-status') ?>" style="display: inline;">
                                            <input type="hidden" name="application_id" value="<?= $a['applicationID'] ?>">
                                            <input type="hidden" name="scholarshipID" value="<?= $id ?>">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn-reject">❌ Reject</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="<?= url('email-applicant/' . $a['applicationID']) ?>" class="btn-email">📧 Email Applicant</a>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <p>No applicants yet.</p>
                    <p style="font-size: 13px; margin-top: 6px;">Check back later for student applications.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

</div>

<!-- Announcement Modal -->
<div id="announcementModal" class="modal">
    <div class="modal-content">
        <form method="POST" action="<?= url('save-announcement/' . $id) ?>">
            <div class="modal-header">
                <h3>Scholarship Announcement</h3>
                <button type="button" class="modal-close" onclick="closeAnnouncementModal()">✕</button>
            </div>
            <div class="modal-body">
                <p>Create a message that will be visible to all applicants of this scholarship.</p>
                <textarea name="announcement" id="announcementText" rows="5" placeholder="Type your announcement here... (e.g., Deadline extension, interview schedule, document requirements update, etc.)"><?= esc($currentAnnouncement) ?></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeAnnouncementModal()">Cancel</button>
                <button type="submit" name="save_announcement" class="btn-save">Save Announcement</button>
            </div>
        </form>
    </div>
</div>

<script>
function printApplicants(scholarshipName) {
    const currentDate = new Date().toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    // Collect data from all applicant cards
    const cards = document.querySelectorAll('.applicant-card');
    let applicantsData = [];
    let approved = 0, ongoing = 0, rejected = 0;
    
    cards.forEach(card => {
        const name = card.querySelector('h3')?.innerText || 'N/A';
        const email = card.querySelector('p')?.innerText || 'N/A';
        const contact = card.querySelector('.applicant-contact')?.innerText || 'N/A';
        const appliedDate = card.querySelector('.applicant-date')?.innerText || 'N/A';
        let statusText = 'Ongoing';
        
        if (card.querySelector('.status-approved')) {
            statusText = 'Approved';
            approved++;
        } else if (card.querySelector('.status-rejected')) {
            statusText = 'Rejected';
            rejected++;
        } else {
            ongoing++;
        }
        
        applicantsData.push({ name, email, contact, appliedDate, statusText });
    });
    
    const total = applicantsData.length;
    
    // Create print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${escapeHtml(scholarshipName)} - Applicants List</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'DM Sans', 'Segoe UI', Arial, sans-serif;
                    padding: 40px;
                    background: white;
                    color: #1a2e22;
                }
                .print-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #1f4432;
                }
                .print-header h1 {
                    font-family: 'Playfair Display', serif;
                    font-size: 32px;
                    color: #1a3a2a;
                    margin-bottom: 8px;
                }
                .print-header p {
                    color: #6b7c6f;
                    font-size: 14px;
                }
                .print-date {
                    font-size: 12px;
                    color: #6b7280;
                    margin-top: 10px;
                }
                .summary {
                    background: #eef3f0;
                    padding: 20px;
                    border-radius: 12px;
                    margin-bottom: 30px;
                    display: flex;
                    justify-content: space-around;
                    flex-wrap: wrap;
                    gap: 20px;
                }
                .summary-item {
                    text-align: center;
                    flex: 1;
                    min-width: 100px;
                }
                .summary-label {
                    font-size: 11px;
                    font-weight: 600;
                    text-transform: uppercase;
                    color: #6b7c6f;
                    letter-spacing: 0.05em;
                    margin-bottom: 8px;
                }
                .summary-value {
                    font-size: 32px;
                    font-weight: 700;
                    color: #1a3a2a;
                }
                .summary-value.approved { color: #065f46; }
                .summary-value.ongoing { color: #1e40af; }
                .summary-value.rejected { color: #991b1b; }
                
                .applicants-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .applicants-table th {
                    background: #1f4432;
                    color: white;
                    padding: 12px 15px;
                    text-align: left;
                    font-size: 11px;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                }
                .applicants-table td {
                    padding: 12px 15px;
                    border-bottom: 1px solid #d8e6de;
                    font-size: 13px;
                    vertical-align: middle;
                }
                .applicants-table tr:last-child td {
                    border-bottom: none;
                }
                .applicants-table tr:hover {
                    background: #f5faf7;
                }
                .status-badge {
                    display: inline-block;
                    padding: 4px 12px;
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
                .footer {
                    margin-top: 30px;
                    padding-top: 20px;
                    text-align: center;
                    font-size: 11px;
                    color: #9ca3af;
                    border-top: 1px solid #e5e7eb;
                }
                @media print {
                    body {
                        padding: 20px;
                    }
                    .summary {
                        break-inside: avoid;
                    }
                    .applicants-table tr {
                        break-inside: avoid;
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h1>${escapeHtml(scholarshipName)}</h1>
                <p>List of Applicants</p>
                <div class="print-date">Generated on: ${currentDate}</div>
            </div>
            
            <div class="summary">
                <div class="summary-item">
                    <div class="summary-label">Total Applicants</div>
                    <div class="summary-value">${total}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Approved</div>
                    <div class="summary-value approved">${approved}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Ongoing</div>
                    <div class="summary-value ongoing">${ongoing}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Rejected</div>
                    <div class="summary-value rejected">${rejected}</div>
                </div>
            </div>
            
            <table class="applicants-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `);
    
    applicantsData.forEach((applicant, index) => {
        let statusClass = 'status-ongoing';
        if (applicant.statusText === 'Approved') statusClass = 'status-approved';
        else if (applicant.statusText === 'Rejected') statusClass = 'status-rejected';
        
        printWindow.document.write(`
            <tr>
                <td>${index + 1}</td>
                <td><strong>${escapeHtml(applicant.name)}</strong></td>
                <td>${escapeHtml(applicant.email)}</td>
                <td>${escapeHtml(applicant.contact)}</td>
                <td>${escapeHtml(applicant.appliedDate)}</td>
                <td><span class="status-badge ${statusClass}">${applicant.statusText}</span></td>
            </tr>
        `);
    });
    
    printWindow.document.write(`
                </tbody>
            </table>
            <div class="footer">
                This is an official document generated from the Scholarship Management System.
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function openAnnouncementModal() {
    // Clear the textarea first for new announcement
    document.getElementById('announcementText').value = '';
    document.getElementById('announcementModal').classList.add('show');
}

function openEditAnnouncementModal() {
    // Get the current announcement text from the banner
    const announcementMessage = document.querySelector('.announcement-message')?.innerText || '';
    const textarea = document.getElementById('announcementText');
    if (textarea) {
        textarea.value = announcementMessage;
    }
    document.getElementById('announcementModal').classList.add('show');
}

function closeAnnouncementModal() {
    document.getElementById('announcementModal').classList.remove('show');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('announcementModal');
    if (event.target === modal) {
        closeAnnouncementModal();
    }
}
</script>

</body>
</html>