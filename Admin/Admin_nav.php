<?php
// Check for logout confirmation
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    session_destroy();
    header('Location: Admin_login.php');
    exit();
}

// Fetch unread inquiries count
$unread_inquiries_count = 0;
if (isset($conn) && $conn instanceof mysqli && @$conn->ping()) {
    try {
        $unread_sql = "SELECT COUNT(*) as unread FROM inquiry WHERE status = 'new'";
        $unread_result = $conn->query($unread_sql);
        if ($unread_result) {
            $unread_row = $unread_result->fetch_assoc();
            $unread_inquiries_count = (int) ($unread_row['unread'] ?? 0);
        }
    } catch (Exception $e) {
        error_log("Error fetching unread inquiries: " . $e->getMessage());
    }
}
?>
<div
    class="flex-col gap-4 p-4 bg-white dark:bg-[#1a2633] w-64 border-r border-gray-200 dark:border-gray-700 fixed h-full z-40">
    <!-- Mobile Menu Toggle -->
    <button id="mobileMenuToggle"
        class="lg:hidden absolute -right-12 top-4 p-2 bg-white dark:bg-[#1a2633] rounded-r-lg border border-l-0 border-gray-200 dark:border-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="flex items-center gap-3 px-3 py-2">
        <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10"
            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCg1Z53prw3r7jvHrQ3BWX9HVYQGDUgb7H78xWoCUM5rEvg1XZKPtvuf3Nq6KnY61EdHLyf-t-DGB5dhOXc_JwnoKAOtBkCgoVcuDQv-_IVN_ZNLrv7xbvf2kYv_XC854fjuW-FhKCmKRXHr_PtkDatlihusYBLIfPtnjyY26cUn-lSruOeCWXggGBz0ORQ4lbIYFAwzp6nGAg1I2wG21Ir6N5WaH3jOI_m04KZfWDwWReWdV-I-qiqUZX4WQhUOWwEJLsRq2ll7fkH");'>
        </div>
        <div class="flex flex-col">
            <h1 class="text-[#111418] dark:text-white text-base font-medium leading-normal">
                <?php echo htmlspecialchars($username); ?>
            </h1>
        </div>
    </div>

    <div class="mt-4 flex-1 overflow-y-auto">
        <nav id="navbar" class="flex flex-col gap-2">
            <a id="dashboardBtn"
                class="flex nav-link items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary dark:bg-primary/20"
                href="Admin_Dashboard.php">
                <span class="material-symbols-outlined">dashboard</span>
                <p class="text-sm font-medium leading-normal">Dashboard</p>
            </a>

            <a class="flex nav-link items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                href="Admin.php">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                <p class="text-sm font-medium leading-normal">Admins</p>
            </a>

            <a class="flex nav-link items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                href="../Blog.php">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                </svg>
                <p class="text-sm font-medium leading-normal">Blog Page</p>
            </a>

            <a class="flex nav-link items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative"
                href="Inquiries.php">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    <?php if ($unread_inquiries_count > 0): ?>
                        <span
                            class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white border-2 border-white dark:border-[#1a2633]">
                            <?php echo $unread_inquiries_count; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-sm font-medium leading-normal">Inquiries</p>
            </a>

            <a class="flex nav-link items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                href="List_Post.php">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                </svg>
                <p class="text-sm font-medium leading-normal">Posts</p>
            </a>

            <a class="flex nav-link items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                href="Draft.php">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                </svg>
                <p class="text-sm font-medium leading-normal">Drafts</p>
            </a>

            <button type="button" id="logoutBtn"
                class="flex nav-link items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                        d="M7.5 3.75A1.5 1.5 0 0 0 6 5.25v13.5a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5V15a.75.75 0 0 1 1.5 0v3.75a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3V5.25a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3V9A.75.75 0 0 1 15 9V5.25a1.5 1.5 0 0 0-1.5-1.5h-6Zm5.03 4.72a.75.75 0 0 1 0 1.06l-1.72 1.72h10.94a.75.75 0 0 1 0 1.5H10.81l1.72 1.72a.75.75 0 1 1-1.06 1.06l-3-3a.75.75 0 0 1 0-1.06l3-3a.75.75 0 0 1 1.06 0Z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium leading-normal">Log out</p>
            </button>
        </nav>
    </div>

    <div class="mt-20">
        <a href="Admin_Post_Editor.php">
            <button
                class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90">
                <span class="truncate">New Post</span>
            </button>
        </a>
    </div>
</div>

<!-- Mobile Overlay -->
<div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

<!-- Confirmation Modal -->
<div id="logoutModal"
    class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-background-dark rounded-xl shadow-lg p-6 w-full max-w-md mx-auto">
        <div class="flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 text-red-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div class="ml-4 text-left">
                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Confirm Logout</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to logout?</p>
                </div>
            </div>
        </div>
        <div class="mt-5 sm:mt-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
            <button id="cancelLogout"
                class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none sm:text-sm">
                Cancel
            </button>
            <a href="?confirm=yes">
                <button
                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:text-sm"
                    type="button">
                    Logout
                </button>
            </a>
        </div>
    </div>
</div>

<script>
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.querySelector('.fixed.w-64');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (mobileMenuToggle && sidebar) {
        // Initially hide sidebar on mobile
        if (window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full');
        }

        mobileMenuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            mobileOverlay.classList.toggle('hidden');
        });

        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        });

        // Update on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                mobileOverlay.classList.add('hidden');
            }
        });
    }

    // Logout modal functionality
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');

    if (logoutBtn && logoutModal) {
        logoutBtn.addEventListener('click', () => {
            logoutModal.classList.remove('hidden');
        });

        cancelLogout.addEventListener('click', () => {
            logoutModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        logoutModal.addEventListener('click', (e) => {
            if (e.target === logoutModal) {
                logoutModal.classList.add('hidden');
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !logoutModal.classList.contains('hidden')) {
                logoutModal.classList.add('hidden');
            }
        });
    }

    // Active link highlighting
    document.addEventListener('DOMContentLoaded', () => {
        const currentPage = window.location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPage || (currentPage === '' && href === 'Admin_Dashboard.php')) {
                // Remove active class from all links
                navLinks.forEach(l => {
                    l.classList.remove('bg-primary/10', 'text-primary', 'dark:bg-primary/20');
                    l.classList.add('hover:bg-gray-100', 'dark:hover:bg-gray-700');
                });

                // Add active class to current link
                link.classList.add('bg-primary/10', 'text-primary', 'dark:bg-primary/20');
                link.classList.remove('hover:bg-gray-100', 'dark:hover:bg-gray-700');
            }
        });
    });
</script>