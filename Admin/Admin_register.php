<?php
include '../Backend/Config.php';
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

        .error-message {
            color: #ff0000;
            font-size: 14px;
            margin-top: 5px;
        }

        .success-message {
            color: #00aa00;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body class="font-display">
    <?php

    //define variables and set to empty values
    $token = $username = $password = $Confirm_password = "";
    $tokenErr = $usernameErr = $passwordErr = $Confirm_passwordErr = "";
    $registration_success = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Validate token
        if (empty($_POST["token"])) {
            $tokenErr = "Registration token is required";
        } else {
            $y = $_POST["token"];
            //check if token only contain letters and numbers
            if (!preg_match("/^[A-Za-z0-9\s]+$/", $y)) {
                $tokenErr = "Only letters, numbers and white space allowed";
            } else {
                $token = test_input($_POST["token"]);

                // Check if token exists in tokens table
                $checkTokenQuery = "SELECT * FROM tokens WHERE token = ?";
                $stmt = $conn->prepare($checkTokenQuery);
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $tokenResult = $stmt->get_result();

                if ($tokenResult->num_rows == 0) {
                    $tokenErr = "Invalid registration token";
                } else {
                    // Check if token has already been used in admins table
                    $checkTokenUsedQuery = "SELECT * FROM admins WHERE Token = ?";
                    $stmt2 = $conn->prepare($checkTokenUsedQuery);
                    $stmt2->bind_param("s", $token);
                    $stmt2->execute();
                    $tokenUsedResult = $stmt2->get_result();

                    if ($tokenUsedResult->num_rows > 0) {
                        $tokenErr = "This token has already been used";
                    }
                    $stmt2->close();
                }
                $stmt->close();
            }
        }

        // Validate username
        if (empty($_POST["username"])) {
            $usernameErr = "Username is required";
        } else {
            $x = $_POST['username'];
            //check if username only contains letters and whitespace
            if (!preg_match("/^[A-Za-z0-9\s]+$/", $x)) {
                $usernameErr = "Only letters, numbers and white space allowed";
            } else {
                $username = test_input($_POST["username"]);

                // Check if username already exists
                $checkUsernameQuery = "SELECT * FROM admins WHERE username = ?";
                $stmt = $conn->prepare($checkUsernameQuery);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $usernameResult = $stmt->get_result();

                if ($usernameResult->num_rows > 0) {
                    $usernameErr = "Username already taken!";
                }
                $stmt->close();
            }
        }

        // Validate password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
            if (strlen($password) < 6) {
                $passwordErr = "Password must be at least 6 characters long";
            }
        }

        // Validate confirm password
        if (empty($_POST["Confirm_password"])) {
            $Confirm_passwordErr = "Confirm Password is required";
        } else {
            $Confirm_password = test_input($_POST["Confirm_password"]);
            if ($password !== $Confirm_password) {
                $Confirm_passwordErr = "Passwords do not match";
            }
        }

        // If no errors, proceed with registration
        if (empty($tokenErr) && empty($usernameErr) && empty($passwordErr) && empty($Confirm_passwordErr)) {
            $hashed_password = md5($password);

            $insertQuery = "INSERT INTO admins (username, password, Token) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sss", $username, $hashed_password, $token);

            if ($stmt->execute()) {
                // Redirect to login page only on successful registration
                header("Location: Admin_Login.php");
                exit();
            } else {
                $usernameErr = "Registration failed. Please try again.";
            }
            $stmt->close();
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
                        Admin Register</h1>
                    <p class="text-[#617589] text-base font-normal leading-normal">Enter your
                        credentials and registration token to create an admin account</p>
                </div>
                <div class="flex flex-col p-8 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <form class="flex flex-col gap-6" method="post"
                        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="registerForm" onsubmit="return validateForm()">

                        <!-- Token Field -->
                        <div class="flex flex-col">
                            <label class="flex flex-col w-full">
                                <p class="text-[#2D3748] text-base font-medium leading-normal pb-2">
                                    Registration Token</p>
                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                    placeholder="Enter your registration token" type="text" name="token"
                                    value="<?php echo htmlspecialchars($token); ?>" id="token" />
                                <div id="tokenError" class="error-message">
                                    <?php echo $tokenErr; ?>
                                </div>
                            </label>
                        </div>

                        <!-- Username Field -->
                        <div class="flex flex-col">
                            <label class="flex flex-col w-full">
                                <p class="text-[#2D3748] text-base font-medium leading-normal pb-2">
                                    Username</p>
                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                    placeholder="Username" type="text" name="username"
                                    value="<?php echo htmlspecialchars($username); ?>" id="username" />
                                <div id="usernameError" class="error-message">
                                    <?php echo $usernameErr; ?>
                                </div>
                            </label>
                        </div>

                        <!-- Password Fields -->
                        <div class="flex flex-col">
                            <label class="flex flex-col w-full">
                                <p class="text-[#2D3748] text-base font-medium leading-normal pb-2">
                                    Password</p>
                                <div class="relative flex w-full flex-1 items-stretch">
                                    <input
                                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                        placeholder="Enter your password" name="password" type="password"
                                        value="<?php echo htmlspecialchars($password); ?>" id="password" />
                                    <button class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#617589]"
                                        type="button" onclick="show_Password()">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6">
                                            <path
                                                d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                            <path
                                                d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                            <path
                                                d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                                        </svg>
                                    </button>
                                </div>
                                <div id="passwordError" class="error-message">
                                    <?php echo $passwordErr; ?>
                                </div>
                            </label>

                            <label class="flex flex-col w-full mt-4">
                                <p class="text-[#2D3748] text-base font-medium leading-normal pb-2">
                                    Confirm Password</p>
                                <div class="relative flex w-full flex-1 items-stretch">
                                    <input
                                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#2D3748] focus:outline-0 border border-[#dbe0e6] bg-background-light focus:border-primary focus:ring-2 focus:ring-primary/30 h-14 placeholder:text-[#617589] p-[15px] text-base font-normal leading-normal transition-all"
                                        placeholder="Confirm your password" type="password" name="Confirm_password"
                                        value="<?php echo htmlspecialchars($Confirm_password); ?>"
                                        id="Confirm_password" />
                                    <button class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#617589]"
                                        type="button" onclick="show_Password()">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6">
                                            <path
                                                d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                            <path
                                                d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                            <path
                                                d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                                        </svg>
                                    </button>
                                </div>
                                <div id="confirmPasswordError" class="error-message">
                                    <?php echo $Confirm_passwordErr; ?>
                                </div>
                            </label>
                        </div>

                        <button
                            class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 px-4 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors"
                            type="submit" name="submit">
                            <span class="truncate">Register</span>
                        </button>
                    </form>

                    <a class="text-primary text-lg font-normal leading-normal pt-2 text-center hover:underline"
                        href="./Admin_login.php">Login</a>
                </div>
            </div>
        </main>
    </div>
