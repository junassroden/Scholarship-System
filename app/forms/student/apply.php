<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

$user = $_SESSION['user'] ?? null;

if (!$user || $user['Role'] !== 'student') {
    header('Location: ' . url('/create'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Scholarship</title>
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background: #f3f6f4; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 260px; height: 100%;
            background: #1f4432;
            color: white;
            padding: 24px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 50;
            display: flex;
            flex-direction: column;
        }
        .sidebar:hover { transform: translateX(0); }
        .sidebar::after {
            content: "☰";
            position: absolute;
            right: -38px; top: 50%;
            transform: translateY(-50%);
            background: #1f4432;
            color: white;
            padding: 10px 12px;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            box-shadow: 2px 4px 12px rgba(0,0,0,0.25);
            font-size: 16px;
        }
        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            color: rgba(255,255,255,0.85);
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }
        .sidebar nav a:hover  { background: rgba(255,255,255,0.12); color: white; }
        .sidebar nav a.active { background: rgba(255,255,255,0.18); color: white; font-weight: 600; }

        /* ── File upload zone ── */
        .file-zone {
            border: 2px dashed #d1d5db;
            border-radius: 14px;
            padding: 20px 18px;
            background: #f9fafb;
            transition: border-color 0.2s, background 0.2s;
            cursor: pointer;
            position: relative;
        }
        .file-zone:hover, .file-zone.dragover {
            border-color: #1f4432;
            background: #edf5f0;
        }
        .file-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .file-zone .zone-label {
            display: flex;
            align-items: center;
            gap: 12px;
            pointer-events: none;
        }
        .file-zone .zone-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: #d4e9dd;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .file-zone .zone-text { font-size: 13.5px; color: #374151; font-weight: 600; }
        .file-zone .zone-hint { font-size: 12px; color: #9ca3af; margin-top: 2px; }
        .file-zone .zone-chosen { font-size: 12px; color: #1f4432; font-weight: 600; margin-top: 4px; display: none; }

        /* ── Deadline badge ── */
        .deadline-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef3c7;
            color: #92400e;
            padding: 5px 14px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
        }

        /* ── Expired banner ── */
        .expired-banner {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 16px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .expired-icon {
            width: 48px; height: 48px;
            background: #fca5a5;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        /* ── Submit button ── */
        .submit-btn {
            width: 100%;
            background: #1f4432;
            color: white;
            padding: 13px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            margin-top: 8px;
        }
        .submit-btn:hover { background: #163324; }
        .submit-btn:active { transform: scale(0.98); }
    </style>
</head>

<body>
<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 style="font-size:18px;font-weight:800;margin-bottom:28px;letter-spacing:-0.01em;">🎓 Student Panel</h2>

        <nav style="display:flex;flex-direction:column;gap:4px;">
            <a href="<?= url('student-dashboard') ?>" class="active">📚 Scholarships</a>
            <a href="<?= url('student-profile') ?>">👤 My Profile</a>
            <a href="<?= url('applications') ?>">📄 My Applications</a>
            <a href="<?= url('application-history') ?>">📊 History</a>
        </nav>

        <div style="margin-top:auto;padding-top:20px;border-top:1px solid rgba(255,255,255,0.18);">
            <a href="<?= url('logout') ?>"
               style="display:block;text-align:center;background:#facc15;color:#111;padding:10px;border-radius:12px;font-weight:700;font-size:14px;text-decoration:none;">
                Logout
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main style="flex:1;padding:40px 40px 60px;max-width:100%;margin:0 auto;">

        <!-- BACK + HEADER -->
        <div style="margin-bottom:24px;">
            <a href="<?= url('student-dashboard') ?>"
               style="font-size:13px;color:blue;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px;">
                ← Back to Scholarships
            </a>
            <h1 style="font-size:26px;font-weight:800;color:#1f4432;margin:0 0 4px;">Apply for Scholarship</h1>
            <p style="color:#6b7280;font-size:14px;margin:0;">Fill out the form below to submit your application.</p>
        </div>

        <!-- SCHOLARSHIP INFO CARD -->
        <div style="background:white;border-radius:20px;box-shadow:0 2px 12px rgba(0,0,0,0.07);padding:24px 28px;margin-bottom:20px;">

            <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
                <div style="width:52px;height:52px;border-radius:14px;background:#d4e9dd;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">🎓</div>
                <div style="flex:1;min-width:200px;">
                    <h2 style="font-size:18px;font-weight:800;color:#1f4432;margin:0 0 8px;word-break:break-word;">
                        <?= htmlspecialchars($scholarship[0]['ScholarshipName']) ?>
                    </h2>
                    <p style="font-size:14px;color:#6b7280;line-height:1.65;margin:0 0 14px;word-break:break-word;"
                       class="whitespace-pre-line">
                        <?= htmlspecialchars($scholarship[0]['Description']) ?>
                    </p>
                    <span class="deadline-badge">
                        📅 Deadline: <?= htmlspecialchars($scholarship[0]['ExpiryDate']) ?>
                    </span>
                </div>
            </div>

        </div>

        <?php if (date('Y-m-d') > $scholarship[0]['ExpiryDate']): ?>

            <!-- EXPIRED BANNER -->
            <div class="expired-banner">
                <div class="expired-icon">🚫</div>
                <div>
                    <div style="font-size:16px;font-weight:800;color:#991b1b;margin-bottom:4px;">Scholarship Expired</div>
                    <div style="font-size:13px;color:#b91c1c;">This scholarship's deadline has already passed. You can no longer submit an application.</div>
                    <a href="<?= url('student-dashboard') ?>"
                       style="display:inline-block;margin-top:12px;background:#1f4432;color:white;padding:8px 20px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;">
                        Browse Other Scholarships →
                    </a>
                </div>
            </div>

        <?php else: ?>

            <!-- APPLICATION FORM -->
            <div style="background:white;border-radius:20px;box-shadow:0 2px 12px rgba(0,0,0,0.07);padding:24px 28px;">

                <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f3f4f6;">
                    <div style="width:34px;height:34px;background:#d4e9dd;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;">📎</div>
                    <div>
                        <div style="font-size:15px;font-weight:700;color:#1f4432;">Required Documents</div>
                        <div style="font-size:12px;color:#9ca3af;">Upload all 4 documents to proceed. JPG or PNG accepted.</div>
                    </div>
                </div>

                <form method="POST"
                      action="<?= url('submit-application') ?>"
                      enctype="multipart/form-data"
                      style="display:flex;flex-direction:column;gap:16px;">

                    <input type="hidden" name="scholarshipID" value="<?= $scholarship[0]['ScholarshipID'] ?>">

                    <?php
                    $docFields = [
                        ['name' => 'BirthCertificateFile', 'label' => 'Birth Certificate',         'icon' => '📋'],
                        ['name' => 'COGFile',              'label' => 'Certificate of Grades (COG)','icon' => '📊'],
                        ['name' => 'EnrollmentFile',       'label' => 'Enrollment Form',            'icon' => '📝'],
                        ['name' => 'CertificateFile',      'label' => 'Certificate of Enrollment',  'icon' => '🏫'],
                    ];
                    foreach ($docFields as $i => $doc):
                    ?>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;margin-bottom:6px;">
                            <?= $doc['label'] ?> <span style="color:#dc2626;">*</span>
                        </label>
                        <div class="file-zone" id="zone-<?= $i ?>"
                             ondragover="this.classList.add('dragover')"
                             ondragleave="this.classList.remove('dragover')"
                             ondrop="this.classList.remove('dragover')">
                            <input type="file" name="<?= $doc['name'] ?>" required
                                   onchange="showFileName(this, <?= $i ?>)">
                            <div class="zone-label">
                                <div class="zone-icon"><?= $doc['icon'] ?></div>
                                <div>
                                    <div class="zone-text">Click to upload</div>
                                    <div class="zone-hint">JPG or PNG — max 10MB</div>
                                    <div class="zone-chosen" id="chosen-<?= $i ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- CHECKLIST REMINDER -->
                    <div style="background:#f0faf4;border:1px solid #a7f3d0;border-radius:12px;padding:14px 18px;font-size:13px;color:#065f46;">
                        <strong>Before submitting, make sure:</strong>
                        <ul style="margin:8px 0 0 16px;list-style:disc;display:flex;flex-direction:column;gap:4px;">
                            <li>All files are clear and legible</li>
                            <li>Documents are recent and valid</li>
                            <li>File sizes are within the limit</li>
                        </ul>
                    </div>

                    <button type="submit" class="submit-btn">
                        Submit Application →
                    </button>

                </form>

            </div>

        <?php endif; ?>

    </main>
</div>

<script>
function showFileName(input, index) {
    const el = document.getElementById('chosen-' + index);
    if (input.files && input.files[0]) {
        el.textContent = '✓ ' + input.files[0].name;
        el.style.display = 'block';
        document.getElementById('zone-' + index).style.borderColor = '#1f4432';
        document.getElementById('zone-' + index).style.background  = '#edf5f0';
    } else {
        el.style.display = 'none';
        document.getElementById('zone-' + index).style.borderColor = '';
        document.getElementById('zone-' + index).style.background  = '';
    }
}
</script>

</body>
</html>