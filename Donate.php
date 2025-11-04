<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./style.css" rel="stylesheet">
    <title>Donate</title>

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

<body class="bg-gray-100 font-(family-name: Public Sans, sans-serif) text-gray-950">

    <?php require 'Header.php'; ?>

    <div class="relative flex min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">

            <main class="flex-1">
                <!-- HeroSection Component -->
                <section class="w-full">
                    <div class="@container">
                        <div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat items-center justify-center p-4 text-center"
                            data-alt="Abstract image with soft, blurred lights in blue and green tones, conveying a sense of hope and community."
                            style='background-image: linear-gradient(rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.5) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuCG_HPAcSDkWK1KXYm-repGtJB3hwAZiWFPk7b2GfqvRttuVXgPeKxVxzO_6LolCTKjkPypGns-J8sGHIw_Cnm9ExkJMElzh1Nu2_AAvl9Dv0EqL2Qdit9Vj1pOagRnEELa8SlAnye2XGwu6d6f9MN9fpdKVQCwxS51TyVHhttuMC_k4P2s2zYPuN60Ubr_G_5SVae1OonbBLu8nI-68tOpy9-tXE1guVHaLb43mkXZ73jTrAfLyQQo3j8u6gLNuar1ITXuOOPgbiNQ");'>
                            <div class="flex flex-col gap-2">
                                <h1
                                    class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">
                                    Your Gift Creates Change</h1>
                                <h2
                                    class="max-w-2xl text-white text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal">
                                    Join us in our mission to make a difference. Your support empowers our work and
                                    brings hope to communities in need.</h2>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="container mx-auto px-4 py-12 md:py-20">
                    <div class="max-w-3xl mx-auto flex flex-col gap-12">
                        <!-- Donation Form Module -->
                        <section class="bg-white p-6 md:p-8 rounded-xl shadow-lg border border-white">
                            <div class="flex flex-col gap-8">
                                <!-- PageHeading Component -->
                                <div class="text-center">
                                    <p class="text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">Make
                                        Your Donation</p>
                                </div>
                                <!-- BodyText Component -->
                                <p class="text-base font-normal leading-normal text-center text-gray-500">
                                    Choose an amount and frequency to support our cause. Every contribution, big or
                                    small, makes a significant impact.
                                </p>
                                <!-- Frequency & Amount Selection -->
                                <div class="flex flex-col gap-6">
                                    <!-- SectionHeader for Frequency -->
                                    <div>
                                        <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">Select Donation
                                            Frequency</h3>
                                        <div class="mt-3 grid grid-cols-2 gap-2 rounded-lg bg-orange-100 p-1">
                                            <button
                                                class="px-4 py-3 text-sm font-bold rounded-md bg-orange-600 text-white transition-colors">One-Time</button>
                                            <button
                                                class="px-4 py-3 text-sm font-bold rounded-md hover:bg-orange-300 text-gray-950 transition-colors">Monthly</button>
                                        </div>
                                    </div>
                                    <!-- SectionHeader for Amount -->
                                    <div>
                                        <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">Choose an Amount
                                        </h3>
                                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                            <button
                                                class="flex items-center justify-center p-3 text-sm font-bold border border-gray-200 rounded-lg hover:bg-orange-100 transition-colors">$25</button>
                                            <button
                                                class="flex items-center justify-center p-3 text-sm font-bold border border-gray-200 rounded-lg hover:bg-orange-100 transition-colors">$50</button>
                                            <button
                                                class="flex items-center justify-center p-3 text-sm font-bold border rounded-lg bg-orange-200 border-orange-600 text-orange-600 ">$100</button>
                                            <div class="col-span-2 sm:col-span-1">
                                                <input
                                                    class="w-full p-3 text-sm font-bold border border-gray-200 rounded-lg focus:ring-orange-600 focus:border-orange-600 bg-gray-100"
                                                    placeholder="Custom" type="number" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Personal Information -->
                                <div class="flex flex-col gap-4">
                                    <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">Personal Information
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <input
                                            class="w-full p-3 text-sm border border-gray-200 rounded-lg focus:ring-orange-600 focus:border-orange-600 bg-gray-100"
                                            placeholder="First Name" type="text" />
                                        <input
                                            class="w-full p-3 text-sm border border-gray-200 rounded-lg focus:ring-orange-600 focus:border-orange-600 bg-gray-100"
                                            placeholder="Last Name" type="text" />
                                    </div>
                                    <input
                                        class="w-full p-3 text-sm border border-gray-200 rounded-lg focus:ring-orange-600 focus:border-orange-600 bg-gray-100"
                                        placeholder="Email Address" type="email" />
                                </div>
                                <!-- Payment Information -->
                                <div class="flex flex-col gap-4">
                                    <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">Payment Details</h3>
                                    <!-- Payment gateway would be embedded here -->
                                    <div class="p-4 border border-dashed border-gray-200 rounded-lg bg-gray-100">
                                        <p class="text-center text-gray-500 text-sm">
                                            Secure Payment Gateway (e.g., Stripe/PayPal) would be integrated here.</p>
                                    </div>
                                    <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>

                                        <span>Secure SSL Encrypted Donation</span>
                                    </div>
                                </div>
                                <!-- CTA Button -->
                                <button
                                    class="w-full flex min-w-[84px] max-w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-orange-600 text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-orange-900 transition-colors">
                                    <span class="truncate">Donate Now</span>
                                </button>
                            </div>
                        </section>
                        <!-- Impact Section -->
                        <!-- <section>
                            <div class="text-center mb-8">
                                <h2 class="text-3xl font-black leading-tight tracking-[-0.033em]">Where Your Donation
                                    Goes</h2>
                                <p class="mt-2 text-text-muted-light dark:text-text-muted-dark">Your generosity fuels
                                    real change. See the tangible impact of your support.</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div
                                    class="flex flex-col items-center text-center p-6 bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark">
                                    <div
                                        class="mb-4 flex items-center justify-center size-14 rounded-full bg-primary/20 text-primary">
                                        <span class="material-symbols-outlined text-3xl">local_florist</span>
                                    </div>
                                    <h4 class="font-bold text-lg">Clean Water</h4>
                                    <p class="text-sm text-text-muted-light dark:text-text-muted-dark mt-1">
                                        <strong>$25</strong> provides a family with a water filter, ensuring access to
                                        safe drinking water for a year.
                                    </p>
                                </div>
                                <div
                                    class="flex flex-col items-center text-center p-6 bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark">
                                    <div
                                        class="mb-4 flex items-center justify-center size-14 rounded-full bg-primary/20 text-primary">
                                        <span class="material-symbols-outlined text-3xl">school</span>
                                    </div>
                                    <h4 class="font-bold text-lg">Education</h4>
                                    <p class="text-sm text-text-muted-light dark:text-text-muted-dark mt-1">
                                        <strong>$50</strong> supplies a child with essential school materials, opening
                                        doors to a brighter future.
                                    </p>
                                </div>
                                <div
                                    class="flex flex-col items-center text-center p-6 bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark">
                                    <div
                                        class="mb-4 flex items-center justify-center size-14 rounded-full bg-primary/20 text-primary">
                                        <span class="material-symbols-outlined text-3xl">health_and_safety</span>
                                    </div>
                                    <h4 class="font-bold text-lg">Healthcare</h4>
                                    <p class="text-sm text-text-muted-light dark:text-text-muted-dark mt-1">
                                        <strong>$100</strong> funds vital medical supplies for a rural clinic, saving
                                        lives in remote communities.
                                    </p>
                                </div>
                            </div>
                        </section> -->
                        <!-- FAQ Accordion -->
                        <!-- <section>
                            <div class="text-center mb-8">
                                <h2 class="text-3xl font-black leading-tight tracking-[-0.033em]">Frequently Asked
                                    Questions</h2>
                            </div>
                            <div class="space-y-4">
                                <details
                                    class="group p-4 bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark cursor-pointer">
                                    <summary class="flex items-center justify-between font-bold text-base list-none">
                                        Is my donation tax-deductible?
                                        <span
                                            class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                                    </summary>
                                    <p class="mt-3 text-sm text-text-muted-light dark:text-text-muted-dark">Yes,
                                        Organization Name is a registered 501(c)(3) non-profit organization. All
                                        donations are tax-deductible to the fullest extent of the law. You will receive
                                        an email receipt for your records shortly after your donation is processed.</p>
                                </details>
                                <details
                                    class="group p-4 bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark cursor-pointer">
                                    <summary class="flex items-center justify-between font-bold text-base list-none">
                                        How can I manage my recurring donation?
                                        <span
                                            class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                                    </summary>
                                    <p class="mt-3 text-sm text-text-muted-light dark:text-text-muted-dark">You can
                                        easily cancel or change your monthly donation at any time. Simply follow the
                                        link in your initial donation receipt email or contact our support team, and
                                        we'll be happy to assist you.</p>
                                </details>
                                <details
                                    class="group p-4 bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark cursor-pointer">
                                    <summary class="flex items-center justify-between font-bold text-base list-none">
                                        Are there other ways to support your work?
                                        <span
                                            class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                                    </summary>
                                    <p class="mt-3 text-sm text-text-muted-light dark:text-text-muted-dark">Absolutely!
                                        Besides monetary donations, you can support us by volunteering, spreading the
                                        word on social media, or participating in our community events. Visit our 'Get
                                        Involved' page to learn more about these opportunities.</p>
                                </details>
                            </div>
                        </section> -->
                    </div>
                </div>
            </main>
            <!-- Footer with Trust Badges -->
            <footer class="bg-white border-t border-gray-200">
                <div class="container mx-auto px-4 py-8">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                        <p class="text-sm text-gray-600">© 2024 Organization Name. All
                            Rights Reserved.</p>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-bold text-gray-600">Your
                                contribution is secure:</span>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-8 bg-gray-300 rounded flex items-center justify-center text-xs text-gray-500"
                                    data-alt="SSL Secure Logo">SSL</div>
                                <div class="w-12 h-8 bg-gray-300 rounded flex items-center justify-center text-xs text-gray-500"
                                    data-alt="PCI Compliant Logo">PCI</div>
                                <div class="w-12 h-8 bg-gray-300 rounded flex items-center justify-center text-xs text-gray-500"
                                    data-alt="Charity Navigator Logo">Seal</div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

</body>

</html>