</body>

<script>
    function show_Password() {
        const x = document.getElementById("password");
        const y = document.getElementById("Confirm_password");

        if (x.type === "password") {
            x.type = "text";
            y.type = "text";
        } else {
            x.type = "password";
            y.type = "password";
        }
    }

    // JavaScript form validation
    function validateForm() {
        // Get form values
        const token = document.getElementById('token').value.trim();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('Confirm_password').value;
        
        // Clear previous error messages
        clearErrors();
        
        let isValid = true;

        // Validate Token
        if (token === '') {
            showError('tokenError', 'Registration token is required');
            isValid = false;
        } else if (!/^[A-Za-z0-9\s]+$/.test(token)) {
            showError('tokenError', 'Only letters, numbers and white space allowed');
            isValid = false;
        }

        // Validate Username
        if (username === '') {
            showError('usernameError', 'Username is required');
            isValid = false;
        } else if (!/^[A-Za-z0-9\s]+$/.test(username)) {
            showError('usernameError', 'Only letters, numbers and white space allowed');
            isValid = false;
        } else if (username.length < 3) {
            showError('usernameError', 'Username must be at least 3 characters long');
            isValid = false;
        }

        // Validate Password
        if (password === '') {
            showError('passwordError', 'Password is required');
            isValid = false;
        } else if (password.length < 6) {
            showError('passwordError', 'Password must be at least 6 characters long');
            isValid = false;
        }

        // Validate Confirm Password
        if (confirmPassword === '') {
            showError('confirmPasswordError', 'Please confirm your password');
            isValid = false;
        } else if (password !== confirmPassword) {
            showError('confirmPasswordError', 'Passwords do not match');
            isValid = false;
        }

        return isValid;
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function clearErrors() {
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(element => {
            element.textContent = '';
        });
    }

    // Real-time validation (optional - as user types)
    document.getElementById('token').addEventListener('input', function() {
        const token = this.value.trim();
        if (token !== '' && !/^[A-Za-z0-9\s]+$/.test(token)) {
            showError('tokenError', 'Only letters, numbers and white space allowed');
        } else {
            document.getElementById('tokenError').textContent = '';
        }
    });

    document.getElementById('username').addEventListener('input', function() {
        const username = this.value.trim();
        if (username !== '' && !/^[A-Za-z0-9\s]+$/.test(username)) {
            showError('usernameError', 'Only letters, numbers and white space allowed');
        } else {
            document.getElementById('usernameError').textContent = '';
        }
    });

    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        if (password !== '' && password.length < 6) {
            showError('passwordError', 'Password must be at least 6 characters');
        } else {
            document.getElementById('passwordError').textContent = '';
        }
    });

    document.getElementById('Confirm_password').addEventListener('input', function() {
        const confirmPassword = this.value;
        const password = document.getElementById('password').value;
        if (confirmPassword !== '' && password !== confirmPassword) {
            showError('confirmPasswordError', 'Passwords do not match');
        } else {
            document.getElementById('confirmPasswordError').textContent = '';
        }
    });
</script>

</html>