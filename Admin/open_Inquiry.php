<?php
session_start();
require_once '../Backend/Config.php';
// require_once '../Backend/track_visits.php';

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}

// Get inquiry ID from URL
$inquiry_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($inquiry_id <= 0) {
    die("Invalid inquiry ID");
}

// Fetch inquiry details
$inquiry = [];
$error = '';

try {
    // First, mark the inquiry as read
    $update_sql = "UPDATE inquiry SET status = 'read' WHERE Inquiry_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $inquiry_id);
    $stmt->execute();
    $stmt->close();

    // Fetch inquiry details
    $sql = "SELECT Inquiry_id, Name, Email, Subject, Message, Date_Sent, status 
            FROM inquiry 
            WHERE Inquiry_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $inquiry_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $inquiry = $result->fetch_assoc();
    } else {
        $error = "Inquiry not found";
    }
    $stmt->close();
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Inquiry Details - <?php echo htmlspecialchars($inquiry['Name'] ?? 'Unknown'); ?></title>

    <link rel="icon" href="../Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="../Assets/img/logo bg.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#022950",
                        "background-light": "#f5f7f8",
                        "background-dark": "#0f1923",
                        "destructive": "#D9534F"
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
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

<body class="font-display bg-background-light dark:bg-background-dark text-[#111418] dark:text-white">
    <div class="relative flex min-h-screen w-full flex-col">
        <div class="layout-container flex h-full grow flex-col">
            <div class="flex flex-1 justify-center py-8 px-4 sm:px-6 lg:px-8">
                <div class="layout-content-container flex w-full max-w-6xl flex-1 flex-col gap-8">
                    <!-- Error Message -->
                    <?php if (!empty($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Header Section -->
                    <header>
                        <!-- Back to Inquiries Link -->
                        <a class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-[#5f758c] dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors"
                            href="Inquiries.php">
                            <span class="material-symbols-outlined text-base">arrow_back</span>
                            Back to Inquiries
                        </a>
                        <!-- Breadcrumbs -->
                        <div class="flex flex-wrap gap-2">
                            <a class="text-[#5f758c] dark:text-gray-400 text-sm font-medium leading-normal" href="#">Admin</a>
                            <span class="text-[#5f758c] dark:text-gray-400 text-sm font-medium leading-normal">/</span>
                            <a class="text-[#5f758c] dark:text-gray-400 text-sm font-medium leading-normal" href="Inquiries.php">Inquiries</a>
                            <span class="text-[#5f758c] dark:text-gray-400 text-sm font-medium leading-normal">/</span>
                            <span class="text-[#111418] dark:text-white text-sm font-medium leading-normal">
                                <?php echo htmlspecialchars($inquiry['Name'] ?? 'Unknown'); ?>
                            </span>
                        </div>
                    </header>

                    <!-- Main Content Grid -->
                    <main class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        <!-- Left Column: Details -->
                        <aside class="flex flex-col gap-6 lg:col-span-1">
                            <!-- Sender Details Card -->
                            <div class="rounded-lg border border-[#dbe0e6] dark:border-gray-700 bg-white dark:bg-background-dark p-6">
                                <h3 class="text-[#111418] dark:text-white text-lg font-bold leading-tight tracking-[-0.015em] mb-4">
                                    Sender Details</h3>
                                <div class="grid grid-cols-1">
                                    <div class="border-t border-t-[#dbe0e6] dark:border-gray-700 py-4">
                                        <p class="text-[#5f758c] dark:text-gray-400 text-xs font-normal leading-normal mb-1">
                                            Full Name</p>
                                        <p class="text-[#111418] dark:text-white text-sm font-medium leading-normal" id="senderName">
                                            <?php echo htmlspecialchars($inquiry['Name'] ?? 'N/A'); ?>
                                        </p>
                                    </div>
                                    <div class="border-t border-t-[#dbe0e6] dark:border-gray-700 py-4">
                                        <p class="text-[#5f758c] dark:text-gray-400 text-xs font-normal leading-normal mb-1">
                                            Email Address</p>
                                        <p class="text-[#111418] dark:text-white text-sm font-medium leading-normal" id="senderEmail">
                                            <?php echo htmlspecialchars($inquiry['Email'] ?? 'N/A'); ?>
                                        </p>
                                    </div>
                                    <div class="border-t border-t-[#dbe0e6] dark:border-gray-700 py-4">
                                        <p class="text-[#5f758c] dark:text-gray-400 text-xs font-normal leading-normal mb-1">
                                            Inquiry ID</p>
                                        <p class="text-[#111418] dark:text-white text-sm font-medium leading-normal" id="inquiryId">
                                            #<?php echo htmlspecialchars($inquiry['Inquiry_id'] ?? 'N/A'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Inquiry Details Card -->
                            <div class="rounded-lg border border-[#dbe0e6] dark:border-gray-700 bg-white dark:bg-background-dark p-6">
                                <h3 class="text-[#111418] dark:text-white text-lg font-bold leading-tight tracking-[-0.015em] mb-4">
                                    Inquiry Details</h3>
                                <div class="grid grid-cols-1">
                                    <div class="border-t border-t-[#dbe0e6] dark:border-gray-700 py-4">
                                        <p class="text-[#5f758c] dark:text-gray-400 text-xs font-normal leading-normal mb-1">
                                            Date Received</p>
                                        <p class="text-[#111418] dark:text-white text-sm font-medium leading-normal" id="dateSent">
                                            <?php 
                                            if (isset($inquiry['Date_Sent'])) {
                                                echo date('F j, Y', strtotime($inquiry['Date_Sent']));
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="border-t border-t-[#dbe0e6] dark:border-gray-700 py-4">
                                        <p class="text-[#5f758c] dark:text-gray-400 text-xs font-normal leading-normal mb-1">
                                            Status</p>
                                        <span id="statusBadge" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                                            <?php 
                                            $status = $inquiry['status'] ?? 'new';
                                            if ($status === 'read') {
                                                echo 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300';
                                            } else {
                                                echo 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300';
                                            }
                                            ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </aside>
                        <!-- Right Column: Inquiry Message & Actions -->
                        <div class="lg:col-span-2">
                            <div class="rounded-lg border border-[#dbe0e6] dark:border-gray-700 bg-white dark:bg-background-dark">
                                <!-- Message Header -->
                                <div class="flex flex-col sm:flex-row flex-wrap items-start sm:items-center justify-between gap-4 p-6 border-b border-[#dbe0e6] dark:border-gray-700">
                                    <div class="flex min-w-72 flex-col gap-1">
                                        <p class="text-[#111418] dark:text-white text-2xl font-bold leading-tight tracking-[-0.02em]" id="inquirySubject">
                                            <?php echo htmlspecialchars($inquiry['Subject'] ?? 'No Subject'); ?>
                                        </p>
                                        <p class="text-[#5f758c] dark:text-gray-400 text-sm font-normal leading-normal">
                                            Inquiry from <span id="displayName"><?php echo htmlspecialchars($inquiry['Name'] ?? 'Unknown'); ?></span>
                                        </p>
                                    </div>
                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2">
                                        <button onclick="replyToInquiry()"
                                            class="flex items-center justify-center gap-2 whitespace-nowrap rounded-md bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 disabled:pointer-events-none disabled:opacity-50">
                                            <span class="material-symbols-outlined text-base">reply</span>
                                            Reply
                                        </button>
                                        <button onclick="deleteInquiry()"
                                            class="flex items-center justify-center gap-2 whitespace-nowrap rounded-md border border-transparent bg-transparent px-4 py-2 text-sm font-medium text-destructive transition-colors hover:bg-destructive/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-destructive/50">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                                <!-- Message Body -->
                                <div class="p-6">
                                    <div class="prose prose-sm dark:prose-invert max-w-none text-[#111418] dark:text-gray-300 leading-relaxed" id="inquiryMessage">
                                        <?php 
                                        if (isset($inquiry['Message'])) {
                                            // Convert line breaks to paragraphs for better formatting
                                            $message = htmlspecialchars($inquiry['Message']);
                                            $message = nl2br($message);
                                            echo $message;
                                        } else {
                                            echo '<p>No message content available.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Toast -->
    <div id="toast" class="fixed top-4 right-4 p-4 rounded-lg text-white shadow-lg transition-opacity duration-300 opacity-0 pointer-events-none z-50"></div>

    <script>
        // Inquiry data from PHP
        const inquiryData = {
            id: <?php echo $inquiry_id; ?>,
            name: "<?php echo addslashes($inquiry['Name'] ?? ''); ?>",
            email: "<?php echo addslashes($inquiry['Email'] ?? ''); ?>",
            subject: "<?php echo addslashes($inquiry['Subject'] ?? ''); ?>",
            message: `<?php echo addslashes($inquiry['Message'] ?? ''); ?>`,
            dateSent: "<?php echo addslashes($inquiry['Date_Sent'] ?? ''); ?>",
            status: "<?php echo addslashes($inquiry['status'] ?? 'new'); ?>"
        };

        // Action Functions
        function replyToInquiry() {
            const subject = `Re: ${inquiryData.subject}`;
            const body = `\n\n--- Original Message ---\nFrom: ${inquiryData.name} (${inquiryData.email})\nSent: ${inquiryData.dateSent}\nSubject: ${inquiryData.subject}\n\n${inquiryData.message}`;
            
            const mailtoLink = `mailto:${inquiryData.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            window.location.href = mailtoLink;
            
            showToast('Opening email client...', 'info');
        }

        function deleteInquiry() {
            if (confirm('Are you sure you want to delete this inquiry? This action cannot be undone.')) {
                // Send AJAX request to delete the inquiry
                fetch(`../Backend/delete_inquiry.php?id=${inquiryData.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Inquiry deleted successfully', 'success');
                        // Redirect back to inquiries list after 2 seconds
                        setTimeout(() => {
                            window.location.href = 'Inquiries.php';
                        }, 2000);
                    } else {
                        showToast('Failed to delete inquiry: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Network error. Please try again.', 'error');
                });
            }
        }

        // Utility Functions
        function showToast(message, type) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `fixed top-4 right-4 p-4 rounded-lg text-white shadow-lg transition-opacity duration-300 z-50 ${
                type === 'success' ? 'bg-green-600' : 
                type === 'error' ? 'bg-red-600' : 
                'bg-blue-600'
            } opacity-100`;
            
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
            }, 3000);
        }

        // Format message with proper line breaks
        function formatMessage(message) {
            return message.replace(/\n/g, '<br>');
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inquiry loaded:', inquiryData);
        });
    </script>
</body>

</html>