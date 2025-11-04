<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./style.css" rel="stylesheet">
    <title>Contact Us</title>

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

<body class="bg-gray-100 font-(family-name: Public Sans, sans-serif)">

    <?php include 'Header.php'; ?>

    <main class="flex-1 px-4 py-8 md:px-10 lg:px-20 xl:px-40">
        <div class="layout-content-container flex flex-col max-w-7xl mx-auto">
            <div class="flex flex-wrap justify-between gap-3 p-4">
                <div class="flex min-w-72 flex-col gap-3">
                    <p class="text-gray-900 text-4xl font-black leading-tight tracking-[-0.033em]">
                        Get in Touch</p>
                    <p class="text-gray-600 text-base font-normal leading-normal">
                        We'd love to hear from you. Please fill out the form below or use our contact details to
                        get in touch.
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mt-8 p-4">
                <div class="flex flex-col gap-6 p-8 rounded-xl bg-white shadow-sm">
                    <form class="flex flex-col gap-6">
                        <label class="flex flex-col w-full">
                            <p class="text-gray-900 text-base font-medium leading-normal pb-2">
                                Name</p>
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-orange-500 border border-gray-300 bg-white focus:border-orange-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                placeholder="Enter your name" value="" />
                        </label>
                        <label class="flex flex-col w-full">
                            <p class="text-gray-900 text-base font-medium leading-normal pb-2">
                                Email</p>
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-orange-500 border border-gray-300 bg-white focus:border-orange-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                placeholder="Enter your email address" type="email" value="" />
                        </label>
                        <label class="flex flex-col w-full">
                            <p class="text-gray-900 text-base font-medium leading-normal pb-2">
                                Subject</p>
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-orange-500 border border-gray-300 bg-white focus:border-orange-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                placeholder="Enter the subject of your message" value="" />
                        </label>
                        <label class="flex flex-col w-full">
                            <p class="text-gray-900 text-base font-medium leading-normal pb-2">
                                Message</p>
                            <textarea
                                class="form-textarea flex w-full min-w-0 flex-1 resize-y overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-orange-500 border border-gray-300 bg-white focus:border-orange-600 h-36 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                placeholder="Enter your message"></textarea>
                        </label>
                        <button
                            class="flex items-center justify-center rounded-lg h-12 px-6 bg-orange-600 text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2"
                            type="submit">
                            Submit
                        </button>
                    </form>
                </div>
                <div class="flex flex-col gap-8">
                    <div class="p-8 rounded-xl bg-white shadow-sm">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Contact Information
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex items-center justify-center size-10 rounded-full bg-orange-200 text-orange-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>

                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Email</p>
                                    <a class="text-gray-900 font-medium hover:text-orange-600"
                                        href="mailto:contact@organization.com">contact@organization.com</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex items-center justify-center size-10 rounded-full bg-orange-200 text-orange-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                    </svg>

                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Phone</p>
                                    <a class="text-gray-900 font-medium hover:text-orange-600 dark:hover:text-orange-600"
                                        href="tel:+1234567890">+1 (234) 567-890</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex items-center justify-center size-10 rounded-full bg-orange-200 text-orange-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>

                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Address</p>
                                    <p class="text-gray-900 font-medium">123 Main Street,
                                        Anytown, USA 12345</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl overflow-hidden shadow-sm h-80">
                        <iframe allowfullscreen="" class="w-full h-full border-0" data-location="Times Square"
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.219575191263!2d-73.9878440847047!3d40.75889497932691!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6451633%3A0x2a210e783ae40713!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1678886567890!5m2!1sen!2sus"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require 'Footer.php'; ?>

</body>

</html>