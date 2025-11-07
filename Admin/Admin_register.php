<?php
session_start();
include_once '../Backend/Config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful. You can now log in.";
        header("location: .Admin_login.php");
        exit();
    } else {
        $error = "username already exist!";
    }
}
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admin Register - C I N Y</title>

    <link rel="icon" href="./Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="./Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="./Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="./Assets/img/logo bg.png">
    <link rel="manifest" href="/site.webmanifest">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script
        id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { colors: { primary: "oklch(64.6% 0.222 41.116)", "background-light": "#f8f7f6", "background-dark": "#221d10" }, fontFamily: { display: "Public Sans" }, borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" } } } };</script>
    <style>
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }
    </style>
</head>

<body class="font-display">
    <div
        class="relative flex min-h-screen w-full flex-col items-center justify-center bg-background-light text-[#2D3748]">

        <main class="w-full max-w-md px-4 py-8">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-3 text-center">
                    <h1 class="text-[#111418] text-4xl font-black leading-tight tracking-[-0.033em]">
                        Admin Register</h1>
                    <p class="text-[#617589] text-base font-normal leading-normal">Enter your
                        credentials to access the admin dashboard</p>
                </div>
                <div class="flex flex-col p-8 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <form class="flex flex-col gap-6" method="post" action="" id="RegisterForm">
                        <div class="flex flex-col gap-4">
                            <label class="flex flex-col w-full">
                                <p class="text-[#2D3748] text-base font-medium leading-normal pb-2">
                                    Username</p>

                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                    placeholder="Username" type="text" value="" id="username" />

                                <p class="text-[#ff0000] text-base font-medium leading-normal pb-2" id="emptyu"></p>

                            </label>
                            <div class="flex flex-col">
                                <label class="flex flex-col w-full">
                                    <p
                                        class="text-[#2D3748] dark:text-gray-300 text-base font-medium leading-normal pb-2">
                                        Password</p>

                                    <div class="relative flex w-full flex-1 items-stretch">
                                        <input
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                            placeholder="Enter your password" type="password" value="" id="password" />
                                        </p>

                                        <button
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#617589] dark:text-gray-400"
                                            type="button" onclick="showpassword()">
                                            <span class="material-symbols-outlined text-2xl">visibility</span>
                                        </button>
                                    </div>

                                    <p class="text-[#fd0303] text-base font-medium leading-normal pb-2" id="emptyp">
                                    </p>

                                </label>

                            </div>

                            <div class="flex flex-col">
                                <label class="flex flex-col w-full">
                                    <p
                                        class="text-[#2D3748] dark:text-gray-300 text-base font-medium leading-normal pb-2">
                                        Confirm Password</p>

                                    <div class="relative flex w-full flex-1 items-stretch">
                                        <input
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                            placeholder="Enter your password" type="password" value=""
                                            id="Confirm_password" />
                                        </p>

                                        <button
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#617589] dark:text-gray-400"
                                            type="button" onclick="showpassword()">
                                            <span class="material-symbols-outlined text-2xl">visibility</span>
                                        </button>
                                    </div>

                                    <p class="text-[#fd0303] text-base font-medium leading-normal pb-2" id="emptyp2">
                                    </p>

                                </label>
                                <!-- <a class="text-primary text-sm font-normal leading-normal pt-2 text-right hover:underline"
                                    href="#">Forgot Password?</a> -->
                            </div>
                        </div>
                        <button
                            class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 px-4 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors"
                            type="button" onclick="validateRegister()" value="submit">
                            <span class="truncate">Register</span>
                        </button>
                    </form>

                    <?php if (isset($error)): ?>
                        <p class="text-red-500 text-center mt-4"><?php echo $error; ?></p>
                    <?php endif; ?>

                    <a class="text-primary text-lg font-normal leading-normal pt-2 text-center hover:underline"
                        href="./Admin_Login.php">Login</a>
                </div>
                <!-- <p class="text-center text-sm text-[#617589] dark:text-gray-500">© 2024 Organization Name. All Rights
                    Reserved. <a class="text-primary hover:underline" href="#">Privacy Policy</a></p> -->
            </div>
        </main>
    </div>
</body>

<script>
    function showpassword(){
        const x = document.getElementById("password");
        const y = document.getElementById("Confirm_password");

        if (x.type === "password"){
            x.type = "text";
            y.type = "text";
        } else {
            x.type = "password";
            y.type = "password";
        }
    }

    function validateRegister() {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const confirm_password = document.getElementById('Confirm_password').value;
        const emptyu1 = document.getElementById('emptyu');
        const emptyp1 = document.getElementById('emptyp');
        const emptyp2 = document.getElementById('emptyp2');

        const x = document.getElementById("RegisterForm");

        emptyu1.innerHTML = "";
        emptyp1.innerHTML = "";
        emptyp2.innerHTML = "";

        if (username === "") {
            emptyu1.innerHTML = "Username cannot be empty";
        } else if (password === "") {
            emptyp1.innerHTML = "Password cannont be empty";
        } else if (confirm_password === "") {
            emptyp2.innerHTML = "Confirm Password cannot be empty";
        } else if (password !== confirm_password) {
            emptyp2.innerHTML = "Passwords do not match";
        } else {
            x.action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>";
        }

    }
</script>

</html>