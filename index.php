<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link href="./style.css" rel="stylesheet"> -->
  <title>Home</title>

  <link rel="icon" href="Assets/img/logo bg.png" type="image/x-icon">
  <link rel="icon" href="Assets/img/logo bg.png" type="image/png" sizes="16x16">
  <link rel="icon" href="Assets/img/logo bg.png" type="image/png" sizes="32x32">
  <link rel="apple-touch-icon" href="Assets/img/logo bg.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta name="theme-color" content="#1f9c7b">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gravitas+One&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* Existing Scroll Animations */
    .fade-in-up {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.6s ease-out;
    }

    .fade-in-up.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .fade-in-left {
      opacity: 0;
      transform: translateX(-30px);
      transition: all 0.6s ease-out;
    }

    .fade-in-left.visible {
      opacity: 1;
      transform: translateX(0);
    }

    .fade-in-right {
      opacity: 0;
      transform: translateX(30px);
      transition: all 0.6s ease-out;
    }

    .fade-in-right.visible {
      opacity: 1;
      transform: translateX(0);
    }

    .scale-in {
      opacity: 0;
      transform: scale(0.9);
      transition: all 0.6s ease-out;
    }

    .scale-in.visible {
      opacity: 1;
      transform: scale(1);
    }

    .stagger-delay-1 {
      transition-delay: 0.1s;
    }

    .stagger-delay-2 {
      transition-delay: 0.2s;
    }

    .stagger-delay-3 {
      transition-delay: 0.3s;
    }

    .stagger-delay-4 {
      transition-delay: 0.4s;
    }

    /* LOGO SPIN ANIMATION (Retained) */
    @keyframes spin-45 {
      from {
        transform: rotate(45deg);
      }

      to {
        transform: rotate(405deg);
      }
    }

    .animate-spin-45 {
      animation: spin-45 10s linear infinite;
    }
  </style>
  <style>
    @keyframes typing {
      0% {
        width: 0;
      }

      60% {
        width: 100%;
      }

      /* 3s typing of 5s total = 60% */
      80% {
        width: 100%;
      }

      100% {
        width: 0;
      }
    }

    @keyframes cursor {

      0%,
      60%,
      100% {
        border-color: transparent;
      }

      61%,
      79% {
        border-color: currentColor;
      }
    }

    .typing-animation {
      display: inline-block;
      overflow: hidden;
      white-space: nowrap;
      border-right: 3px solid;
      width: 0;
      animation:
        typing 5s steps(40, end) infinite,
        cursor 5s step-end infinite;
    }
  </style>
</head>

