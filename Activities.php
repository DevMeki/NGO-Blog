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
  <link href="./style.css" rel="stylesheet">
  <title>About Us</title>

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

  <style>
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
    
    .stagger-delay-1 { transition-delay: 0.1s; }
    .stagger-delay-2 { transition-delay: 0.2s; }
    .stagger-delay-3 { transition-delay: 0.3s; }
    .stagger-delay-4 { transition-delay: 0.4s; }
  </style>
</head>

<body class="bg-gray-100 text-gray-950 font-(family-name:public sans)">

  <?php require 'Header.php'; ?>

  <div class="layout-content-container flex max-w-7xl mx-auto flex-1">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
      <div class="layout-container flex h-full grow flex-col">

        <div class="@container">
          <div class="@[480px]:p-0">
            <div
              class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 rounded-xl items-center justify-center p-4 mt-4 text-center"
              data-alt="Abstract background image with blue and purple gradients"
              style='background-image: linear-gradient(rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.6) 100%), url("Assets/img/cover 1.jpg");'>
              <div class="flex flex-col gap-2">
                <h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl fade-in-up">
                  Concerned Igbo-Eze North Youths (CINY)
                </h1>
                <h2 class="text-white text-base font-normal leading-normal max-w-2xl mx-auto fade-in-up stagger-delay-1">
                  A short, impactful statement about the organization's mission to inspire and
                  drive positive change.
                </h2>
              </div>
            </div>
          </div>
        </div>
      </div>

      <main class="flex-1 px-4 sm:px-10 py-5">
        <div class="layout-content-container flex flex-col max-w-4xl mx-auto flex-1">

          <section class="py-12">
            <h2
              class="text-blue-600 text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-5 pt-5 text-center fade-in-up">
              Our Journey</h2>
            <div class="grid grid-cols-[auto_1fr] gap-x-4 px-4">
              <div class="flex flex-col items-center gap-2 pt-3">
                <div class="text-blue-600 bg-blue-200 rounded-full p-2 fade-in-up stagger-delay-1">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path
                      d="M5.223 2.25c-.497 0-.974.198-1.325.55l-1.3 1.298A3.75 3.75 0 0 0 7.5 9.75c.627.47 1.406.75 2.25.75.844 0 1.624-.28 2.25-.75.626.47 1.406.75 2.25.75.844 0 1.623-.28 2.25-.75a3.75 3.75 0 0 0 4.902-5.652l-1.3-1.299a1.875 1.875 0 0 0-1.325-.549H5.223Z" />
                    <path fill-rule="evenodd"
                      d="M3 20.25v-8.755c1.42.674 3.08.673 4.5 0A5.234 5.234 0 0 0 9.75 12c.804 0 1.568-.182 2.25-.506a5.234 5.234 0 0 0 2.25.506c.804 0 1.567-.182 2.25-.506 1.42.674 3.08.675 4.5.001v8.755h.75a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5H3Zm3-6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1-.75-.75v-3Zm8.25-.75a.75.75 0 0 0-.75.75v5.25c0 .414.336.75.75.75h3a.75.75 0 0 0 .75-.75v-5.25a.75.75 0 0 0-.75-.75h-3Z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="w-[2px] bg-gray-300 h-full grow fade-in-up"></div>
              </div>
              <div class="flex flex-1 flex-col py-3 pl-2 fade-in-right stagger-delay-1">
                <p class="text-blue-600 text-lg font-medium leading-normal">Founded</p>
                <p class="text-[#617589] text-base font-normal leading-normal">16th September 2019
                </p>
                <p class="text-[#111418] mt-2">It started with an
                  enraged cry from a son of the soil; Mr Chukwudi Onu and few others who could not pretend any
                  longer about the sickling impoverished of Igbo-eze North.</p>
              </div>
              <div class="flex flex-col items-center gap-2">
                <div class="w-[2px] bg-gray-300 h-4 fade-in-up"></div>
                <div class="text-blue-600 bg-blue-200 rounded-full p-2 fade-in-up stagger-delay-2">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                      d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 0 1 .75.75c0 5.056-2.383 9.555-6.084 12.436A6.75 6.75 0 0 1 9.75 22.5a.75.75 0 0 1-.75-.75v-4.131A15.838 15.838 0 0 1 6.382 15H2.25a.75.75 0 0 1-.75-.75 6.75 6.75 0 0 1 7.815-6.666ZM15 6.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"
                      clip-rule="evenodd" />
                    <path
                      d="M5.26 17.242a.75.75 0 1 0-.897-1.203 5.243 5.243 0 0 0-2.05 5.022.75.75 0 0 0 .625.627 5.243 5.243 0 0 0 5.022-2.051.75.75 0 1 0-1.202-.897 3.744 3.744 0 0 1-3.008 1.51c0-1.23.592-2.323 1.51-3.008Z" />
                  </svg>
                </div>
                <div class="w-[2px] bg-gray-300 h-full grow fade-in-up"></div>
              </div>
              <div class="flex flex-1 flex-col py-3 pl-2 fade-in-right stagger-delay-2">
                <p class="text-blue-600 text-lg font-medium leading-normal">First Major
                  Activity</p>
                <p class="text-[#617589] text-base font-normal leading-normal">January 6th 2020
                </p>
                <p class="text-[#111418] mt-2">We staged our first peaceful
                  protest against the long sabotaged reign of bad leadership and ill-representation by those who
                  are supposed to ensure the development of Igbo Eze North through their representation at the
                  corridor of democracy.</p>
              </div>
              <div class="flex flex-col items-center gap-2 pb-3">
                <div class="w-[2px] bg-gray-200 h-4 fade-in-up"></div>
                <div class="text-blue-600 bg-blue-200 rounded-full p-2 fade-in-up stagger-delay-3">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                      d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM8.547 4.505a8.25 8.25 0 1 0 11.672 8.214l-.46-.46a2.252 2.252 0 0 1-.422-.586l-1.08-2.16a.414.414 0 0 0-.663-.107.827.827 0 0 1-.812.21l-1.273-.363a.89.89 0 0 0-.738 1.595l.587.39c.59.395.674 1.23.172 1.732l-.2.2c-.211.212-.33.498-.33.796v.41c0 .409-.11.809-.32 1.158l-1.315 2.191a2.11 2.11 0 0 1-1.81 1.025 1.055 1.055 0 0 1-1.055-1.055v-1.172c0-.92-.56-1.747-1.414-2.089l-.654-.261a2.25 2.25 0 0 1-1.384-2.46l.007-.042a2.25 2.25 0 0 1 .29-.787l.09-.15a2.25 2.25 0 0 1 2.37-1.048l1.178.236a1.125 1.125 0 0 0 1.302-.795l.208-.73a1.125 1.125 0 0 0-.578-1.315l-.665-.332-.091.091a2.25 2.25 0 0 1-1.591.659h-.18c-.249 0-.487.1-.662.274a.931.931 0 0 1-1.458-1.137l1.279-2.132Z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
              <div class="flex flex-1 flex-col py-3 pl-2 fade-in-right stagger-delay-3">
                <p class="text-blue-600 text-lg font-medium leading-normal">Expansion
                </p>
                <p class="text-[#617589] text-base font-normal leading-normal">2020
                </p>
                <p class="text-[#111418] mt-2">Actively seeking out all other Concerned social bodies and every
                  concerned youth in Igbo-eze North to join the movement.</p>
              </div>
            </div>
          </section>
          <section class="py-12">
            <h2
              class="text-blue-600 text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-8 pt-5 text-center fade-in-up">
              What We Believe In</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div
                class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 scale-in stagger-delay-1">
                <div class="text-blue-600 bg-blue-200 rounded-full p-3 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd"
                      d="M9.661 2.237a.531.531 0 0 1 .678 0 11.947 11.947 0 0 0 7.078 2.749.5.5 0 0 1 .479.425c.069.52.104 1.05.104 1.59 0 5.162-3.26 9.563-7.834 11.256a.48.48 0 0 1-.332 0C5.26 16.564 2 12.163 2 7c0-.538.035-1.069.104-1.589a.5.5 0 0 1 .48-.425 11.947 11.947 0 0 0 7.077-2.75Zm4.196 5.954a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-blue-600 mb-2">Integrity</h3>
                <p class="text-[#617589] text-sm">We operate with honesty, defending the helpless and hopeless, and
                  remaining non-political despite threats and temptations for personal gain.</p>
              </div>
              <div
                class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 scale-in stagger-delay-2">
                <div class="text-blue-600 bg-blue-200 rounded-full p-3 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path
                      d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM1.49 15.326a.78.78 0 0 1-.358-.442 3 3 0 0 1 4.308-3.516 6.484 6.484 0 0 0-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 0 1-2.07-.655ZM16.44 15.98a4.97 4.97 0 0 0 2.07-.654.78.78 0 0 0 .357-.442 3 3 0 0 0-4.308-3.517 6.484 6.484 0 0 1 1.907 3.96 2.32 2.32 0 0 1-.026.654ZM18 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM5.304 16.19a.844.844 0 0 1-.277-.71 5 5 0 0 1 9.947 0 .843.843 0 0 1-.277.71A6.975 6.975 0 0 1 10 18a6.974 6.974 0 0 1-4.696-1.81Z" />
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-blue-600 mb-2">Community</h3>
                <p class="text-[#617589] text-sm">We are resolute in our commitment to the wellbeing of Igbo-eze North,
                  fighting for democratic dividends and advocating for essential public services.</p>
              </div>
              <div
                class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 scale-in stagger-delay-3">
                <div class="text-blue-600 bg-blue-200 rounded-full p-3 mb-4">
                  <i class="bi bi-unity"></i>
                </div>
                <h3 class="text-lg font-bold text-blue-600 mb-2">Unity</h3>
                <p class="text-[#617589] text-sm">We remain non-political, non-religious, and unbiased, matching forward
                  through the collective efforts of every concerned youth and social body.</p>
              </div>
            </div>
          </section>
          <!-- <section class="py-12">
            <h2
              class="text-blue-600 text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-8 pt-5 text-center fade-in-up">
              Meet the Team</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
              <div
                class="bg-white rounded-xl shadow-sm overflow-hidden text-center group transform hover:-translate-y-2 transition-all duration-300 scale-in stagger-delay-1">
                <img alt="Headshot of John Doe" class="w-full h-56 object-cover"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuDeWl45vMNB9oNsyMzfmeR1dKTs2nNue9DPhqerFqr2COKlnylAikbblH0biXRa7MxMqjGYuyGZmbXTPrjXhTvz42f-n1l8HAgM2SI2MTpm0CJLY5qXPH5jlpvPg_XaFauK6EunNbQlTbnCuwIotyvswXl71P94MtzIRRfByJNwi0EluTRvK9jZF-W8Q2AiHJdN-JGLQtYmC3qq5AkXgKhoLRvPD7EQXyc0lVKgTZob4X2_jXr_JzAvrweGh2Z4EczVtQYb2TyUNNJ_" />
                <div class="p-6">
                  <h3 class="text-lg font-bold text-blue-600">John Doe</h3>
                  <p class="text-blue-600 text-sm font-medium">CEO &amp; Founder</p>
                  <p class="text-[#617589] text-sm mt-2">John leads the
                    organization with a vision
                    for a better future and a passion for innovation.</p>
                </div>
              </div>
              <div
                class="bg-white rounded-xl shadow-sm overflow-hidden text-center group transform hover:-translate-y-2 transition-all duration-300 scale-in stagger-delay-2">
                <img alt="Headshot of Jane Smith" class="w-full h-56 object-cover"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuB27YWNOJyMbGcECRMezxe86LWdMJVHOkAR49J5CtMF9kLpBp_lJknLtl6q5MYpXOzOP293wa78LuqpceQ1HQtu5gr4itKgPGZ1RmaLN7JA0KCvWDaiKw17eozMjzbcwe6I1SegO7jGUttleCxiCByuKeFmVVeko-l48AdPF8Z5Y0gJL2jgIZLYAH8XkHJMrM3yrlT33s5sbF9ipWSY_81x9FBwUhrxmoQJiViaoUfLjMVLymgO4GcBZcZtY6f76bC0jINGXuibfFCX" />
                <div class="p-6">
                  <h3 class="text-lg font-bold text-blue-600">Jane Smith</h3>
                  <p class="text-blue-600 text-sm font-medium">Chief Operating Officer</p>
                  <p class="text-[#617589] text-sm mt-2">Jane ensures operational
                    excellence and
                    helps steer the company towards its goals.</p>
                </div>
              </div>
              <div
                class="bg-white rounded-xl shadow-sm overflow-hidden text-center group transform hover:-translate-y-2 transition-all duration-300 scale-in stagger-delay-3">
                <img alt="Headshot of Alex Johnson" class="w-full h-56 object-cover"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuBRlxWPSOugi_f1BKCb48jUbwGjOR8_pQaOd2UAkcOAQTMLoIJvz6V9pHAfHvaAJJNdmWbuzDFGBDO7bGY3lEHlMoc7ueP97mKZpTPToMID4LyuAOh36FOg2cCfEcp2ZFZVgS70E6OlMfAfpFUU2Of5pt3cUNsJ31MB_DvRkCWaz1sPeROm7y4a790jLsncQXfFt4AIIEVvomGM1icmiBsvYovU5TYM7r55tgcbdTaj0fgVVd5VovNh0pu_r-r_cfomU2VHz9qCwbKS" />
                <div class="p-6">
                  <h3 class="text-lg font-bold text-blue-600">Alex Johnson</h3>
                  <p class="text-blue-600 text-sm font-medium">Lead Developer</p>
                  <p class="text-[#617589] text-sm mt-2">Alex is the mastermind
                    behind our
                    technology, pushing the boundaries of what's possible.</p>
                </div>
              </div>
            </div>
          </section> -->
          <section class="py-12 text-center fade-in-up">
            <h2 class="text-2xl font-bold text-blue-600 mb-4">Ready to join our mission?
            </h2>
            <p class="text-[#617589] mb-8 max-w-xl mx-auto">Be part of our journey to
              make a
              difference. Whether you want to contribute, partner with us, or learn more, we'd love to
              hear from you.
            </p>
            <a href="Contact_us.php">
              <button
              class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-blue-600 text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-blue-900 transition-colors mx-auto">
              <span class="truncate">Get Involved</span>
            </button>
            </a>
          </section>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Scroll animation implementation
    document.addEventListener('DOMContentLoaded', function() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
          }
        });
      }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      });

      // Observe all elements with animation classes
      document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .scale-in').forEach(element => {
        observer.observe(element);
      });
    });
  </script>
</body>

</html>