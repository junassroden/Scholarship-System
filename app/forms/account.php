<?php
$role = $_GET['role'] ?? 'student';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

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
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2b20 0%, #163d2c 50%, #1f4432 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* ── CARD ── */
        .register-card {
            width: 100%;
            max-width: 920px;
            background: var(--bg-card);
            border-radius: 24px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.3);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1.1fr;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            background: var(--green-dark);
            padding: 48px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel-glow {
            position: absolute;
            width: 320px; height: 320px;
            background: rgba(250,204,21,0.15);
            filter: blur(80px);
            border-radius: 50%;
            bottom: -80px; left: -80px;
            pointer-events: none;
        }

        .left-panel h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: white;
            line-height: 1.2;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .left-panel p {
            font-size: 14px;
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .feature-list {
            margin-top: 36px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: rgba(255,255,255,0.75);
        }

        .feature-dot {
            width: 8px; height: 8px;
            background: var(--yellow-gold);
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            padding: 44px 44px;
            background: var(--bg-card);
            overflow-y: auto;
            max-height: 100vh;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 24px;
            font-weight: 500;
            transition: color 0.15s;
        }

        .btn-back:hover { color: var(--green-dark); }

        .right-panel h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--green-deep);
            margin-bottom: 8px;
        }

        .right-panel .subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 28px;
            line-height: 1.4;
        }

        /* ── FLASH MESSAGES ── */
        .flash-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .flash-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #15803d;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 24px;
        }

        /* ── FORM GRID ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-grid.full { 
            grid-template-columns: 1fr; 
        }

        .form-group { 
            margin-bottom: 0; 
        }

        .form-group.span-2 { 
            grid-column: span 2; 
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
            letter-spacing: 0.03em;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: #f9fcfa;
            color: var(--text-main);
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
        }

        .form-group input[type="file"] {
            padding: 10px 16px;
            background: #f9fcfa;
            cursor: pointer;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
            background: var(--bg-card);
        }

        .form-group input::placeholder { 
            color: var(--text-muted); 
            font-size: 12px;
        }

        /* Password wrapper */
        .password-wrap { 
            position: relative; 
        }
        
        .password-wrap input { 
            padding-right: 100px; 
        }

        .toggle-pw {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            font-weight: 600;
            color: var(--green-accent);
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: color 0.15s;
        }

        .toggle-pw:hover { 
            color: var(--green-dark); 
        }

        /* ── SUBMIT BUTTON ── */
        .btn-submit {
            width: 100%;
            background: var(--yellow-gold);
            color: #111;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.12s, box-shadow 0.15s;
            box-shadow: 0 4px 14px rgba(250,204,21,0.35);
            margin-top: 8px;
        }

        .btn-submit:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(250,204,21,0.4);
        }

        .btn-submit:active { 
            transform: translateY(0); 
        }

        /* ── LOGIN LINK ── */
        .login-link {
            margin-top: 28px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .login-link a {
            color: var(--green-dark);
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover { 
            text-decoration: underline; 
        }

        /* ── HIDDEN FIELDS ── */
        .fields-hidden { 
            display: none; 
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            body { padding: 16px; }
            .register-card { 
                grid-template-columns: 1fr; 
                max-width: 100%;
            }
            .left-panel { 
                display: none; 
            }
            .right-panel { 
                padding: 32px 24px; 
                max-height: none; 
            }
            .form-grid { 
                grid-template-columns: 1fr; 
                gap: 16px;
                margin-bottom: 16px;
            }
            .form-group.span-2 { 
                grid-column: span 1; 
            }
        }

        @media (max-width: 480px) {
            body { padding: 12px; }
            .right-panel { 
                padding: 24px 20px; 
            }
            .right-panel h2 { 
                font-size: 22px; 
            }
            .form-group input,
            .form-group select {
                padding: 10px 14px;
            }
        }
    </style>
</head>

<body>

    <div class="register-card">

        <!-- LEFT PANEL -->
        <div class="left-panel">
            <h2>Join Us 🚀</h2>
            <p>Create your <?= ucfirst($role) ?> account and start your scholarship journey today.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    Browse available scholarships
                </div>
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    Track your applications
                </div>
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    Manage and post opportunities
                </div>
            </div>

            <div class="left-panel-glow"></div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">

            <a href="<?= url('create') ?>" class="btn-back">← Back</a>

            <h2>Register as <?= ucfirst($role) ?></h2>
            <p class="subtitle">Fill in the required details to create your account</p>

            <!-- ALERTS -->
            <?php if ($error = get_flash('error')): ?>
                <div class="flash-error">⚠️ <?= $error ?></div>
            <?php endif; ?>

            <?php if ($success = get_flash('success')): ?>
                <div class="flash-success">✓ <?= $success ?></div>
            <?php endif; ?>

            <!-- FORM -->
            <form action="<?= url('insert') ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <?php csrf_field(); ?>
                <input type="hidden" name="role" id="role" value="<?= $role ?>">

                <!-- COMMON FIELDS -->
                <div class="form-grid">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="Username" required placeholder="Choose a username">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="Email" required placeholder="Enter your email">
                    </div>

                    <div class="form-group span-2">
                        <label>Password</label>
                        <div class="password-wrap">
                            <input type="password" name="Password" id="password" required placeholder="Create a password">
                            <button type="button" class="toggle-pw" onclick="togglePassword()">Show</button>
                        </div>
                    </div>
                </div>

                <!-- STUDENT FIELDS -->
                <div id="studentFields" class="form-grid <?= $role !== 'student' ? 'fields-hidden' : '' ?>">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="StudentFirstName" placeholder="First name">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="StudentLastName" placeholder="Last name">
                    </div>
                    <div class="form-group span-2">
                        <label>Contact Number</label>
                        <input type="tel" name="StudentContactNumber" id="contactNumber" 
                               placeholder="Contact number (max 11 digits)"
                               maxlength="11"
                               onkeypress="return onlyNumbers(event)">
                    </div>
                </div>

                <!-- ORGANIZER FIELDS -->
                <div id="organizerFields" class="form-grid <?= $role !== 'organizers' ? 'fields-hidden' : '' ?>">
                    <div class="form-group span-2">
                        <label>Organization Name</label>
                        <input type="text" name="OrganizationName" placeholder="Organization name">
                    </div>
                    <div class="form-group">
                        <label>Organization Type</label>
                        <select name="OrganizationType">
                            <option value="">Select Type</option>
                            <option>Government</option>
                            <option>Private Company</option>
                            <option>NGO</option>
                            <option>Foundation</option>
                            <option>Startup</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="tel" name="ContactNumber" id="orgContactNumber" 
                               placeholder="Contact number (max 11 digits)"
                               maxlength="11"
                               onkeypress="return onlyNumbers(event)">
                    </div>
                    <div class="form-group">
                        <label>Document Type</label>
                        <select name="DocumentType">
                            <option value="">Select Document</option>
                            <option>Business Permit</option>
                            <option>SEC Registration</option>
                            <option>DTI Registration</option>
                            <option>Government ID</option>
                        </select>
                    </div>
                    <div class="form-group span-2">
                        <label>Upload Credential</label>
                        <input type="file" name="CredentialFile" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>

                <button type="submit" class="btn-submit">Create Account</button>

            </form>

            <div class="login-link">
                <p>Already have an account? <a href="<?= url('create') ?>">Login here</a></p>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const btn = input.nextElementSibling;
            const isHidden = input.type === "password";
            input.type = isHidden ? "text" : "password";
            btn.textContent = isHidden ? "Hide" : "Show";
        }

        function onlyNumbers(event) {
            const charCode = event.which ? event.which : event.keyCode;
            // Allow numbers (48-57), backspace (8), delete (46), tab (9), enter (13), arrows (37-40)
            if (charCode >= 48 && charCode <= 57) {
                return true;
            }
            if (charCode === 8 || charCode === 46 || charCode === 9 || charCode === 13) {
                return true;
            }
            if (charCode >= 37 && charCode <= 40) {
                return true;
            }
            return false;
        }

        function validateForm() {
            // For organizer contact number
            const orgContact = document.getElementById('orgContactNumber');
            if (orgContact && orgContact.value) {
                if (!/^\d*$/.test(orgContact.value)) {
                    alert('Please enter only numbers for contact number.');
                    orgContact.focus();
                    return false;
                }
            }
            
            // For student contact number
            const studentContact = document.getElementById('contactNumber');
            if (studentContact && studentContact.value) {
                if (!/^\d*$/.test(studentContact.value)) {
                    alert('Please enter only numbers for contact number.');
                    studentContact.focus();
                    return false;
                }
            }
            
            return true;
        }

        // Real-time cleanup for contact number fields
        document.addEventListener('DOMContentLoaded', function() {
            const contactFields = ['contactNumber', 'orgContactNumber'];
            contactFields.forEach(function(fieldId) {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', function() {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });
                }
            });
        });
    </script>

</body>
</html>