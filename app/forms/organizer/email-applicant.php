<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Applicant</title>

    <!-- ✅ Tailwind CDN -->
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

        /* ── CARD ── */
        .max-w-2xl.mx-auto.bg-white.rounded-3xl {
            background: var(--bg-card) !important;
            border-radius: 16px !important;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important;
            padding: 0 !important;
            overflow: hidden;
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            position: relative;
        }

        /* Green top accent bar */
        .max-w-2xl.mx-auto.bg-white.rounded-3xl::before {
            content: '';
            display: block;
            height: 4px;
            background: var(--green-dark);
        }

        /* Inner padding wrapper */
        .max-w-2xl.mx-auto.bg-white.rounded-3xl > * {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .max-w-2xl.mx-auto.bg-white.rounded-3xl > h2 {
            padding-top: 2rem;
        }

        .max-w-2xl.mx-auto.bg-white.rounded-3xl > form {
            padding-bottom: 2rem;
        }

        /* ── HEADING ── */
        h2.text-2xl.font-extrabold {
            font-family: 'Playfair Display', serif !important;
            font-size: 22px !important;
            font-weight: 700 !important;
            color: var(--green-deep) !important;
            margin-bottom: 24px !important;
        }

        /* ── LABELS ── */
        label.block.text-sm.font-semibold.text-gray-700 {
            color: var(--text-main) !important;
            font-size: 13px !important;
            font-weight: 600;
            margin-bottom: 6px !important;
        }

        /* ── INPUTS & TEXTAREA ── */
        input[type="text"],
        textarea {
            width: 100%;
            border: 1px solid var(--border) !important;
            border-radius: 10px !important;
            padding: 10px 16px !important;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: #f9fcfa;
            color: var(--text-main);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none !important;
            border-color: var(--green-accent) !important;
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12) !important;
            background: var(--bg-card);
        }

        input[type="text"]::placeholder,
        textarea::placeholder {
            color: var(--text-muted);
        }

        textarea { resize: none; }

        /* ── BACK BUTTON ── */
        a.bg-gray-300 {
            background: var(--bg-card) !important;
            color: var(--text-muted) !important;
            border: 1px solid var(--border) !important;
            border-radius: 10px !important;
            padding: 9px 20px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            transition: background 0.15s;
            text-decoration: none;
        }

        a.bg-gray-300:hover {
            background: #f5faf7 !important;
        }

        /* ── SEND BUTTON ── */
        button[type="submit"] {
            background: var(--green-dark) !important;
            border-radius: 10px !important;
            padding: 9px 20px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            transition: background 0.15s, transform 0.12s;
        }

        button[type="submit"]:hover {
            background: var(--green-mid) !important;
            transform: translateY(-1px);
        }

        /* space-y-6 spacing */
        .space-y-6 > * + * { margin-top: 1.5rem; }
    </style>
</head>
<body class="bg-gray-100">

<div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-md p-8 mt-10">
    <h2 class="text-2xl font-extrabold text-[#1f4432] mb-6">
        📧 Email Applicant
    </h2>

    <form method="POST" action="<?= url('send-email-applicant') ?>" class="space-y-6">
        <input type="hidden" name="applicationID" value="<?= $applicant['applicationID'] ?>">
        <input type="hidden" name="scholarshipID" value="<?= $applicant['scholarshipID'] ?>">

        <!-- Subject -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
            <input type="text" name="subject" required
                   class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-[#1f4432]"
                   placeholder="Enter email subject">
        </div>

        <!-- Message -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
            <textarea name="message" rows="8" required
                      class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-[#1f4432]"
                      placeholder="Write your message here..."></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-between">
            <a href="<?= url('view-applicants/' . $applicant['scholarshipID']) ?>"
               class="bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold hover:bg-gray-400 transition">
                ⬅ Back to Applicants
            </a>

            <button type="submit"
                    class="bg-[#1f4432] text-white px-6 py-3 rounded-xl font-semibold hover:bg-[#163524] transition">
                Send Email 📤
            </button>
        </div>
    </form>
</div>

</body>
</html>