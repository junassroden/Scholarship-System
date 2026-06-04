<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['Role'];
    if ($role === 'admin') {
        header("Location: " . url('admin-dashboard'));
        exit;
    } elseif ($role === 'organizers') {
        header("Location: " . url('organizer-dashboard'));
        exit;
    } elseif ($role === 'student') {
        header("Location: " . url('student-dashboard'));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship System</title>
    <link rel="stylesheet" href="<?= base_url() ?>public/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/design/styles.css">
	<link rel="icon" type="image/png" href="<?= base_url() ?>public/resources/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800 scroll-smooth">

    <!-- NAVBAR -->
    <div id="navbar" class="bg-[#1f4432] sticky top-0 z-50 shadow-lg px-6 py-3 transition-all">
        <nav class="flex justify-between items-center max-w-7xl mx-auto">
            <img src="public/resources/logo.png" class="h-10">
            <div class="hidden md:flex gap-8 text-white font-semibold">
                <a href="#">Home</a>
                <a href="#about">About</a>
                <a href="#benefits">Benefits</a>
                <a href="#faq">FAQ</a>
            </div>
            <a href="<?= url('/create') ?>"
                class="btn bg-yellow-400 px-5 py-2 rounded-xl font-bold hover:scale-105 transition">
                Login
            </a>
        </nav>
    </div>

    <!-- HERO -->
    <div class="relative h-screen flex items-center justify-center bg-cover"
        style="background-image:url('public/resources/kapitolyo.jpg')">

        <div class="absolute inset-0 bg-black/70"></div>

        <div class="relative text-center text-white px-6 fade-up">
            <img src="public/resources/logo.png" class="w-32 mx-auto mb-6">

            <h1 class="text-5xl md:text-6xl font-extrabold mb-4">
                Empower Your Future
            </h1>

            <p class="text-gray-300 max-w-xl mx-auto mb-8">
                A modern scholarship platform designed to support, uplift, and empower students.
            </p>

            <div class="flex gap-4 justify-center">
                <a href="#about"
                    class="btn bg-yellow-400 text-black px-6 py-3 rounded-xl font-bold hover:scale-105 transition">
                    Explore
                </a>
                <a href="<?= url('/create') ?>" class="btn border px-6 py-3 rounded-xl hover:bg-white/10 transition">
                    Sign In
                </a>
            </div>
        </div>
    </div>

    <!-- ABOUT -->
    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Heading -->
            <div class="text-center mb-20 fade-up">
                <h2 class="text-4xl md:text-5xl font-extrabold text-[#1f4432]">
                    About Our Program
                </h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto text-lg">
                    We are committed to empowering students through accessible, transparent, and impactful scholarship
                    opportunities.
                </p>
            </div>

            <!-- Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-10">

                <!-- CARD -->
                <div
                    class="fade-up bg-gray-50 p-10 rounded-3xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="bg-[#1f4432]/10 text-[#1f4432] p-5 rounded-2xl text-3xl">
                            🎓
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-[#1f4432] mb-3">Academic Excellence</h3>
                    <p class="text-gray-600">
                        Supporting students who demonstrate strong academic performance and dedication.
                    </p>
                </div>

                <!-- CARD -->
                <div
                    class="fade-up bg-gray-50 p-10 rounded-3xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="bg-[#1f4432]/10 text-[#1f4432] p-5 rounded-2xl text-3xl">
                            💰
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-[#1f4432] mb-3">Financial Assistance</h3>
                    <p class="text-gray-600">
                        Providing financial support to reduce the burden of educational expenses.
                    </p>
                </div>

                <!-- CARD -->
                <div
                    class="fade-up bg-gray-50 p-10 rounded-3xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="bg-[#1f4432]/10 text-[#1f4432] p-5 rounded-2xl text-3xl">
                            🌱
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-[#1f4432] mb-3">Leadership Growth</h3>
                    <p class="text-gray-600">
                        Developing leadership skills to prepare scholars for future responsibilities.
                    </p>
                </div>

                <!-- CARD -->
                <div
                    class="fade-up bg-gray-50 p-10 rounded-3xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="bg-[#1f4432]/10 text-[#1f4432] p-5 rounded-2xl text-3xl">
                            🤝
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-[#1f4432] mb-3">Community Engagement</h3>
                    <p class="text-gray-600">
                        Encouraging scholars to contribute positively to their communities.
                    </p>
                </div>

                <!-- CARD -->
                <div
                    class="fade-up bg-gray-50 p-10 rounded-3xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="bg-[#1f4432]/10 text-[#1f4432] p-5 rounded-2xl text-3xl">
                            ⚖️
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-[#1f4432] mb-3">Equal Opportunity</h3>
                    <p class="text-gray-600">
                        Ensuring fair access to scholarship opportunities for all qualified students.
                    </p>
                </div>

                <!-- CARD -->
                <div
                    class="fade-up bg-gray-50 p-10 rounded-3xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="bg-[#1f4432]/10 text-[#1f4432] p-5 rounded-2xl text-3xl">
                            🚀
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-[#1f4432] mb-3">Future Success</h3>
                    <p class="text-gray-600">
                        Preparing students for long-term career success and personal growth.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- BENEFITS -->
    <section id="benefits"
        class="py-28 bg-gradient-to-br from-[#163d2c] via-[#1f4432] to-[#0f2b20] text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Heading -->
            <div class="text-center mb-20 fade-up">
                <h2 class="text-4xl md:text-5xl font-extrabold mb-4">
                    Why Choose Us
                </h2>
                <p class="text-gray-300 max-w-2xl mx-auto">
                    Experience a modern, efficient, and transparent scholarship system designed to support your journey.
                </p>
            </div>

            <!-- Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-10">

                <!-- CARD -->
                <div class="fade-up group bg-white/10 backdrop-blur-lg border border-white/10 p-10 rounded-3xl 
                        hover:-translate-y-2 hover:shadow-2xl transition duration-300 text-center">

                    <div class="flex justify-center mb-6">
                        <div
                            class="bg-yellow-400 text-black p-4 rounded-2xl text-2xl shadow-lg group-hover:scale-110 transition">
                            ⚡
                        </div>
                    </div>

                    <h3 class="text-xl font-bold mb-3">Fast Approval</h3>
                    <p class="text-gray-300">
                        Efficient and streamlined processes ensure quick application review and approval.
                    </p>
                </div>

                <!-- CARD -->
                <div class="fade-up group bg-white/10 backdrop-blur-lg border border-white/10 p-10 rounded-3xl 
                        hover:-translate-y-2 hover:shadow-2xl transition duration-300 text-center">

                    <div class="flex justify-center mb-6">
                        <div
                            class="bg-yellow-400 text-black p-4 rounded-2xl text-2xl shadow-lg group-hover:scale-110 transition">
                            🔒
                        </div>
                    </div>

                    <h3 class="text-xl font-bold mb-3">Secure System</h3>
                    <p class="text-gray-300">
                        Advanced security measures protect your personal and academic data at all times.
                    </p>
                </div>

                <!-- CARD -->
                <div class="fade-up group bg-white/10 backdrop-blur-lg border border-white/10 p-10 rounded-3xl 
                        hover:-translate-y-2 hover:shadow-2xl transition duration-300 text-center">

                    <div class="flex justify-center mb-6">
                        <div
                            class="bg-yellow-400 text-black p-4 rounded-2xl text-2xl shadow-lg group-hover:scale-110 transition">
                            🌍
                        </div>
                    </div>

                    <h3 class="text-xl font-bold mb-3">Inclusive Access</h3>
                    <p class="text-gray-300">
                        Open to all qualified students, ensuring equal opportunity regardless of background.
                    </p>
                </div>

                <!-- CARD -->
                <div class="fade-up group bg-white/10 backdrop-blur-lg border border-white/10 p-10 rounded-3xl 
                        hover:-translate-y-2 hover:shadow-2xl transition duration-300 text-center">

                    <div class="flex justify-center mb-6">
                        <div
                            class="bg-yellow-400 text-black p-4 rounded-2xl text-2xl shadow-lg group-hover:scale-110 transition">
                            📊
                        </div>
                    </div>

                    <h3 class="text-xl font-bold mb-3">Transparent Tracking</h3>
                    <p class="text-gray-300">
                        Monitor your application status in real-time with full transparency.
                    </p>
                </div>

                <!-- CARD -->
                <div class="fade-up group bg-white/10 backdrop-blur-lg border border-white/10 p-10 rounded-3xl 
                        hover:-translate-y-2 hover:shadow-2xl transition duration-300 text-center">

                    <div class="flex justify-center mb-6">
                        <div
                            class="bg-yellow-400 text-black p-4 rounded-2xl text-2xl shadow-lg group-hover:scale-110 transition">
                            🎯
                        </div>
                    </div>

                    <h3 class="text-xl font-bold mb-3">Merit-Based Selection</h3>
                    <p class="text-gray-300">
                        Fair evaluation based on academic performance and qualifications.
                    </p>
                </div>

                <!-- CARD -->
                <div class="fade-up group bg-white/10 backdrop-blur-lg border border-white/10 p-10 rounded-3xl 
                        hover:-translate-y-2 hover:shadow-2xl transition duration-300 text-center">

                    <div class="flex justify-center mb-6">
                        <div
                            class="bg-yellow-400 text-black p-4 rounded-2xl text-2xl shadow-lg group-hover:scale-110 transition">
                            🚀
                        </div>
                    </div>

                    <h3 class="text-xl font-bold mb-3">Future Opportunities</h3>
                    <p class="text-gray-300">
                        Unlock career growth, networking, and long-term success through our programs.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-24 bg-white">
        <div class="max-w-4xl mx-auto px-6">

            <h2 class="text-4xl font-extrabold text-center text-[#1f4432] mb-10 fade-up">FAQ</h2>

            <div class="space-y-4">

                <!-- ITEM -->
                <div class="fade-up bg-gray-100 p-5 rounded-xl cursor-pointer accordion">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Who can apply?</span>
                        <span>+</span>
                    </div>
                    <div class="accordion-content mt-2 text-gray-600">
                        Students who meet academic and financial requirements.
                    </div>
                </div>

                <div class="fade-up bg-gray-100 p-5 rounded-xl cursor-pointer accordion">
                    <div class="flex justify-between">
                        <span class="font-semibold">How long is approval?</span>
                        <span>+</span>
                    </div>
                    <div class="accordion-content mt-2 text-gray-600">
                        Typically 2–4 weeks after submission.
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= FAQ ================= -->
    <section id="faq" class="py-24 bg-white">
        <div class="max-w-5xl mx-auto px-6">

            <h2 class="text-4xl font-extrabold text-center text-[#1f4432] mb-12 fade-up">
                Frequently Asked Questions
            </h2>

            <div class="space-y-5">

                <!-- ITEM -->
                <div class="fade-up bg-gray-100 p-6 rounded-2xl cursor-pointer accordion">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-lg">Who can apply for the scholarship?</span>
                        <span class="text-xl">+</span>
                    </div>
                    <div class="accordion-content mt-3 text-gray-600">
                        Students who meet the academic, financial, and residency requirements are eligible to apply.
                    </div>
                </div>

                <!-- ITEM -->
                <div class="fade-up bg-gray-100 p-6 rounded-2xl cursor-pointer accordion">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-lg">How long does the approval process take?</span>
                        <span class="text-xl">+</span>
                    </div>
                    <div class="accordion-content mt-3 text-gray-600">
                        The process typically takes 2–4 weeks depending on the number of applications received.
                    </div>
                </div>

                <!-- ITEM -->
                <div class="fade-up bg-gray-100 p-6 rounded-2xl cursor-pointer accordion">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-lg">What documents are required?</span>
                        <span class="text-xl">+</span>
                    </div>
                    <div class="accordion-content mt-3 text-gray-600">
                        Applicants must submit academic records, proof of residency, and other supporting documents.
                    </div>
                </div>

                <!-- ITEM -->
                <div class="fade-up bg-gray-100 p-6 rounded-2xl cursor-pointer accordion">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-lg">Is the scholarship renewable?</span>
                        <span class="text-xl">+</span>
                    </div>
                    <div class="accordion-content mt-3 text-gray-600">
                        Yes, scholars can renew their scholarship based on academic performance and compliance.
                    </div>
                </div>

                <!-- ITEM -->
                <div class="fade-up bg-gray-100 p-6 rounded-2xl cursor-pointer accordion">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-lg">Can I apply to multiple programs?</span>
                        <span class="text-xl">+</span>
                    </div>
                    <div class="accordion-content mt-3 text-gray-600">
                        Yes, applicants may apply to multiple programs, but only one scholarship may be granted.
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= CTA ================= -->
    <section
        class="py-28 bg-gradient-to-br from-[#163d2c] via-[#1f4432] to-[#0f2b20] text-white text-center relative overflow-hidden">

        <!-- Glow Effect -->
        <div class="absolute w-[500px] h-[500px] bg-yellow-400/20 blur-[120px] top-[-100px] left-1/2 -translate-x-1/2">
        </div>

        <div class="relative max-w-3xl mx-auto px-6 fade-up">

            <h2 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight">
                Start Your Scholarship Journey Today
            </h2>

            <p class="text-gray-300 text-lg mb-8">
                Join thousands of students who are already transforming their future through our scholarship programs.
            </p>

            <!-- CTA BUTTON -->
            <a href="<?= url('/create') ?>" class="btn inline-block bg-yellow-400 text-black px-10 py-4 rounded-2xl font-bold text-lg shadow-xl 
                   hover:scale-105 hover:shadow-2xl transition">
                Apply Now
            </a>

            <!-- TRUST ELEMENTS -->
            <div class="mt-10 grid grid-cols-3 gap-6 text-sm text-gray-300">
                <div>
                    <p class="text-2xl font-bold text-yellow-400">10K+</p>
                    <p>Students Supported</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-yellow-400">95%</p>
                    <p>Success Rate</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-yellow-400">₱50M+</p>
                    <p>Funds Distributed</p>
                </div>
            </div>

        </div>

    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 py-10 text-center">
        © 2026 Scholarship System
    </footer>
    <script src="<?= base_url() ?>public/script/scroll.js"></script>


</body>

</html>