<?php
// Include database configuration
require_once 'Backend/Config.php';
session_start();
require_once 'Backend/track_visits.php';

// Initialize variables
$success_message = "";
$error_message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if it's a donation submission
    if (isset($_POST['submit_donation'])) {
        // Sanitize and validate input
        $firstName = trim(filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING));
        $lastName = trim(filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
        $amount = trim(filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        
        // Basic validation
        $errors = [];
        
        // Validate required fields
        if (empty($firstName)) {
            $errors[] = "First name is required.";
        }
        
        if (empty($lastName)) {
            $errors[] = "Last name is required.";
        }
        
        if (empty($email)) {
            $errors[] = "Email address is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }
        
        if (empty($amount)) {
            $errors[] = "Donation amount is required.";
        } elseif (!is_numeric($amount) || $amount <= 0) {
            $errors[] = "Please enter a valid donation amount.";
        }
        
        // If no validation errors, proceed
        if (empty($errors)) {
            try {
                // Prepare data for database
                $fullName = $firstName . ' ' . $lastName;
                $subject = "Donation";
                
                // Format the amount with currency symbol
                $formattedAmount = "₦" . number_format(floatval($amount), 2);
                
                // Create message with donor information
                $message = "Donor Information:\n";
                $message .= "Full Name: " . $fullName . "\n";
                $message .= "Email: " . $email . "\n";
                $message .= "Phone: " . (!empty($phone) ? $phone : "Not provided") . "\n\n";
                $message .= "Donation Amount: " . $formattedAmount . "\n\n";
                $message .= "This donor has agreed to make a bank transfer donation.";
                
                // Set status and current date
                $status = 'new';
                $currentDate = date('Y-m-d H:i:s');
                
                // Check database connection
                if (!isset($conn)) {
                    throw new Exception("Database connection not available.");
                }
                
                // Prepare SQL statement with proper error handling
                $sql = "INSERT INTO inquiry (Name, Email, Subject, Message, Date_Sent, status) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                
                if ($stmt) {
                    // Bind parameters
                    mysqli_stmt_bind_param($stmt, "ssssss", 
                        $fullName, 
                        $email, 
                        $subject, 
                        $message, 
                        $currentDate, 
                        $status
                    );
                    
                    // Execute statement
                    if (mysqli_stmt_execute($stmt)) {
                        $success_message = "Thank you for your donation of " . $formattedAmount . "! ";
                        $success_message .= "We have received your information and will contact you soon.";
                        
                        // Optionally, you can also log the donation in a separate table
                        // For now, we're just using the inquiry table
                        
                    } else {
                        throw new Exception("Failed to save donation information. Please try again.");
                    }
                    
                    // Close statement
                    mysqli_stmt_close($stmt);
                    
                } else {
                    throw new Exception("Database preparation error: " . mysqli_error($conn));
                }
                
            } catch (Exception $e) {
                // Log the error (for production)
                error_log("Donation Error: " . $e->getMessage());
                
                // User-friendly error message
                $error_message = "Sorry, there was an error processing your donation. ";
                $error_message .= "Please try again or contact support if the problem persists.";
            }
            
        } else {
            // Combine validation errors
            $error_message = implode(" ", $errors);
        }
        
    }
    // You can add more POST handlers here if needed
}

// Check for GET parameters that might indicate a successful donation from elsewhere
if (isset($_GET['donation_success']) && $_GET['donation_success'] == 'true') {
    $success_message = "Thank you for your donation! Your support means a lot to us.";
}

// Include header
require 'Header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./style.css" rel="stylesheet">
    <title>Donate</title>

    <link rel="icon" href="Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="Assets/img/logo bg.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#1f9c7b">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gravitas+One&display=swap" rel="stylesheet">

    <style>
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-enter {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1) translateY(0);
            transition: all 0.2s ease-out;
        }
        
        /* Validation styles */
        .error {
            border-color: #ef4444 !important;
        }
        
        .success {
            border-color: #10b981 !important;
        }
        
        /* Toast animation */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(100%);
            }
        }
        
        .toast-slide-in {
            animation: slideInRight 0.3s ease-out forwards;
        }
        
        .toast-slide-out {
            animation: slideOutRight 0.3s ease-in forwards;
        }
    </style>
