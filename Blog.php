<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./style.css" rel="stylesheet">
  <title>Blog</title>

  <link rel="icon" href="./Assets/img/logo bg.png" type="image/x-icon">
  <link rel="icon" href="./Assets/img/logo bg.png" type="image/png" sizes="16x16">
  <link rel="icon" href="./Assets/img/logo bg.png" type="image/png" sizes="32x32">
  <link rel="apple-touch-icon" href="./Assets/img/logo bg.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta name="theme-color" content="#1f9c7b">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gravitas+One&display=swap" rel="stylesheet">

</head>

<body class="bg-gray-100 font-(family-name:public sans)">

  <?php include 'Header.php'; ?>

  <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
    <div class="layout-container flex h-full grow flex-col">
      <div class="px-4 md:px-10 lg:px-40 flex flex-1 justify-center py-5">
        <div class="layout-content-container flex flex-col max-w-[960px] flex-1">

          <main class="flex-1">
            <div class="@container">
              <div class="@[480px]:p-4">
                <div
                  class="flex min-h-[400px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-lg items-center justify-center p-4"
                  data-alt="Abstract gradient background from dark blue to purple"
                  style='background-image: linear-gradient(rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAQuPy8KQYni_ClRjv4UYhiI3oTW_BGPBTY-MhYG8BOo5rYddfkckLYjWKq6H0h0LvTfgqRRla1efiSEoB-cL7D58uNupdnDW4cfRH6UxbeBQn-9nvIpeWhxtTpENUKIzopDhqlfM_f1JcD9nZv4Gbd3HN5m9llYkeEEavOJHYyoBvOBLgTI-4TB6wIRNVLAgTs07KdCBwrQ3cWrMONF3_rpYesMYAjJ_qIJiYrMiQqbW3A9pOiKPmcOCl_4ekHVYDjgeWAnL_SXmwk");'>
                  <div class="flex flex-col gap-2 text-center">
                    <h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl">
                      Insights &amp; News</h1>
                    <h2 class="text-white/90 text-sm font-normal leading-normal @[480px]:text-base">
                      Stay updated with
                      our latest articles and announcements.</h2>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-4 py-6">
              <label class="flex flex-col min-w-40 h-12 w-full">
                <div class="flex w-full flex-1 items-stretch rounded-lg h-full border-2 border-orange-200">
                  <div
                    class="text-gray-500 flex border-solid bg-white items-center justify-center pl-4 rounded-l-lg border-r-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                      <path fill-rule="evenodd"
                        d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                        clip-rule="evenodd" />
                    </svg>

                  </div>
                  <input
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-800 focus:outline-2 focus:ring-1 bg-white focus:border-1 h-full placeholder:text-gray-600 px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                    placeholder="Search for articles..." value="" />
                </div>
              </label>
            </div>
            <div class="flex gap-3 p-3 flex-wrap pr-4">
              <button
                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-orange-600 text-white">
                <p class="text-sm font-medium leading-normal">All</p>
              </button>
              <button
                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-white hover:bg-orange-200 text-gray-800">
                <p class="text-sm font-medium leading-normal">Technology</p>
              </button>
              <button
                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-white hover:bg-orange-200 text-gray-800">
                <p class="text-sm font-medium leading-normal">Business</p>
              </button>
              <button
                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-white hover:bg-orange-200 text-gray-800">
                <p class="text-sm font-medium leading-normal">Marketing</p>
              </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
              <div
                class="flex flex-col gap-3 pb-3 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover"
                  data-alt="Abstract image of AI neural networks"
                  style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDGNJ77b1AF9hlikNQOIm5C7S8lp3W48-QJ6UEOricKiC_mVyGgoy1VI6lnE8a1ScvPHuhBgAMWM90GgD4B0fwdMjTP6v_LJyJdYo0d7zOmqHygIkK8RMi6HWCgn915PAOwXTUvhW3UUU6YW4Rhu1TAgRtu-C5wgfBjVfGiyoC0SsLEIesbSu00AtvW7uJKwmYtASrfuB4OmXTbFvU1wi-rxl4Uid0yfKQBU3N527E3MMHE8bn30kfJMp_WlzwfBfHguh4vmWz9JBCc");'>
                </div>
                <div class="p-4 flex flex-col flex-1">
                  <p class="text-orange-600 text-sm font-medium">Technology</p>
                  <h3 class="text-lg font-bold leading-tight text-gray-800 mt-1">The
                    Future of AI in
                    Business</h3>
                  <p class="text-gray-600 text-sm font-normal leading-normal mt-2 flex-1">
                    An in-depth
                    look at how artificial intelligence is reshaping industries.</p>
                  <div class="flex items-center justify-between mt-4">
                    <p class="text-gray-500 text-xs font-normal">June 1, 2024</p>
                    <a class="text-orange-600 font-bold text-sm group-hover:underline" href="#">Read
                      More →</a>
                  </div>
                </div>
              </div>
              <div
                class="flex flex-col gap-3 pb-3 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover"
                  data-alt="Colorful charts and graphs on a screen"
                  style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCojiuS1p075HSFYLhHUdHD_2en_MZfUDSgOXzhyfuH2tu2Xa2Q3M-eLTgBEcAMe-JN45DNcbESOYHPtZVommgvtsKBP5oQmor0z61hOkbSds5p8446Y30dDtoZesjc0II4toIcWPAGqVBQCPPw5RlEhLVsXF35swMQ0ZetwfED2VHjaUodlv5CEdhB6N5yVVt7zNwA2ob8qzOM4hhB-FKdJa7hoMptenbnqgX8fniAOinnCX84U_Sdqof1w-_70VRmaCPb-7twCv_1");'>
                </div>
                <div class="p-4 flex flex-col flex-1">
                  <p class="text-orange-600 text-sm font-medium">Marketing</p>
                  <h3 class="text-lg font-bold leading-tight text-gray-800 mt-1">10
                    Tips for Effective
                    Marketing</h3>
                  <p class="text-gray-600 text-sm font-normal leading-normal mt-2 flex-1">
                    Practical
                    advice for creating successful marketing campaigns.</p>
                  <div class="flex items-center justify-between mt-4">
                    <p class="text-gray-500 text-xs font-normal">May 28, 2024</p>
                    <a class="text-orange-600 font-bold text-sm group-hover:underline" href="#">Read
                      More →</a>
                  </div>
                </div>
              </div>
              <div
                class="flex flex-col gap-3 pb-3 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover"
                  data-alt="Person using a laptop in a modern office"
                  style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA4pOJRkzODm4jnZq8nRYYmaTK9Y4q6IVUD0894kasmAnuFjrGUPOwZZUl-48Tak5tKN7Zyry5GKJy5ibCWoEbPdaLlUEPcjfHCGUKQglG-GQYy69X6UWsxxieQrrEtl5U3eJ5mJy0iSuLEnzbe18aij1HJ-u4tDZuh0Afc7cSViY3ukhJp9qqPSrWXn50dsjb4swlb9tLbrC7nLreLIn179edN2pBAaYWREoXKbejPU51zwFlE56n4ciUmH814731s7iRQy7lz1umh");'>
                </div>
                <div class="p-4 flex flex-col flex-1">
                  <p class="text-orange-600 text-sm font-medium">Business</p>
                  <h3 class="text-lg font-bold leading-tight text-gray-800 mt-1">
                    Navigating Digital
                    Transformation</h3>
                  <p class="text-gray-600 text-sm font-normal leading-normal mt-2 flex-1">
                    How to
                    adapt your business to the ever-changing digital landscape.</p>
                  <div class="flex items-center justify-between mt-4">
                    <p class="text-gray-500 text-xs font-normal">May 22, 2024</p>
                    <a class="text-orange-600 font-bold text-sm group-hover:underline" href="#">Read
                      More →</a>
                  </div>
                </div>
              </div>
              <div
                class="flex flex-col gap-3 pb-3 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover"
                  data-alt="User interface design wireframes"
                  style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCr3iPR-gW22JiWtU2NgwrxYJDrJzY5DpZzyYNo77_et8uMua4zt5NpLVO_6zIc2uW0g-AGJ4wzlkAzc-PDyYbQyYfAuTBJj-NkfQcw6bO8DksPNABciOaqm8VhvOeXCWa1_nVYmZZo4zcHNgj-8tNFphjIxpuD47ILupxD4q7P-1GhXVLAEvAsp1AAmANdBqLOVH7h0rmUNnjKQXF8oe_7ZEYLwqCPIpJnic6QiTdzvZ1aEIS_Ch74ifuiBPlKlO3kpqTW8blIAC_d");'>
                </div>
                <div class="p-4 flex flex-col flex-1">
                  <p class="text-orange-600 text-sm font-medium">Technology</p>
                  <h3 class="text-lg font-bold leading-tight text-gray-800 mt-1">The
                    Importance of User
                    Experience</h3>
                  <p class="text-gray-600 text-sm font-normal leading-normal mt-2 flex-1">
                    Why a great
                    user experience is crucial for customer satisfaction.</p>
                  <div class="flex items-center justify-between mt-4">
                    <p class="text-gray-500 text-xs font-normal">May 15, 2024</p>
                    <a class="text-orange-600 font-bold text-sm group-hover:underline" href="#">Read
                      More →</a>
                  </div>
                </div>
              </div>
              <div
                class="flex flex-col gap-3 pb-3 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" data-alt="Servers in a data center"
                  style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD9YB8WjmII-jF1gV6KNF_b18SnOu4ucfa3jEKr2ou0Sf1mkiWiY2hQ-9k-PGh4lbm-12GNtXbe-4XR5BSAzLUB6pbBWZPdNJdPcLtiDiiqonVu-svXbmBsWkvjfDdQvmzbAyZIiPI2gJLksvckXrKredoPiRwxaRb-2FhJWqWHmoSv_qQW5RCaZmDAYQJ_A0NJkdp2IC6bvl7gfUseE7kJxpQpb_BvFicOYAwzNYTVqevxuLGotvWpHazmWMMvRM04ySEVZoY5YYut");'>
                </div>
                <div class="p-4 flex flex-col flex-1">
                  <p class="text-orange-600 text-sm font-medium">Technology</p>
                  <h3 class="text-lg font-bold leading-tight text-gray-800 mt-1">A
                    Guide to Cloud
                    Computing</h3>
                  <p class="text-gray-600 text-sm font-normal leading-normal mt-2 flex-1">
                    Understanding the benefits and challenges of cloud technology.</p>
                  <div class="flex items-center justify-between mt-4">
                    <p class="text-gray-500 text-xs font-normal">May 10, 2024</p>
                    <a class="text-orange-600 font-bold text-sm group-hover:underline" href="#">Read
                      More →</a>
                  </div>
                </div>
              </div>
              <div
                class="flex flex-col gap-3 pb-3 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover"
                  data-alt="Green leaves and sustainable imagery"
                  style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuARWTSYYFAh3Ayb0-N0DY2K1vPE-DbIUwNbxwkLAWMP65UE3TYmwkNP0CsxMvYhxc42dVWRkwbsbEt0OocBa7l0hTfGL8A8i0VLFILksy3kUf8-7YVcuSnxGBvWhyh4rYwCykN6Bnf7WQIxOab5keXrm1m8jafzJGO-NZYc-e2JQchZXqeT_FKFkWC_K4iTqhea9_shMC1l33lZMOJTFmzXpBaipiIJB0rCyWrlsgGNRRrSZE8si3tpOFpV0fGvk5Y_LNVhOlhK5Htw");'>
                </div>
                <div class="p-4 flex flex-col flex-1">
                  <p class="text-orange-600 text-sm font-medium">Business</p>
                  <h3 class="text-lg font-bold leading-tight text-gray-800 mt-1">
                    Sustainable Business
                    Practices</h3>
                  <p class="text-gray-600 text-sm font-normal leading-normal mt-2 flex-1">
                    Exploring
                    eco-friendly strategies for modern businesses.</p>
                  <div class="flex items-center justify-between mt-4">
                    <p class="text-gray-500 text-xs font-normal">May 5, 2024</p>
                    <a class="text-orange-600 font-bold text-sm group-hover:underline" href="#">Read
                      More →</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex justify-center items-center gap-2 p-4 mt-8">
              <button
                class="px-3 py-1 rounded-md text-sm font-medium bg-gray-200 text-gray-500 cursor-not-allowed"
                disabled="">Previous</button>
              <button class="w-8 h-8 rounded-md text-sm font-medium bg-orange-600 text-white">1</button>
              <button
                class="w-8 h-8 rounded-md text-sm font-medium text-gray-700 hover:bg-orange-200">2</button>
              <button
                class="w-8 h-8 rounded-md text-sm font-medium text-gray-700 hover:bg-orange-200">3</button>
              <button
                class="px-3 py-1 rounded-md text-sm font-medium text-gray-70 hover:bg-orange-200">Next</button>
            </div>
          </main>
          <footer
            class="mt-16 border-t border-solid border-t-[#f0f2f4] py-8 px-4 sm:px-10 text-center text-gray-500">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
              <p class="text-sm">© 2024 Innovate Corp. All rights reserved.</p>
              <div class="flex gap-4">
                <a class="hover:text-primary" href="#">Privacy Policy</a>
                <a class="hover:text-primary" href="#">Terms of Service</a>
              </div>
            </div>
          </footer>
        </div>
      </div>
    </div>
  </div>

</body>

</html>