<body class="text-gray-950 bg-gray-100">

  <?php
  // --- PHP INCLUDES (Retained) ---
  require_once 'Backend/Config.php';
  session_start();
  require_once 'Backend/track_visits.php';
  require 'Header.php';

  // Fetch latest 3 blog posts
  $blog_posts = [];
  try {
    $sql = "SELECT post_id, Title, Content, Image_path, Date_posted, Categories, Tags, Featured, published_by 
                      FROM blog_post 
                      ORDER BY Date_posted DESC 
                      LIMIT 3";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $blog_posts[] = $row;
      }
    }
  } catch (Exception $e) {
    // Handle error silently or log it
    error_log("Error fetching blog posts: " . $e->getMessage());
  }
  ?>

  <div class="bg-gray-100 min-h-screen font-sans">

    <div
      class="relative min-h-screen flex flex-col justify-center items-center py-10 px-6 sm:px-10 overflow-hidden bg-blue-950 text-white">

      <p
        class="font-semibold text-lg sm:text-2xl border border-white rounded-full px-6 py-2 bg-black bg-opacity-30 w-max mb-10 text-center scale-in">
        Welcome To
      </p>

      <section class="flex flex-col lg:flex-row justify-between items-center w-full max-w-6xl z-10">
        <div class="max-w-xl text-center lg:text-left mb-12 lg:mb-0">
          <h1 class="text-4xl sm:text-6xl font-extrabold leading-tight fade-in-left">
            <span>CONCERNED <br> IGBO-EZE NORTH </span>
            <span class="typing-animation"> YOUTHS (CINY)</span>
          </h1>
        </div>

        <div
          class="w-64 h-64 sm:w-80 sm:h-80 bg-white/10 rounded-full flex justify-center items-center p-4 shadow-2xl backdrop-blur-sm">
          <img src="Assets/img/logo bg.png" alt="CINY Logo" class="w-full h-full object-contain p-4 animate-spin-45" />
        </div>
      </section>

      <div class="absolute bottom-6 flex justify-center fade-in-up stagger-delay-2">
        <svg class="w-8 h-8 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </div>
    </div>

    <hr class="border-t border-gray-300">

    <div class="min-h-screen bg-gray-50 p-6 sm:p-10 font-sans flex flex-col justify-center">
      <main class="w-full max-w-4xl mx-auto flex flex-col md:flex-row gap-10">
        <div class="md:w-1/3">
          <h2 class="text-3xl font-bold text-blue-800 mb-4 border-b pb-2 fade-in-left">BIO</h2>
        </div>

        <div class="md:w-2/3 text-lg leading-relaxed text-gray-700">
          <p class="mb-4 fade-in-up stagger-delay-1">
            Concerned Igbo-Eze North Youth (CINY) is a **non-profitable, non-religious and non-political organization**.
            Interested only in the growth, development, and good-welfare of the people of Igbo-Eze Noth.
          </p>
          <p class="mb-4 fade-in-up stagger-delay-2">
            The youth-led movement is founded with the primary aim of advocating for **social justice, community
            development, and responsible leadership** in Igbo-Eze North Local Government Area of Enugu State, Nigeria.
          </p>
          <p class="mb-4 fade-in-up stagger-delay-3">
            Driven by a shared commitment to the protection of human rights, security, youth empowerment, and the
            overall well-being of citizens, CINY stands as the unified voice of the youth calling for transparency,
            equity, and sustainable progress across all sectors.
          </p>
          <p class="fade-in-up stagger-delay-4">
            We are a watchdog of the people, confronting injustices, holding public servants accountable, and driving
            grassroots development initiatives that impact lives positively.
          </p>
        </div>
      </main>
    </div>

    <hr class="border-t border-gray-300">

    <div class="bg-gray-100 p-6 sm:p-10 font-sans relative overflow-hidden flex flex-col justify-center py-20">

      <div class="w-full max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12">

        <div class="fade-in-left">
          <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-4 border-blue-600 pb-2 w-max">VISION</h2>
          <p class="text-xl leading-relaxed text-gray-700">
            To build a just, peaceful, and progressive Igbo-Eze North where every citizen, especially the youth,
            can live with dignity, access opportunities, and thrive in a secure and transparent society.
          </p>
        </div>

        <div class="fade-in-right">
          <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-4 border-blue-600 pb-2 w-max">MISSION
          </h2>
          <ul class="space-y-4 text-xl text-gray-700">
            <li class="flex items-start">
              <span class="text-2xl font-bold text-blue-600 mr-3 mt-1">•</span>
              To defend and uphold the rights and welfare of every youth and vulnerable person in Igbo-Eze North.
            </li>
            <li class="flex items-start">
              <span class="text-2xl font-bold text-blue-600 mr-3 mt-1">•</span>
              To promote transparency, accountability, and responsiveness in governance and public institutions.
            </li>
          </ul>
        </div>
      </div>
    </div>


  </div>
  <main class="flex-1">
    <div class="px-4 sm:px-10 lg:px-10 flex flex-1 justify-center py-5">
      <div class="layout-content-container flex flex-col flex-1">
        <div class="@container">
          <div class="@[480px]:p-4">
            <div
              class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-xl items-center justify-center p-4 text-center"
              data-alt="Abstract gradient background"
              style='background-image: linear-gradient(rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%), url("Assets/img/Cover 2.jpg")'>
              <div class="flex flex-col gap-2">
                <h1
                  class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl fade-in-up">
                  Empowering Our Community, <br> One Initiative at a Time
                </h1>
                <h2
                  class="text-white/90 text-sm font-normal leading-normal @[480px]:text-base fade-in-up stagger-delay-1">
                  We are a non-profit organization dedicated to making a positive impact in Igbo-Eze North.
                </h2>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="px-4 sm:px-10 lg:px-40 flex flex-1 justify-center py-10">
      <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
        <div class="flex flex-col gap-10 @container">

          <div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-4 p-0">
            <div
              class="flex flex-1 gap-4 rounded-xl border border-transparent bg-white p-6 flex-col items-center text-center shadow-sm hover:shadow-lg transition-all scale-in stagger-delay-1">
              <div class="text-blue-600 text-4xl">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-7">
                  <path
                    d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                  <path
                    d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
                  <path
                    d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z" />
                </svg>
              </div>
              <div class="flex flex-col gap-1">
                <h2 class="text-blue-600 text-lg font-bold leading-tight">
                  Infrastructural Development</h2>
                <p class="text-[#617589] mt-3 text-sm font-normal leading-normal">
                  Demanding essential public infrastructure, and an end to infrastructural deficits
                  (roads, hospitals, electricity, water) across the community.</p>
              </div>
            </div>
            <div
              class="flex flex-1 gap-4 rounded-xl border border-transparent bg-white p-6 flex-col items-center text-center shadow-sm hover:shadow-lg transition-all scale-in stagger-delay-2">
              <div class="text-blue-600 text-4xl">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-7">
                  <path
                    d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                </svg>
              </div>
              <div class="flex flex-col gap-1">
                <h2 class="text-blue-600 text-lg font-bold leading-tight">
                  Community Welfare & Health</h2>
                <p class="text-[#617589] mt-3 text-sm font-normal leading-normal">
                  Providing social welfare assistance (like food donations during the pandemic) and ensuring urgent
                  state intervention for public health crises (like the Yellow Fever Outbreak).</p>
              </div>
            </div>
            <div
              class="flex flex-1 gap-4 rounded-xl border border-transparent bg-white p-6 flex-col items-center text-center shadow-sm hover:shadow-lg transition-all scale-in stagger-delay-3">
              <div class="text-blue-600 text-4xl"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                  fill="currentColor" class="size-7">
                  <path fill-rule="evenodd"
                    d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z"
                    clip-rule="evenodd" />
                  <path
                    d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z" />
                </svg>
              </div>
              <div class="flex flex-col gap-1">
                <h2 class="text-blue-600 text-lg font-bold leading-tight">
                  Advocacy & Mobilization</h2>
                <p class="text-[#617589] mt-3 text-sm font-normal leading-normal">
                  Driving awareness through peaceful protests, social media platforms, and sponsored radio programs to
                  draw government attention to the region's decaying conditions.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="px-4 sm:px-10 lg:px-40 flex flex-1 justify-center py-10">
      <div class="layout-content-container flex flex-col max-w-[960px] flex-1 gap-6">
        <h1 class="text-blue-600 tracking-tight text-[32px] font-bold leading-tight text-center fade-in-up">
          Our Activities</h1>
        <div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-6 p-4">
          <div class="flex flex-col gap-3 pb-3 fade-in-left stagger-delay-1">
            <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg"
              data-alt="People working together in a workshop" style='background-image: url("Assets/img/human.jpg");'>
            </div>
            <div>
              <p class="text-blue-600 text-lg font-medium leading-normal">Humanitarian Aid</p>
              <p class="text-[#617589] mt-2 text-sm font-normal leading-normal">Donated over 5,000 tubers of yam for
                community distribution during the COVID-19 pandemic.</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 fade-in-up stagger-delay-2">
            <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg"
              data-alt="A person receiving medical check-up" style='background-image: url("Assets/img/media.jpg");'>
            </div>
            <div>
              <p class="text-blue-600 text-lg font-medium leading-normal">Media Outreach</p>
              <p class="text-[#617589] mt-2 text-sm font-normal leading-normal">Sponsored months of radio programs to
                promote cultural heritage and draw government attention to the decay.</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 fade-in-right stagger-delay-3">
            <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg"
              data-alt="Children learning in a classroom" style='background-image: url("Assets/img/conflict.jpg");'>
            </div>
            <div>
              <p class="text-blue-600 text-lg font-medium leading-normal">Conflict Resolution</p>
              <p class="text-[#617589] mt-2 text-sm font-normal leading-normal">Mediating into various cases of our
                people
                both within and outside the community.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="px-4 sm:px-10 lg:px-40 flex flex-1 justify-center py-10">
      <div class="layout-content-container flex flex-col max-w-[960px] flex-1 gap-6">
        <h1 class="text-blue-600 tracking-tight text-[32px] font-bold leading-tight text-center fade-in-up">
          Latest From The Blog</h1>
        <div class="flex justify-center">
          <div class="grid grid-cols-1 md:grid-cols-3 p-4 gap-8">
            <?php if (!empty($blog_posts)): ?>
              <?php foreach ($blog_posts as $index => $post):
                // Decode image JSON string (default to empty array if decoding fails)
                $image_paths = json_decode($post['Image_path'], true) ?: [];
                // Use the first image path, or a placeholder if none exists
                $image_url = !empty($image_paths) ? htmlspecialchars($image_paths[0]) : 'https://placehold.co/600x400/f3f4f6/374151?text=No+Image';
                $delay_class = 'stagger-delay-' . ($index + 1);
                ?>
                <div
                  class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-white dark:bg-background-dark/50 shadow-sm min-w-72 hover:shadow-lg transition-all scale-in <?php echo $delay_class; ?>">
                  <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg"
                    style='background-image: url("<?php echo $image_url; ?>");'
                    data-alt="<?php echo htmlspecialchars($post['Title']); ?>">
                  </div>
                  <div class="flex flex-col flex-1 justify-between p-4 pt-0 gap-4">
                    <div>
                      <p class="text-blue-600 text-base font-medium leading-normal">
                        <?php echo htmlspecialchars($post['Title']); ?>
                      </p>
                      <p class="text-[#617589] text-sm font-normal leading-normal">
                        <?php
                        // Create a short excerpt from the content
                        $content = $post['Content'];
                        $excerpt = strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                        echo htmlspecialchars($excerpt);
                        ?>
                      </p>
                      <p class="text-[#617589] text-xs font-normal leading-normal mt-2">
                        <?php echo date('M j, Y', strtotime($post['Date_posted'])); ?>
                      </p>
                    </div>
                    <a class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-blue-100 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-blue-400 hover:text-white transition-colors"
                      href="post.php?id=<?php echo htmlspecialchars($post['post_id']); ?>">
                      <span class="truncate">Read More</span>
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-span-3 text-center py-8 fade-in-up">
                <p class="text-[#617589] text-lg">No blog posts available at the moment.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
    <div class="px-4 sm:px-10 lg:px-40 flex flex-1 justify-center py-10 bg-white">
      <div
        class="layout-content-container flex flex-col md:flex-row items-center justify-between max-w-[960px] flex-1 gap-8 p-8 rounded-xl fade-in-up">
        <div class="text-center md:text-left">
          <h2 class="text-2xl md:text-3xl font-bold text-blue-600">Join Our Community
          </h2>
          <p class="text-[#617589] mt-2">Become a part of our mission to create a
            better world. Your support makes a difference.</p>
        </div>
        <a href="Contact_us.php">
          <button
            class="flex min-w-[150px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-blue-600 text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-blue-900 transition-colors">
            <span class="truncate">Get Involved</span>
          </button>
        </a>
      </div>
    </div>
  </main>

  <?php require 'Footer.php'; ?>

  <script>
    // Function to set up the Intersection Observer
    function setupIntersectionObserver() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            // Stop observing once visible to prevent re-triggering
            observer.unobserve(entry.target);
          }
        });
      }, {
        // Adjust threshold for when the element should be considered "visible"
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      });

      // Observe all elements with animation classes
      document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .scale-in').forEach(element => {
        // Only observe elements that haven't been made visible yet
        if (!element.classList.contains('visible')) {
          observer.observe(element);
        }
      });
    }

    // 1. Initial setup on DOM Content Loaded (fastest reliable check)
    document.addEventListener('DOMContentLoaded', setupIntersectionObserver);

    // 2. Fallback check after the page visually loads
    // This is the critical change to fix issues on slow live servers
    window.addEventListener('load', () => {
      // Re-run the observer setup after a short delay to ensure layout is finalized
      setTimeout(setupIntersectionObserver, 100);
    });
  </script>
</body>

</html>