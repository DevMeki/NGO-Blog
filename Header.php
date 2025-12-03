<header
  class="sticky backdrop-blur-sm top-0 z-10 w-full bg-background-light/80 md:px-10 md:py-3 py-2 px-3 border-b border-gray-300">
  <div class="flex justify-between">
    <div class="flex justify-between gap-3 items-center">
      <img src="./Assets/img/logo.png" class="size-7" alt="CINY Logo">
      <h2 class="text-2xl leading-tight tracking-[-0.015em] font-serif font-extrabold">C I N Y</h2>
    </div>

    <div class="hidden md:flex flex-1 justify-end gap-8">
      <nav class="flex items-center gap-9">
        <a class="text-sm font-medium hover:text-blue-600 nav-link" href="./index.php">Home</a>
        <a class="text-sm font-medium hover:text-blue-600 nav-link" href="./About_us.php">About</a>
        <a class="text-sm font-medium hover:text-blue-600 nav-link" href="./Activities.php">Activities</a>
        <a class="text-sm font-medium hover:text-blue-600 nav-link" href="./Blog.php">Blog</a>
        <a class="text-sm font-medium hover:text-blue-600 nav-link" href="./Contact_us.php">Contact us</a>
      </nav>

      <a href="./Donate.php">
        <button
          class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center rounded-lg h-10 px-4 hover:bg-blue-400 bg-blue-600 text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
          Donate
        </button>
      </a>
    </div>

    <!-- menu button for sm starts here  -->
    <div class="flex gap-4 items-center">
      <a href="./Donate.php" class="md:hidden">
        <button
          class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center rounded-lg h-8 px-4 hover:bg-blue-400 bg-blue-600 text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
          Donate
        </button>
      </a>

      <button class="py-1 px-2 rounded-xl font-bold text-2xl md:hidden cursor-pointer" type="button"
        onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </div>
</header>

<!-- sidebar starts here  -->
<div id="sidebar" class="fixed z-50 top-0 left-0 hidden w-64 h-full bg-white overflow-x-hidden shadow-xl transition-transform duration-300 ease-in-out transform -translate-x-full">
  <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-blue-600 text-white">
    <div class="flex items-center gap-3">
      <img src="./Assets/img/logo bg.png" class="size-6" alt="CINY Logo">
      <h2 class="text-xl font-bold">C I N Y</h2>
    </div>
    <button class="font-bold text-2xl p-2 cursor-pointer hover:bg-blue-700 rounded-lg transition-colors" type="button"
      onclick="toggleSidebar()">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <nav class="flex flex-col gap-2 p-4">
    <a class="text-gray-700 hover:bg-blue-100 hover:text-blue-600 px-4 py-3 rounded-lg nav-link transition-colors" href="./index.php">
      <i class="bi bi-house-door mr-3"></i>Home
    </a>
    <a class="text-gray-700 hover:bg-blue-100 hover:text-blue-600 px-4 py-3 rounded-lg nav-link transition-colors" href="./About_us.php">
      <i class="bi bi-info-circle mr-3"></i>About
    </a>
    <a class="text-gray-700 hover:bg-blue-100 hover:text-blue-600 px-4 py-3 rounded-lg nav-link transition-colors" href="./Activities.php">
      <i class="bi bi-activity mr-3"></i>Activities
    </a>
    <a class="text-gray-700 hover:bg-blue-100 hover:text-blue-600 px-4 py-3 rounded-lg nav-link transition-colors" href="./Blog.php">
      <i class="bi bi-journal-text mr-3"></i>Blog
    </a>
    <a class="text-gray-700 hover:bg-blue-100 hover:text-blue-600 px-4 py-3 rounded-lg nav-link transition-colors" href="./Contact_us.php">
      <i class="bi bi-envelope mr-3"></i>Contact us
    </a>
    <a class="mt-4" href="./Donate.php">
      <button class="w-full flex items-center justify-center rounded-lg h-12 px-4 bg-blue-600 text-white text-base font-bold hover:bg-blue-700 transition-colors">
        <i class="bi bi-heart-fill mr-2"></i>Donate
      </button>
    </a>
  </nav>
</div>

<!-- Overlay for mobile sidebar -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="toggleSidebar()"></div>

<script>
// Function to toggle sidebar
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  
  if (sidebar.classList.contains('hidden')) {
    // Show sidebar
    sidebar.classList.remove('hidden');
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
    // Prevent body scrolling when sidebar is open
    document.body.style.overflow = 'hidden';
  } else {
    // Hide sidebar
    sidebar.classList.add('-translate-x-full');
    setTimeout(() => {
      sidebar.classList.add('hidden');
      overlay.classList.add('hidden');
      document.body.style.overflow = '';
    }, 300); // Match transition duration
  }
}

// Function to mark active page based on current URL
function setActivePage() {
  // Get current page URL and filename
  const currentPage = window.location.pathname;
  const currentFilename = currentPage.split('/').pop().toLowerCase();
  
  // Select all navigation links (both desktop and mobile)
  const navLinks = document.querySelectorAll('.nav-link');
  
  // Mark active links
  navLinks.forEach(link => {
    const linkHref = link.getAttribute('href');
    const linkFilename = linkHref.split('/').pop().toLowerCase();
    
    // Check if current page matches the link's target
    if (currentFilename === linkFilename) {
      // For desktop links
      if (link.classList.contains('hover:text-blue-600')) {
        link.classList.add('text-blue-600', 'font-semibold');
        link.classList.remove('hover:text-blue-600');
      }
      // For mobile links
      if (link.classList.contains('hover:bg-blue-100')) {
        link.classList.add('bg-blue-100', 'text-blue-600', 'font-semibold');
        link.classList.remove('hover:bg-blue-100', 'hover:text-blue-600');
      }
    }
  });
}

// Close sidebar when clicking on a link (for mobile)
document.querySelectorAll('#sidebar .nav-link').forEach(link => {
  link.addEventListener('click', () => {
    toggleSidebar();
  });
});

// Close sidebar with Escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar.classList.contains('hidden')) {
      toggleSidebar();
    }
  }
});

// Run when DOM is loaded
document.addEventListener('DOMContentLoaded', setActivePage);
</script>

<!-- Add Bootstrap Icons if not already included -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">