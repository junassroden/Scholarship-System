<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Applicants</title>

    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">

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

        /* ── FORM CARD ── */
        .form-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 640px;
            overflow: hidden;
        }

        .form-card-stripe {
            height: 4px;
            background: var(--green-dark);
        }

        .form-card-body {
            padding: 2rem;
        }

        /* ── CARD HEADER ── */
        .card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 28px;
        }

        .card-header-left {
            display: flex;
            align-items: flex-start;
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
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ── BACK BUTTON ── */
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
            white-space: nowrap;
            font-family: 'DM Sans', sans-serif;
            transition: background 0.15s;
        }

        .btn-back:hover { background: #f5faf7; }

        /* ── FORM FIELDS ── */
        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .field input,
        .field textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: #f9fcfa;
            color: var(--text-main);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field input:focus,
        .field textarea:focus {
            outline: none;
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
            background: var(--bg-card);
        }

        .field input::placeholder,
        .field textarea::placeholder { color: var(--text-muted); }

        .field textarea { resize: none; }

        /* ── DIVIDER ── */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 24px 0;
        }

        /* ── BUTTONS ROW ── */
        .btn-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        /* ── SEND BUTTON ── */
        .btn-send {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--green-dark);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background 0.15s, transform 0.12s;
        }

        .btn-send:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <div class="form-card">

        <div class="form-card-stripe"></div>

        <div class="form-card-body">

            <!-- Card Header -->
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-header-icon">📧</div>
                    <div class="card-header-text">
                        <h2>Email Applicants</h2>
                        <p>Send a message to all students who applied to this scholarship.</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="<?= url('send-email-applicants') ?>">

                <input type="hidden" name="scholarshipID" value="<?= $id ?>">

                <div class="field">
                    <label>Subject</label>
                    <input type="text" name="subject" required
                           placeholder="Enter email subject">
                </div>

                <div class="field">
                    <label>Message</label>
                    <textarea name="message" rows="8" required
                              placeholder="Write your message here..."></textarea>
                </div>

                <hr class="divider">

                <div class="btn-row">
                    <a href="<?= url('view-applicants/' . $id) ?>" class="btn-back">
                        ⬅ Back to Applicants
                    </a>
                    <button type="submit" class="btn-send">
                        Send Email 📤
                    </button>
                </div>

            </form>

        </div>
    </div>

</body>
</html>