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
        onclick="document.querySelector('.fixed').style.display='block'">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </div>
</header>

<!-- sidebar starts here  -->
<div class="fixed z-1 top-14 left-0 hidden w-60 h-full bg-blue-400 overflow-x-hidden">
  <button class="font-bold text-3xl right-5 absolute top-2 py-2 px-2 rounded-xl cursor-pointer" type="button"
    onclick="this.parentElement.style.display='none'">
    <i class="bi bi-x-circle"></i>
  </button>

  <nav class="flex flex-col gap-4 py-15 text-xl font-medium text-gray-950">
    <a class="bg-light-500 hover:bg-blue-200 px-4 py-1 nav-link" href="./index.php">Home</a>
    <a class="bg-light-500 hover:bg-blue-200 px-4 py-1 nav-link" href="./About_us.php">About</a>
    <a class="bg-light-500 hover:bg-blue-200 px-4 py-1 nav-link" href="./Activities.php">Activities</a>
    <a class="bg-light-500 hover:bg-blue-200 px-4 py-1 nav-link" href="./Blog.php">Blog</a>
    <a class="bg-light-500 hover:bg-blue-200 px-4 py-1 nav-link" href="./Contact_us.php">Contact us</a>
  </nav>
</div>

<script>
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
      if (link.classList.contains('hover:bg-blue-200')) {
        link.classList.add('bg-blue-300', 'font-semibold');
        link.classList.remove('hover:bg-blue-200');
      }
    }
  });
}

// Run when DOM is loaded
document.addEventListener('DOMContentLoaded', setActivePage);
</script>