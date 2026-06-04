<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View File</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --green-deep:   #1a3a2a;
            --green-dark:   #1f4432;
            --green-mid:    #2d6a4f;
            --green-accent: #40916c;
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
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 48px 16px;
        }

        .file-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 820px;
            overflow: hidden;
        }

        .file-card-stripe {
            height: 4px;
            background: var(--green-dark);
        }

        .file-card-body {
            padding: 2rem;
        }

        /* Header */
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .card-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .card-header-icon {
            width: 42px; height: 42px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .card-header-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--green-deep);
            line-height: 1.2;
        }

        .card-header-text p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-card);
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: background 0.15s;
        }

        .btn-back:hover { background: #f5faf7; }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin-bottom: 24px;
        }

        /* Preview area */
        .preview-wrap {
            background: #f9fcfa;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 300px;
        }

        .preview-wrap img {
            max-height: 68vh;
            width: auto;
            border-radius: 8px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            object-fit: contain;
        }

        .filename-pill {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* Download button */
        .btn-download {
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--green-dark);
            color: white;
            border-radius: 10px;
            padding: 9px 22px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-download:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <div class="file-card">

        <div class="file-card-stripe"></div>

        <div class="file-card-body">

            <!-- Header -->
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-header-icon">📄</div>
                    <div class="card-header-text">
                        <h2>File Viewer</h2>
                        <p>Submitted document preview</p>
                    </div>
                </div>
                <a href="<?= url('view-application/' . ($_GET['application_id'] ?? '')) ?>" class="btn-back">
                    ⬅ Back
                </a>
            </div>

            <hr class="divider">

            <!-- Preview -->
            <div class="preview-wrap">
                <?php 
                $url = $_GET['path'] ?? '';
                $applicationId = $_GET['application_id'] ?? '';
                ?>
                <img src="<?= esc($url) ?>" alt="Document">

                <div class="filename-pill">
                    📎 <?= esc(basename($url)) ?>
                </div>

                <a href="<?= esc($url) ?>" download class="btn-download">
                    ⬇ Download File
                </a>
            </div>

        </div>
    </div>

</body>
</html>