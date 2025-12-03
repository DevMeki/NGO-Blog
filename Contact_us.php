<?php
include 'Backend/Config.php';
session_start();
require_once 'Backend/track_visits.php';

// Define variables and set to empty values
$Name = $Email = $Subject = $Message = "";
$NameErr = $EmailErr = $SubjectErr = $MessageErr = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Check if form is being submitted
    error_log("Form submitted via POST method");

    // Validate Name
    if (empty($_POST["Name"])) {
        $NameErr = "Name can not be empty";
    } else {
        $y = $_POST["Name"];
        if (!preg_match("/^[A-Za-z0-9\s]+$/", $y)) {
            $NameErr = "Only letters, numbers and white space allowed";
        } else {
            $Name = test_input($_POST["Name"]);
        }
    }

    // Validate Email
    if (empty($_POST["Email"])) {
        $EmailErr = "Email is required";
    } else {
        $x = $_POST['Email'];
        if (!preg_match("/^\S+@\S+\.\S+$/", $x)) {
            $EmailErr = "Please enter a valid email address";
        } else {
            $Email = test_input($_POST["Email"]);
        }
    }

    // Validate Subject
    if (empty($_POST["Subject"])) {
        $SubjectErr = "Subject can not be empty";
    } else {
        $y = $_POST["Subject"];
        if (!preg_match("/^[A-Za-z0-9\s]+$/", $y)) {
            $SubjectErr = "Only letters, numbers and white space allowed";
        } else {
            $Subject = test_input($_POST["Subject"]);
        }
    }

    // Validate Message
    if (empty($_POST["Message"])) {
        $MessageErr = "Message can not be empty";
    } else {
        $y = $_POST["Message"];
        // Removed the restrictive validation for message to allow punctuation and special characters
        $Message = test_input($_POST["Message"]);
    }

    // Debug: Check validation results
    error_log("Validation - NameErr: $NameErr, EmailErr: $EmailErr, SubjectErr: $SubjectErr, MessageErr: $MessageErr");

    if (empty($NameErr) && empty($EmailErr) && empty($SubjectErr) && empty($MessageErr)) {
        // Debug: Ready to insert
        error_log("All validation passed, attempting database insert");

        $insertQuery = "INSERT INTO inquiry (Name, Email, Subject, Message) VALUES (?, ?, ?, ?)";

        // Debug: Check if query preparation works
        if ($stmt = $conn->prepare($insertQuery)) {
            $stmt->bind_param("ssss", $Name, $Email, $Subject, $Message);

            if ($stmt->execute()) {
                // Debug: Success
                error_log("Inquiry successfully inserted into database");

                // Set success message instead of immediate redirect
                $success_message = "Thank you! Your message has been sent successfully.";

                // Clear form fields
                $Name = $Email = $Subject = $Message = "";

                // Optional: You can still redirect after showing success message
                // header("Location: Contact_us.php?success=1");
                // exit();
            } else {
                // Debug: Execute failed
                error_log("Execute failed: " . $stmt->error);
                $NameErr = "Sending Inquiry failed. Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Debug: Prepare failed
            error_log("Prepare failed: " . $conn->error);
            $NameErr = "Sending Inquiry failed. Please try again. Error: " . $conn->error;
        }
    } else {
        // Debug: Validation failed
        error_log("Form validation failed");
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Debug: Check database connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    $NameErr = "Database connection error. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./style.css" rel="stylesheet">
    <title>Contact Us</title>

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

</head>

<body class="bg-gray-100 font-(family-name: Public Sans, sans-serif)">
    <?php include 'Header.php'; ?>

    <main class="flex-1 px-4 py-8 md:px-10 lg:px-20 xl:px-40">
        <div class="layout-content-container flex flex-col max-w-7xl mx-auto">
            <div class="flex flex-wrap justify-between gap-3 p-4">
                <div class="flex min-w-72 flex-col gap-3">
                    <p class="text-blue-600 text-4xl font-black leading-tight tracking-[-0.033em]">
                        Get in Touch</p>
                    <p class="text-gray-600 text-base font-normal leading-normal">
                        We'd love to hear from you. Please fill out the form below or use our contact details to
                        get in touch.
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mt-8 p-4">
                <div class="flex flex-col gap-6 p-8 rounded-xl bg-white shadow-sm">
                    <!-- Add this after the form opening tag to show success message -->
                    <?php if (!empty($success_message)): ?>
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <form class="flex flex-col gap-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                        id="Inquiry_Form" onsubmit="return Validate_Form()" method="POST">

                        <!-- Name Field -->
                        <label class="flex flex-col w-full">
                            <p class="text-blue-600 text-base font-medium leading-normal pb-2">Name</p>
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-blue-500 border border-gray-300 bg-white focus:border-blue-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                id="Name" name="Name" placeholder="Enter your name"
                                value="<?php echo htmlspecialchars($Name); ?>" />
                            <div id="NameError" class="mt-1 text-red-600 text-sm">
                                <?php echo $NameErr; ?>
                            </div>
                        </label>

                        <!-- Email Field -->
                        <label class="flex flex-col w-full">
                            <p class="text-blue-600 text-base font-medium leading-normal pb-2">Email</p>
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-blue-500 border border-gray-300 bg-white focus:border-blue-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                id="Email" name="Email" placeholder="Enter your email address" type="email"
                                value="<?php echo htmlspecialchars($Email); ?>" />
                            <div id="EmailError" class="mt-1 text-red-600 text-sm">
                                <?php echo $EmailErr; ?>
                            </div>
                        </label>

                        <!-- Subject Field -->
                        <label class="flex flex-col w-full">
                            <p class="text-blue-600 text-base font-medium leading-normal pb-2">Subject</p>
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-blue-500 border border-gray-300 bg-white focus:border-blue-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                id="Subject" name="Subject" placeholder="Enter the subject of your message" type="text"
                                value="<?php echo htmlspecialchars($Subject); ?>" />
                            <div id="SubjectError" class="mt-1 text-red-600 text-sm">
                                <?php echo $SubjectErr; ?>
                            </div>
                        </label>

                        <!-- Message Field -->
                        <label class="flex flex-col w-full">
                            <p class="text-blue-600 text-base font-medium leading-normal pb-2">Message</p>
                            <textarea
                                class="form-textarea flex w-full min-w-0 flex-1 resize-y overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-blue-500 border border-gray-300 bg-white focus:border-blue-600 h-36 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                id="Message" name="Message"
                                placeholder="Enter your message"><?php echo htmlspecialchars($Message); ?></textarea>
                            <div id="MessageError" class="mt-1 text-red-600 text-sm">
                                <?php echo $MessageErr; ?>
                            </div>
                        </label>

                        <button
                            class="flex items-center justify-center rounded-lg h-12 px-6 bg-blue-600 text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                            type="submit" name="submit">
                            Submit
                        </button>
                    </form>
                </div>
                <div class="flex flex-col gap-8">
                    <div class="p-8 rounded-xl bg-white shadow-sm">
                        <h3 class="text-xl font-bold text-blue-600 mb-6">Contact Information
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex items-center justify-center size-10 rounded-full bg-blue-200 text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>

                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Email</p>
                                    <a class="text-gray-900 font-medium hover:text-blue-600"
                                        href="mailto:cinydirect@cinyforum.org">cinydirect@cinyforum.org</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex items-center justify-center size-10 rounded-full bg-blue-200 text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                    </svg>

                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Phone</p>
                                    <a class="text-gray-900 font-medium hover:text-blue-600 dark:hover:text-blue-600"
                                        href="tel:+2348100688174">+234 8100 688 174</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex items-center justify-center size-10 rounded-full bg-blue-200 text-blue-600">
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
                                    <p class="text-gray-900 font-medium">Igbo-Eze North , Enugu, Nigeria</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="rounded-xl overflow-hidden shadow-sm h-80">
                        <iframe allowfullscreen="" class="w-full h-full border-0" data-location="Times Square"
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.219575191263!2d-73.9878440847047!3d40.75889497932691!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6451633%3A0x2a210e783ae40713!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1678886567890!5m2!1sen!2sus"></iframe>
                    </div> -->
                </div>
            </div>
        </div>
    </main>

    <?php require 'Footer.php'; ?>

    <script>
        // JavaScript form validation
        function Validate_Form() {
            // Get form values
            const Name = document.getElementById('Name').value.trim();
            const Email = document.getElementById('Email').value.trim();
            const Subject = document.getElementById('Subject').value.trim();
            const Message = document.getElementById('Message').value.trim();

            // Clear previous error messages
            clearErrors();

            let isValid = true;

            // Validate Name
            if (Name === '') {
                showError('NameError', 'Name is required');
                isValid = false;
            } else if (!/^[A-Za-z\s]+$/.test(Name)) {
                showError('NameError', 'Only letters and spaces allowed for name');
                isValid = false;
            }

            // Validate Email
            if (Email === '') {
                showError('EmailError', 'Email is required');
                isValid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(Email)) {
                showError('EmailError', 'Please enter a valid email address');
                isValid = false;
            }

            // Validate Subject
            if (Subject === '') {
                showError('SubjectError', 'Subject is required');
                isValid = false;
            }

            // Validate Message
            if (Message === '') {
                showError('MessageError', 'Message is required');
                isValid = false;
            }

            return isValid;
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
            }
        }

        function clearErrors() {
            const errorElements = document.querySelectorAll('[id$="Error"]');
            errorElements.forEach(element => {
                element.textContent = '';
            });
        }

        // Real-time validation as user types
        document.addEventListener('DOMContentLoaded', function () {
            // Name real-time validation
            document.getElementById('Name').addEventListener('input', function () {
                const Name = this.value.trim();
                const errorElement = document.getElementById('NameError');

                if (Name === '') {
                    showError('NameError', '');
                } else if (!/^[A-Za-z\s]+$/.test(Name)) {
                    showError('NameError', 'Only letters and spaces allowed');
                } else {
                    showError('NameError', '');
                }
            });

            // Email real-time validation
            document.getElementById('Email').addEventListener('input', function () {
                const Email = this.value.trim();
                const errorElement = document.getElementById('EmailError');

                if (Email === '') {
                    showError('EmailError', '');
                } else if (!/^\S+@\S+\.\S+$/.test(Email)) {
                    showError('EmailError', 'Please enter a valid email address');
                } else {
                    showError('EmailError', '');
                }
            });

            // Subject real-time validation
            document.getElementById('Subject').addEventListener('input', function () {
                const Subject = this.value.trim();
                const errorElement = document.getElementById('SubjectError');

                if (Subject === '') {
                    showError('SubjectError', '');
                } else {
                    showError('SubjectError', '');
                }
            });

            // Message real-time validation
            document.getElementById('Message').addEventListener('input', function () {
                const Message = this.value.trim();
                const errorElement = document.getElementById('MessageError');

                if (Message === '') {
                    showError('MessageError', '');
                } else {
                    showError('MessageError', '');
                }
            });

            // Real-time validation on blur (when user leaves field)
            document.getElementById('Name').addEventListener('blur', function () {
                const Name = this.value.trim();
                if (Name === '') {
                    showError('NameError', 'Name is required');
                } else if (!/^[A-Za-z\s]+$/.test(Name)) {
                    showError('NameError', 'Only letters and spaces allowed');
                }
            });

            document.getElementById('Email').addEventListener('blur', function () {
                const Email = this.value.trim();
                if (Email === '') {
                    showError('EmailError', 'Email is required');
                } else if (!/^\S+@\S+\.\S+$/.test(Email)) {
                    showError('EmailError', 'Please enter a valid email address');
                }
            });

            document.getElementById('Subject').addEventListener('blur', function () {
                const Subject = this.value.trim();
                if (Subject === '') {
                    showError('SubjectError', 'Subject is required');
                }
            });

            document.getElementById('Message').addEventListener('blur', function () {
                const Message = this.value.trim();
                if (Message === '') {
                    showError('MessageError', 'Message is required');
                }
            });
        });

        // Enhanced real-time validation with visual feedback
        document.addEventListener('DOMContentLoaded', function () {
            const fields = ['Name', 'Email', 'Subject', 'Message'];

            fields.forEach(field => {
                const input = document.getElementById(field);
                const errorElement = document.getElementById(field + 'Error');

                if (input) {
                    // Add input event listener for real-time validation
                    input.addEventListener('input', function () {
                        validateField(field, this.value.trim());
                    });

                    // Add blur event listener for final validation
                    input.addEventListener('blur', function () {
                        validateField(field, this.value.trim(), true);
                    });

                    // Add focus event to clear error when user starts typing
                    input.addEventListener('focus', function () {
                        if (errorElement.textContent !== '') {
                            errorElement.textContent = '';
                        }
                    });
                }
            });
        });

        function validateField(fieldName, value, isBlur = false) {
            const errorElement = document.getElementById(fieldName + 'Error');
            const inputElement = document.getElementById(fieldName);

            // Remove any existing styling
            inputElement.classList.remove('border-red-500', 'border-green-500');

            switch (fieldName) {
                case 'Name':
                    if (value === '') {
                        if (isBlur) showError('NameError', 'Name is required');
                        inputElement.classList.add('border-red-500');
                    } else if (!/^[A-Za-z\s]+$/.test(value)) {
                        showError('NameError', 'Only letters and spaces allowed');
                        inputElement.classList.add('border-red-500');
                    } else {
                        showError('NameError', '');
                        inputElement.classList.add('border-green-500');
                    }
                    break;

                case 'Email':
                    if (value === '') {
                        if (isBlur) showError('EmailError', 'Email is required');
                        inputElement.classList.add('border-red-500');
                    } else if (!/^\S+@\S+\.\S+$/.test(value)) {
                        showError('EmailError', 'Please enter a valid email address');
                        inputElement.classList.add('border-red-500');
                    } else {
                        showError('EmailError', '');
                        inputElement.classList.add('border-green-500');
                    }
                    break;

                case 'Subject':
                    if (value === '') {
                        if (isBlur) showError('SubjectError', 'Subject is required');
                        inputElement.classList.add('border-red-500');
                    } else {
                        showError('SubjectError', '');
                        inputElement.classList.add('border-green-500');
                    }
                    break;

                case 'Message':
                    if (value === '') {
                        if (isBlur) showError('MessageError', 'Message is required');
                        inputElement.classList.add('border-red-500');
                    } else {
                        showError('MessageError', '');
                        inputElement.classList.add('border-green-500');
                    }
                    break;
            }
        }

        // Optional: Add character count for message
        document.addEventListener('DOMContentLoaded', function () {
            const messageInput = document.getElementById('Message');
            if (messageInput) {
                // Create character count element
                const charCount = document.createElement('div');
                charCount.className = 'text-sm text-gray-500 mt-1 text-right';
                charCount.id = 'charCount';
                charCount.textContent = '0 characters';

                // Insert after the message input
                messageInput.parentNode.insertBefore(charCount, messageInput.nextSibling);

                // Update character count in real-time
                messageInput.addEventListener('input', function () {
                    const count = this.value.length;
                    charCount.textContent = count + ' characters';

                    // Change color based on length
                    if (count === 0) {
                        charCount.className = 'text-sm text-gray-500 mt-1 text-right';
                    } else if (count < 10) {
                        charCount.className = 'text-sm text-red-500 mt-1 text-right';
                    } else if (count < 50) {
                        charCount.className = 'text-sm text-blue-500 mt-1 text-right';
                    } else {
                        charCount.className = 'text-sm text-green-500 mt-1 text-right';
                    }
                });
            }
        });

        // Optional: Add form submission indicator
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('Inquiry_Form');
            const submitBtn = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function () {
                // Add loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Sending...';
                submitBtn.classList.add('opacity-50');

                // Re-enable after 3 seconds (in case of error)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit';
                    submitBtn.classList.remove('opacity-50');
                }, 3000);
            });
        });
    </script>

</body>

</html>