<?php
require_once 'Backend/Config.php';
session_start();
require_once 'Backend/track_visits.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link href="./style.css" rel="stylesheet"> -->
  <title>Activities & History</title>

  <link rel="icon" href="Assets/img/logo bg.png" type="image/x-icon">
  <link rel="icon" href="Assets/img/logo bg.png" type="image/png" sizes="16x16">
  <link rel="icon" href="Assets/img/logo bg.png" type="image/png" sizes="32x32">
  <link rel="apple-touch-icon" href="Assets/img/logo bg.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta name="theme-color" content="#1f9c7b">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gravitas+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    :root {
      --primary-color: oklch(54.6% 0.245 262.881);
      --primary-dark: #167a60;
      --secondary-color: #2c3e50;
      --accent-color: #e74c3c;
      --light-bg: #f8f9fa;
      --dark-bg: #1a1a1a;
      --text-light: #617589;
      --text-dark: #111418;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-bg);
      color: var(--text-dark);
      overflow-x: hidden;
    }

    .dark body {
      background-color: var(--dark-bg);
      color: #ffffff;
    }

    /* Animation Classes */
    .fade-in-up {
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .fade-in-up.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .fade-in-left {
      opacity: 0;
      transform: translateX(-40px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .fade-in-left.visible {
      opacity: 1;
      transform: translateX(0);
    }

    .fade-in-right {
      opacity: 0;
      transform: translateX(40px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .fade-in-right.visible {
      opacity: 1;
      transform: translateX(0);
    }

    .scale-in {
      opacity: 0;
      transform: scale(0.95);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
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

    .stagger-delay-5 {
      transition-delay: 0.5s;
    }

    /* Custom Styles */
    .hero-section {
      background: linear-gradient(135deg, oklch(54.6% 0.245 262.881), rgba(44, 62, 80, 0.2)),
        url('Assets/img/activities.jpg');
      background-size: cover;
      background-position: center;
      min-height: 60vh;
      display: flex;
      align-items: center;
    }

    .history-card {
      background: white;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border-left: 5px solid var(--primary-color);
    }

    .dark .history-card {
      background: #2d3748;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .history-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    }

    .dark .history-card:hover {
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    }

    .timeline-item {
      position: relative;
      padding-left: 2.5rem;
      margin-bottom: 2.5rem;
    }

    .timeline-item:before {
      content: '';
      position: absolute;
      left: 0;
      top: 0.5rem;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: var(--primary-color);
      border: 3px solid white;
    }

    .dark .timeline-item:before {
      border-color: #2d3748;
    }

    .timeline-item:after {
      content: '';
      position: absolute;
      left: 7px;
      top: 1.5rem;
      bottom: -2.5rem;
      width: 2px;
      background: #e2e8f0;
    }

    .dark .timeline-item:after {
      background: #4a5568;
    }

    .timeline-item:last-child:after {
      display: none;
    }

    .program-card {
      background: white;
      border-radius: 16px;
      padding: 1.5rem;
      height: 100%;
      transition: all 0.3s ease;
      border-top: 4px solid var(--primary-color);
    }

    .dark .program-card {
      background: #2d3748;
    }

    .program-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .stat-card {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: white;
      border-radius: 16px;
      padding: 2rem;
      text-align: center;
    }

    .stat-number {
      font-size: 3rem;
      font-weight: 700;
      line-height: 1;
      margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
      .hero-section {
        min-height: 50vh;
        padding: 2rem 1rem;
      }

      .stat-number {
        font-size: 2rem;
      }

      .timeline-item {
        padding-left: 2rem;
      }

      .timeline-item:before {
        width: 12px;
        height: 12px;
        top: 0.75rem;
      }
    }

    @media (max-width: 480px) {
      .hero-section {
        min-height: 40vh;
      }

      .history-card,
      .program-card,
      .stat-card {
        padding: 1.5rem 1rem;
      }
    }

    .btn-primary {
      background: var(--primary-color);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(31, 156, 123, 0.3);
    }

    .section-title {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary-color), #3498db);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
      .section-title {
        font-size: 2rem;
      }
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-950">

  <?php require 'Header.php'; ?>

  <div class="layout-content-container">
    <!-- Hero Section -->
    <section class="hero-section fade-in-up">
      <div class="container mx-auto px-4 md:px-8 lg:px-16 max-w-7xl">
        <div class="max-w-3xl my-16">
          <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">
            Our Journey of Advocacy & Development
          </h1>
          <p class="text-white/90 text-lg md:text-xl mb-8">
            Since September 2019, we've been championing the cause of Igbo-Eze North through peaceful protests,
            community programs, and relentless advocacy for good governance.
          </p>
          <a href="#history" class="btn-primary inline-flex items-center gap-2">
            <span>Explore Our History</span>
            <i class="bi bi-arrow-down"></i>
          </a>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <main class="py-12">
      <div class="container mx-auto px-4 md:px-8 lg:px-16 max-w-7xl">

        <!-- History Timeline Section -->
        <section id="history" class="mb-16 fade-in-up">
          <div class="text-center mb-12 mt-4">
            <h2 class="section-title">Our History & Journey</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
              A timeline of our movement's key milestones and achievements in advocating for Igbo-Eze North development
            </p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Left Column -->
            <div class="space-y-16">
              <div class="history-card fade-in-left stagger-delay-1">
                <div class="flex items-center gap-3 mb-4">
                  <span class="material-symbols-outlined text-primary" style="font-size: 2.5rem;">history_edu</span>
                  <h3 class="text-2xl font-bold text-gray-800">The Birth of a Movement</h3>
                </div>
                <p class="text-gray-600 mb-4">
                  Concerned Igbo-eze North Youths was formed on 16th September 2019 following a clarion call by
                  concerned sons and daughters of Igbo-Eze North who could no longer tolerate the infrastructural
                  deficit plaguing our communities.
                </p>
                <div class="bg-primary/10 p-4 rounded-lg">
                  <p class="text-sm italic text-gray-700 ">
                    "A divine movement birthed out of passion for the suffering of our people due to total denial from
                    good governance and basic infrastructure."
                  </p>
                </div>
              </div>

              <div class="history-card fade-in-left stagger-delay-2">
                <div class="flex items-center gap-3 mb-4">
                  <span class="material-symbols-outlined text-accent" style="font-size: 2.5rem;">gavel</span>
                  <h3 class="text-2xl font-bold text-gray-800 ">First Peaceful Protest</h3>
                </div>
                <p class="text-gray-600 ">
                  On <strong>January 6th 2020</strong>, we staged our first peaceful protest against bad leadership and
                  ill-representation. Despite facing arrests, detention, and threats, our resolve for democratic change
                  remained unshaken.
                </p>
              </div>

              <div class="history-card fade-in-left stagger-delay-3">
                <div class="flex items-center gap-3 mb-4">
                  <span class="material-symbols-outlined text-green-600"
                    style="font-size: 2.5rem;">volunteer_activism</span>
                  <h3 class="text-2xl font-bold text-gray-800 ">COVID-19 & Community Support</h3>
                </div>
                <p class="text-gray-600 ">
                  During the pandemic, CINY donated <strong>over 5,000 tubers of yam</strong> to the local government
                  for distribution. We also escalated the Yellow Fever outbreak to state authorities, ensuring prompt
                  intervention.
                </p>
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-16">
              <div class="history-card fade-in-right stagger-delay-1">
                <div class="flex items-center gap-3 mb-4">
                  <span class="material-symbols-outlined text-blue-600" style="font-size: 2.5rem;">campaign</span>
                  <h3 class="text-2xl font-bold text-gray-800 ">Media Advocacy</h3>
                </div>
                <p class="text-gray-600  mb-4">
                  We sponsored months of radio programs at Voice FM Nsukka to promote our cultural heritage and draw
                  government attention to infrastructural decay in Igbo-Eze North.
                </p>
                <div class="flex items-center gap-2 text-sm text-gray-500 ">
                  <i class="bi bi-mic-fill"></i>
                  <span>Radio Programs • Community Awareness • Cultural Promotion</span>
                </div>
              </div>

              <div class="history-card fade-in-right stagger-delay-2">
                <div class="flex items-center gap-3 mb-4">
                  <span class="material-symbols-outlined text-purple-600" style="font-size: 2.5rem;">groups</span>
                  <h3 class="text-2xl font-bold text-gray-800 ">Leadership & Organization</h3>
                </div>
                <p class="text-gray-600">
                  On <strong>August 7th, 2022</strong>, the Concerned Igbo-Eze North Youth (CINY)
                  held its maiden electoral process, leading to the emergence of its first executive council. This
                  historic event was followed by a formal inaugural leadership ceremony, marking a significant milestone
                  in the organization's history and structural development.
                </p>
              </div>

              <div class="history-card fade-in-right stagger-delay-3">
                <div class="flex items-center gap-3 mb-4">
                  <span class="material-symbols-outlined text-orange-600" style="font-size: 2.5rem;">policy</span>
                  <h3 class="text-2xl font-bold text-gray-800 ">Political Engagement</h3>
                </div>
                <p class="text-gray-600 ">
                  In 2022/2023, we hosted a 2-week online interactive session with all aspiring candidates from
                  political parties. This unprecedented move allowed direct engagement between candidates and
                  constituents.
                </p>
              </div>
            </div>
          </div>
        </section>

        <!-- Statistics Section -->
        <section class="mb-16 mt-16">
          <div class="text-center mb-12 fade-in-up">
            <h2 class="section-title">Our Impact in Numbers</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
              Quantifying our commitment to Igbo-Eze North development
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <div class="stat-card scale-in stagger-delay-1">
              <div class="stat-number">4+</div>
              <p class="text-white/90 font-semibold">Years of Advocacy</p>
              <p class="text-white/80 text-sm mt-2">Since September 2019</p>
            </div>

            <div class="stat-card scale-in stagger-delay-2">
              <div class="stat-number">5,000+</div>
              <p class="text-white/90 font-semibold">Yam Tubers Donated</p>
              <p class="text-white/80 text-sm mt-2">During COVID-19 pandemic</p>
            </div>

            <div class="stat-card scale-in stagger-delay-4">
              <div class="stat-number">1,000s</div>
              <p class="text-white/90 font-semibold">Youths Engaged</p>
              <p class="text-white/80 text-sm mt-2">Across all communities</p>
            </div>
          </div>
        </section>

        <!-- Programs Section -->
        <section class="mb-16 mt-16">
          <div class="text-center mb-12 fade-in-up">
            <h2 class="section-title">Our Key Programs & Initiatives</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
              Comprehensive programs addressing various aspects of community development
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-16">
            <div class="program-card fade-in-up stagger-delay-1">
              <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-blue-600" style="font-size: 2.5rem;">campaign</span>
                <h3 class="text-xl font-bold text-gray-800">Advocacy & Awareness</h3>
              </div>
              <ul class="space-y-2 text-gray-600">
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Radio programs promoting cultural heritage</span>
                </li>
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Social media campaigns on governance</span>
                </li>
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Community documentary on infrastructure</span>
                </li>
              </ul>
            </div>

            <div class="program-card fade-in-up stagger-delay-2">
              <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-blue-600" style="font-size: 2.5rem;">handshake</span>
                <h3 class="text-xl font-bold text-gray-800 ">Community Support</h3>
              </div>
              <ul class="space-y-2 text-gray-600 ">
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Emergency relief distribution</span>
                </li>
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Health crisis intervention</span>
                </li>
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Conflict mediation services</span>
                </li>
              </ul>
            </div>

            <div class="program-card fade-in-up stagger-delay-3">
              <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-blue-600" style="font-size: 2.5rem;">diversity_3</span>
                <h3 class="text-xl font-bold text-gray-800">Political Engagement</h3>
              </div>
              <ul class="space-y-2 text-gray-600">
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Candidate interactive sessions</span>
                </li>
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Voter education programs</span>
                </li>
                <li class="flex items-start gap-2">
                  <i class="bi bi-check-circle-fill text-blue-600 mt-1"></i>
                  <span>Governance accountability advocacy</span>
                </li>
              </ul>
            </div>
          </div>
        </section>

        <!-- Our Values Section -->
        <section class="mb-16 mt-16">
          <div class="text-center mb-12 fade-in-up">
            <h2 class="section-title">Our Core Values</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
              The principles that guide our actions and decisions
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 text-center fade-in-left stagger-delay-1">
              <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-blue-600" style="font-size: 2rem;">balance</span>
              </div>
              <h3 class="text-xl font-bold mb-2">Non-Political</h3>
              <p class="text-gray-600">
                We remain the only social group in Igbo-Eze North that started and continues as non-political, resisting
                all temptations for brown envelopes.
              </p>
            </div>

            <div class="bg-white rounded-xl p-6 text-center fade-in-up stagger-delay-2">
              <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-blue-600" style="font-size: 2rem;">diversity_3</span>
              </div>
              <h3 class="text-xl font-bold mb-2">Inclusive</h3>
              <p class="text-gray-600">
                Open to all youths from every community in Igbo-Eze North, regardless of political, religious, or social
                affiliations.
              </p>
            </div>

            <div class="bg-white rounded-xl p-6 text-center fade-in-right stagger-delay-3">
              <div class="w-16 h-16 bg-blue-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">target</span>
              </div>
              <h3 class="text-xl font-bold mb-2">Development-Focused</h3>
              <p class="text-gray-600">
                Our only priority remains the wellbeing and infrastructural development of Igbo-Eze North communities.
              </p>
            </div>
          </div>
        </section>

        <!-- Call to Action -->
        <section class="text-center py-12 px-4 bg-gradient-to-r from-primary/10 to-blue-500/10 rounded-2xl fade-in-up">
          <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">
            Join Our Movement for Change
          </h2>
          <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
            Be part of the transformation. Together, we can build an Igbo-Eze North we shall all be proud of.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="Contact_us.php" class="btn-primary inline-flex items-center justify-center gap-2">
              <i class="bi bi-envelope"></i>
              <span>Contact Us</span>
            </a>
            <!-- <a href="get-involved.php" class="bg-white text-primary font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition-colors inline-flex items-center justify-center gap-2 border border-primary">
                            <i class="bi bi-people"></i>
                            <span>Get Involved</span>
                        </a> -->
          </div>
        </section>

      </div>
    </main>
  </div>

  <?php include 'Footer.php'; ?>

  <script>
    // Enhanced Scroll Animation
    document.addEventListener('DOMContentLoaded', function () {
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
          }
        });
      }, observerOptions);

      // Observe all animation elements
      document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .scale-in').forEach(element => {
        observer.observe(element);
      });

      // Smooth scroll for anchor links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const targetId = this.getAttribute('href');
          if (targetId === '#') return;

          const targetElement = document.querySelector(targetId);
          if (targetElement) {
            window.scrollTo({
              top: targetElement.offsetTop - 100,
              behavior: 'smooth'
            });
          }
        });
      });

      // Add parallax effect to hero section
      window.addEventListener('scroll', function () {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.hero-section');
        if (hero) {
          hero.style.transform = `translateY(${scrolled * 0.1}px)`;
        }
      });
    });
  </script>
</body>

</html>