</head>

<body class="bg-gray-100 font-[Public_Sans,_sans-serif] text-gray-950">

    <div class="relative flex min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">

            <main class="flex-1">
                <!-- Success and Error Messages -->
                <?php if (!empty($success_message)): ?>
                    <div class="container mx-auto px-4 pt-6">
                        <div class="max-w-3xl mx-auto">
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                                <?php echo htmlspecialchars($success_message); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="container mx-auto px-4 pt-6">
                        <div class="max-w-3xl mx-auto">
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- HeroSection Component -->
                <section class="w-full">
                    <div class="@container">
                        <div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat items-center justify-center p-4 text-center"
                            data-alt="Abstract image with soft, blurred lights in blue and green tones, conveying a sense of hope and community."
                            style='background-image: linear-gradient(rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.5) 100%), url("Assets/img/donate.jpg");'>
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
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="donationForm" novalidate>
                                <div class="flex flex-col gap-8">
                                    <!-- PageHeading Component -->
                                    <div class="text-center">
                                        <p class="text-blue-600 text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">
                                            Support Our Cause.
                                        </p>
                                    </div>
                                    <!-- BodyText Component -->
                                    <p class="text-base font-normal leading-normal text-center text-gray-500">
                                        Enter an amount to support our cause. Every contribution, big or
                                        small, makes a significant impact.
                                    </p>
                                    <!-- Frequency & Amount Selection -->
                                    <div class="flex flex-col gap-6">
                                        <div class="col-span-2 sm:col-span-1">
                                            <input
                                                class="w-full p-3 text-sm font-bold border border-gray-200 rounded-lg focus:ring-blue-600 focus:border-blue-600 bg-gray-100 <?php echo (isset($_POST['amount']) && empty($amount) && !empty($error_message)) ? 'error' : ''; ?>"
                                                placeholder="Amount" 
                                                type="number" 
                                                id="donationAmount" 
                                                name="amount"
                                                min="1" 
                                                step="0.01" 
                                                value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>"
                                                required />
                                            <p class="text-red-500 text-sm mt-1 hidden" id="amountError">Please enter a
                                                valid amount</p>
                                        </div>
                                    </div>
                                    <!-- Personal Information -->
                                    <div class="flex flex-col gap-4">
                                        <h3 class="text-blue-600 text-lg font-bold leading-tight tracking-[-0.015em]">Personal
                                            Information
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input
                                                class="w-full p-3 text-sm border border-gray-200 rounded-lg focus:ring-blue-600 focus:border-blue-600 bg-gray-100 <?php echo (isset($_POST['firstName']) && empty($firstName) && !empty($error_message)) ? 'error' : ''; ?>"
                                                placeholder="First Name" 
                                                type="text" 
                                                id="firstName" 
                                                name="firstName"
                                                value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>"
                                                required />
                                            <input
                                                class="w-full p-3 text-sm border border-gray-200 rounded-lg focus:ring-blue-600 focus:border-blue-600 bg-gray-100 <?php echo (isset($_POST['lastName']) && empty($lastName) && !empty($error_message)) ? 'error' : ''; ?>"
                                                placeholder="Last Name" 
                                                type="text" 
                                                id="lastName" 
                                                name="lastName"
                                                value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>"
                                                required />
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input
                                                class="w-full p-3 text-sm border border-gray-200 rounded-lg focus:ring-blue-600 focus:border-blue-600 bg-gray-100 <?php echo (isset($_POST['email']) && (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) && !empty($error_message)) ? 'error' : ''; ?>"
                                                placeholder="Email Address" 
                                                type="email" 
                                                id="email" 
                                                name="email"
                                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                                required />
                                            <input
                                                class="w-full p-3 text-sm border border-gray-200 rounded-lg focus:ring-blue-600 focus:border-blue-600 bg-gray-100"
                                                placeholder="Phone number" 
                                                type="tel" 
                                                id="phone" 
                                                name="phone"
                                                value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" />
                                        </div>
                                    </div>
                                    <!-- Payment Information -->
                                    <div class="flex flex-col gap-4">
                                        <h3 class="text-blue-600 text-lg font-bold leading-tight tracking-[-0.015em]">Payment Details
                                        </h3>
                                        <div class="p-4 border border-dashed border-gray-200 rounded-lg bg-gray-100">
                                            <p class="text-center text-gray-500 text-sm">
                                                Bank Transfer Payment Method
                                            </p>
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
                                    <!-- Hidden submit field -->
                                    <input type="hidden" name="submit_donation" value="1">

                                    <!-- CTA Button -->
                                    <button type="button" onclick="validateAndOpenModal()"
                                        class="w-full flex min-w-[84px] max-w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-blue-600 text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-blue-700 transition-colors">
                                        <span class="truncate">Donate Now</span>
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </main>
            <!-- Footer with Trust Badges -->
            <footer class="bg-white border-t border-gray-200">
                <div class="container mx-auto px-4 py-8">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                        <p class="text-sm text-gray-600">© <?php echo date('Y'); ?> CINY. All Rights Reserved.</p>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-bold text-gray-600">Your contribution is secure:</span>
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

    <!-- Modal Overlay -->
    <div id="modalOverlay" class="fixed inset-0 modal-overlay z-30 hidden flex items-center justify-center p-4">
        <div id="modalContainer"
            class="modal-enter bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-university text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-600">Bank Transfer Details</h3>
                        <p class="text-sm text-gray-500">Complete your donation via bank transfer</p>
                    </div>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-4 space-y-4">
                <!-- Amount Display -->
                <div class="text-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Transfer Amount</p>
                    <p class="text-2xl font-bold text-blue-600" id="displayAmount">₦0.00</p>
                    <p class="text-sm text-gray-500 mt-1">to the following account:</p>
                </div>

                <!-- Account Details -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-500 flex items-center space-x-2">
                        <i class="fas fa-user text-blue-500"></i>
                        <span>Account Name</span>
                    </label>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-900 font-semibold" id="accountName">CINY</p>
                    </div>
                </div>

                <!-- Account Number -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-500 flex items-center space-x-2">
                        <i class="fas fa-credit-card text-blue-500"></i>
                        <span>Account Number</span>
                    </label>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between">
                        <p class="text-gray-900 font-mono font-semibold tracking-wide" id="accountNumber">704 992 2017
                        </p>
                        <button onclick="copyAccountNumber()"
                            class="text-gray-400 hover:text-blue-600 transition-colors p-1" title="Copy to clipboard">
                            <i class="fas fa-copy text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Bank Name -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-500 flex items-center space-x-2">
                        <i class="fas fa-university text-blue-500"></i>
                        <span>Bank Name</span>
                    </label>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-900 font-semibold" id="bankName">FCMB (First City Monument Bank)</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex space-x-3 p-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button onclick="closeModal()"
                    class="flex-1 py-3 px-4 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                    Cancel
                </button>
                <button onclick="submitDonationForm()"
                    class="flex-1 py-3 px-4 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center justify-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span>I've Transferred</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast"
        class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden transform translate-x-full transition-transform duration-300">
        <div class="flex items-center space-x-3">
            <i class="fas fa-check-circle"></i>
            <span id="toastMessage">Thank you for your donation!</span>
        </div>
    </div>

    <script>
        // Form Validation and Modal Functions
        function validateAndOpenModal() {
            const amountInput = document.getElementById('donationAmount');
            const firstNameInput = document.getElementById('firstName');
            const lastNameInput = document.getElementById('lastName');
            const emailInput = document.getElementById('email');
            const amountError = document.getElementById('amountError');
            
            let isValid = true;
            
            // Reset error states
            amountError.classList.add('hidden');
            amountInput.classList.remove('error', 'success');
            firstNameInput.classList.remove('error', 'success');
            lastNameInput.classList.remove('error', 'success');
            emailInput.classList.remove('error', 'success');
            
            // Validate amount
            const amount = parseFloat(amountInput.value);
            if (!amount || amount <= 0 || isNaN(amount)) {
                amountError.classList.remove('hidden');
                amountInput.classList.add('error');
                amountInput.focus();
                isValid = false;
            } else {
                amountInput.classList.add('success');
            }
            
            // Validate first name
            if (!firstNameInput.value.trim()) {
                firstNameInput.classList.add('error');
                isValid = false;
            } else {
                firstNameInput.classList.add('success');
            }
            
            // Validate last name
            if (!lastNameInput.value.trim()) {
                lastNameInput.classList.add('error');
                isValid = false;
            } else {
                lastNameInput.classList.add('success');
            }
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value)) {
                emailInput.classList.add('error');
                isValid = false;
            } else {
                emailInput.classList.add('success');
            }
            
            // If valid, open modal
            if (isValid) {
                // Update modal with the amount
                updateModalAmount(amount);
                
                // Open modal
                openModal();
            } else {
                // Show validation error toast
                showToast("Please fill all required fields correctly.", "error");
            }
        }

        function updateModalAmount(amount) {
            const displayAmount = document.getElementById('displayAmount');
            displayAmount.textContent = formatCurrency(amount);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN'
            }).format(amount);
        }

        function openModal() {
            const overlay = document.getElementById('modalOverlay');
            const modal = document.getElementById('modalContainer');

            overlay.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('modal-enter');
            }, 10);
            
            // Prevent body scrolling when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const overlay = document.getElementById('modalOverlay');
            const modal = document.getElementById('modalContainer');

            modal.classList.add('modal-enter');
            setTimeout(() => {
                overlay.classList.add('hidden');
                // Restore body scrolling
                document.body.style.overflow = '';
            }, 200);
        }

        // Copy Account Number
        function copyAccountNumber() {
            const accountNumber = document.getElementById('accountNumber').textContent;
            const cleanAccountNumber = accountNumber.replace(/\s/g, '');
            
            // Use the modern Clipboard API
            navigator.clipboard.writeText(cleanAccountNumber).then(() => {
                // Show temporary feedback
                const copyBtn = event.currentTarget;
                const originalIcon = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check text-green-500 text-sm"></i>';
                copyBtn.title = "Copied!";
                
                // Show success toast
                showToast("Account number copied to clipboard!", "success");
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalIcon;
                    copyBtn.title = "Copy to clipboard";
                }, 2000);
            }).catch(err => {
                // Fallback for older browsers
                const tempInput = document.createElement('input');
                tempInput.value = cleanAccountNumber;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                
                // Show feedback
                const copyBtn = event.currentTarget;
                const originalIcon = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check text-green-500 text-sm"></i>';
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalIcon;
                }, 2000);
            });
        }

        // Submit the donation form
        function submitDonationForm() {
            // Submit the form
            document.getElementById('donationForm').submit();
            
            // Close modal
            closeModal();
            
            // Show success message
            showToast("Thank you for your donation! Redirecting...", "success");
        }

        // Show Toast Notification
        function showToast(message, type = "success") {
            const toast = document.getElementById('successToast');
            const toastMessage = document.getElementById('toastMessage');
            
            // Set toast color based on type
            if (type === "error") {
                toast.classList.remove('bg-green-500');
                toast.classList.add('bg-red-500');
            } else {
                toast.classList.remove('bg-red-500');
                toast.classList.add('bg-green-500');
            }
            
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            toast.classList.add('toast-slide-in');
            toast.classList.remove('toast-slide-out');
            toast.classList.remove('translate-x-full');

            setTimeout(() => {
                toast.classList.remove('toast-slide-in');
                toast.classList.add('toast-slide-out');
                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.classList.remove('toast-slide-out');
                }, 300);
            }, 5000);
        }

        // Close modal when clicking outside
        document.getElementById('modalOverlay').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Auto-format amount input
        document.getElementById('donationAmount').addEventListener('input', function (e) {
            const value = e.target.value;
            if (value && !isNaN(value)) {
                updateModalAmount(parseFloat(value));
            }
        });
        
        // Clear error styles when user starts typing
        const formInputs = document.querySelectorAll('#donationForm input');
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });
        
        // Form persistence - restore form data if page was refreshed
        window.addEventListener('load', function() {
            // Check if there was a form submission error
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                showToast("Please correct the errors in the form.", "error");
            }
        });
    </script>

</body>

</html>