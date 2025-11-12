<?php
include '../Backend/Config.php';
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admin Login - C I N Y</title>

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

    <?php
    //define variables and set to empty values
    $username = $password = "";
    $usernameErr = $passwordErr = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["username"])) {
            $usernameErr = "Username is required";
        } else {
            $x = $_POST['username'];
            //check if username only contains letters and whitespace
            if (!preg_match("/^[A-Za-z0-9\s]+$/", $x)) {
                $usernameErr = "Only letters, numbers and white space allowed";
            } else {
                $username = test_input($_POST["username"]);
            }
        }

        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
            $password = md5($password);

            $sql = "SELECT * FROM admins WHERE username = '$username' and password = '$password'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                session_start();
                $row = $result->fetch_assoc();
                $_SESSION['username'] = $row['username'];
                header("location: Admin_Dashboard.php");
                exit();
            } else {
                $passwordErr = "Incorrect username or password";
            }
        }
    }

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    ?>
    <div
        class="relative flex min-h-screen w-full flex-col items-center justify-center bg-background-light text-[#2D3748]">

        <main class="w-full max-w-md px-4 py-8">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-3 text-center">
                    <h1 class="text-[#111418] text-4xl font-black leading-tight tracking-[-0.033em]">
                        Admin Login</h1>
                    <p class="text-[#617589] text-base font-normal leading-normal">Enter your
                        credentials to access the admin dashboard</p>
                </div>
                <div class="flex flex-col p-8 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <form class="flex flex-col gap-6" method="post"
                        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="flex flex-col gap-4">
                            <label class="flex flex-col w-full">
                                <p class="text-[#2D3748] text-base font-medium leading-normal pb-2">
                                    Username</p>

                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                    placeholder="Username" type="text" value="" name="username" id="username" />

                                <p class="text-[#ff0000] text-base font-medium leading-normal pb-2" id="emptyu"></p>
                                <p class="text-[#ff0000] text-base font-medium leading-normal pb-2">
                                    <?php echo $usernameErr; ?>
                                </p>

                            </label>
                            <div class="flex flex-col">
                                <label class="flex flex-col w-full">
                                    <p
                                        class="text-[#2D3748] dark:text-gray-300 text-base font-medium leading-normal pb-2">
                                        Password</p>

                                    <div class="relative flex w-full flex-1 items-stretch">
                                        <input
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                            placeholder="Enter your password" type="password" name="password" value=""
                                            id="password" />
                                        </p>

                                        <button
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#617589] dark:text-gray-400"
                                            type="button" onclick="show_Password()">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="size-6">
                                                <path
                                                    d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                                <path
                                                    d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                                <path
                                                    d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                                            </svg>

                                        </button>
                                    </div>

                                    <p class="text-[#fd0303] text-base font-medium leading-normal pb-2" id="emptyp">
                                    </p>
                                    <p class="text-[#ff0000] text-base font-medium leading-normal pb-2">
                                        <?php echo $passwordErr; ?>
                                    </p>

                                </label>
                                <!-- <a class="text-primary text-sm font-normal leading-normal pt-2 text-right hover:underline"
                                    href="#">Forgot Password?</a> -->
                            </div>
                        </div>
                        <button
                            class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 px-4 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors"
                            type="button" onclick="validateLogin()" id="LoginBTN">
                            <span class="truncate">Log In</span>
                        </button>
                    </form>

                    <a class="text-primary text-lg font-normal leading-normal pt-2 text-center hover:underline"
                        href="./Admin_register.php">Register</a>
                </div>
                <!-- <p class="text-center text-sm text-[#617589] dark:text-gray-500">© 2024 Organization Name. All Rights
                    Reserved. <a class="text-primary hover:underline" href="#">Privacy Policy</a></p> -->
            </div>
        </main>
    </div>

    
</body>

<script>

    function show_Password() {
        const x = document.getElementById("password");
        // const y = document.getElementById("Confirm_password");

        if (x.type === "password") {
            x.type = "text";
            // y.type = "text";
        } else {
            x.type = "password";
            // y.type = "password";
        }
    }

    function validateLogin() {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        // const Confirm_password = document.getElementById('Confirm_password').value;
        const emptyu1 = document.getElementById('emptyu');
        const emptyp1 = document.getElementById('emptyp');
        // const emptyp2 = document.getElementById('emptyp2');
        const LoginBTN = document.getElementById('LoginBTN');

        emptyu1.innerHTML = "";
        emptyp1.innerHTML = "";
        // emptyp2.innerHTML = "";

        if (username === "") {
            emptyu1.innerHTML = "Username cannot be empty";
        } else if (!/^[A-Za-z0-9\s]+$/.test(username)) {
            emptyu1.innerHTML = "Username can only contain letters, numbers and spaces";
        } else if (password === "") {
            emptyp1.innerHTML = "Password cannont be empty";
            // } else if (Confirm_password === "") {
            //     emptyp2.innerHTML = "Confirm Password cannot be empty";
            // } else if (password !== Confirm_password) {
            //     emptyp2.innerHTML = "Passwords do not match";
        } else {
            LoginBTN.type = "submit";
            LoginBTN.name = "submit";
        }

    }
</script>

</html>