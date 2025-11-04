<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./style.css" rel="stylesheet">
    <title>Admin Post Editor</title>

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

    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <header
                class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#dbe0e6] px-10 py-3 bg-white">
                <div class="flex items-center gap-4">
                    <div class="size-6 ">
                        <img src="./Assets/img/logo.png">
                    </div>
                    <h2 class="text-orange-600 text-lg font-bold leading-tight tracking-[-0.015em]">
                        Admin</h2>
                </div>
                <div class="flex flex-1 justify-end gap-8">
                    <div class="flex items-center gap-9"><a class="text-sm font-medium leading-normal"
                            href="./Blog.php">Back
                            to Blog</a></div>
                    <button
                        class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-orange-600 text-white text-sm font-bold leading-normal tracking-[0.015em]">
                        <span class="truncate">Logout</span>
                    </button>
                </div>
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex flex-wrap justify-between gap-3 p-4">
                            <div class="flex min-w-72 flex-col gap-3">
                                <p class="text-4xl font-black leading-tight tracking-[-0.033em]">
                                    Create New Post</p>
                                <p class="text-gray-500 text-base font-normal leading-normal">Fill in
                                    the details below to create a new blog post.</p>
                            </div>
                        </div>
                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <label class="flex flex-col">
                                <p class="text-base font-medium leading-normal pb-2">
                                    Post Title</p>
                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg focus:outline-0 focus:ring-0 border border-[#dbe0e6] bg-white focus:border-orange-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                    placeholder="Enter the title of your post" value="" />
                            </label>
                        </div>
                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <p class=" text-base font-medium leading-normal pb-2">
                                Content</p>
                            <div class="flex flex-col min-w-40 h-full flex-1">
                                <div
                                    class="flex w-full flex-1 items-stretch rounded-lg flex-col border border-[#dbe0e6]">
                                    <div class="flex flex-1 flex-col">
                                        <textarea
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-t-lg text-text-primary focus:outline-0 focus:ring-0 border-0 bg-white focus:border-primary min-h-72 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                            placeholder="Write your article content here..."></textarea>
                                        <div
                                            class="flex border-t border-t-[#dbe0e6] bg-gray-50 item-center pr-[15px] rounded-b-lg px-[15px] py-2">
                                            <div class="flex items-center gap-4 flex-1 justify-between">
                                                <div class="flex items-center gap-1">
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linejoin="round"
                                                                d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 0 0 0-8.25H6.75Zm0 0v.38m0 16.122h6.747a4.5 4.5 0 0 0 0-9.001h-7.5v9h.753Zm0 0v-.37m0-15.751h6a3.75 3.75 0 1 1 0 7.5h-6m0-7.5v7.5m0 0v8.25m0-8.25h6.375a4.125 4.125 0 0 1 0 8.25H6.75m.747-15.38h4.875a3.375 3.375 0 0 1 0 6.75H7.497v-6.75Zm0 7.5h5.25a3.75 3.75 0 0 1 0 7.5h-5.25v-7.5Z" />
                                                        </svg>

                                                    </button>
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M5.248 20.246H9.05m0 0h3.696m-3.696 0 5.893-16.502m0 0h-3.697m3.697 0h3.803" />
                                                        </svg>

                                                    </button>
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M17.995 3.744v7.5a6 6 0 1 1-12 0v-7.5m-2.25 16.502h16.5" />
                                                        </svg>

                                                    </button>
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                                        </svg>

                                                    </button>
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                        </svg>

                                                    </button>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm text-gray-500 italic">Draft
                                                        saved</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <div class="flex flex-col">
                                <div
                                    class="bg-white flex flex-col items-center gap-6 rounded-lg border-2 border-dashed border-[#dbe0e6] px-6 py-14">
                                    <div class="flex max-w-[480px] flex-col items-center gap-2">
                                        <p
                                            class="text-lg font-bold leading-tight tracking-[-0.015em] max-w-[480px] text-center">
                                            Featured Image</p>
                                        <p
                                            class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal max-w-[480px] text-center">
                                            Image should be at least 1200px wide. Drag and drop an image here or click
                                            to upload.
                                        </p>
                                    </div>
                                    <button
                                        class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-gray-100 text-sm font-bold leading-normal tracking-[0.015em]">
                                        <span class="truncate">Upload Image</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h3 class="text-lg font-bold  mb-4">Publish Settings</h3>
                            <div class="space-y-4">
                                <button
                                    class="w-full flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-orange-600 text-white text-sm font-bold leading-normal tracking-[0.015em]">
                                    <span class="truncate">Publish</span>
                                </button>
                                <button
                                    class="w-full flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-gray-200 text-sm font-bold leading-normal tracking-[0.015em]">
                                    <span class="truncate">Save Draft</span>
                                </button>
                                <!-- <div class="flex items-center justify-between pt-4">
                                    <label class="" for="publish-immediately">Publish
                                        Immediately</label>
                                    <div
                                        class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                        <input checked=""
                                            class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 checked:bg-orange-600"
                                            id="publish-immediately" name="toggle" type="checkbox" />
                                        <label
                                            class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                                            for="publish-immediately"></label>
                                    </div>
                                </div> -->
                                <!-- <div>
                                    <label class="dark:text-white text-sm font-medium">Schedule
                                        Publication</label>
                                    <input
                                        class="mt-1 form-input w-full rounded-lg border border-[#dbe0e6] dark:border-gray-700 bg-white dark:bg-background-dark text-text-primary dark:text-white focus:border-primary dark:focus:border-primary"
                                        type="date" />
                                </div> -->
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h3 class="text-lg font-bold mb-4">Categories &amp; Tags
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="text-sm font-medium">Categories</label>
                                    <select
                                        class="mt-1 form-select p-2 w-full rounded-lg border border-[#dbe0e6] bg-white focus:border-orange-600 dark:focus:border-orange-600">
                                        <option>Announcements</option>
                                        <option>Product Updates</option>
                                        <option>Company News</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Tags</label>
                                    <input
                                        class="mt-1 p-2 form-input w-full rounded-lg border border-[#dbe0e6] bg-white focus:border-orange-600 dark:focus:border-orange-600"
                                        placeholder="Add tags, comma-separated" />
                                </div>
                                <div class="flex items-center">
                                    <input
                                        class="h-4 w-4 focus:ring-orange-600 border-gray-300 rounded"
                                        id="featured-post" type="checkbox" />
                                    <label class="ml-2 block text-sm"
                                        for="featured-post">Mark as Featured Post</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>