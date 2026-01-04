# AI Coding Assistant Instructions for CINY Website

## Project Overview
This is a PHP-based website for "Concerned Igbo-Eze North Youths (CINY)" - a community organization website featuring blog posts, admin management, visitor analytics, and donation/contact functionality.

## Architecture & Tech Stack
- **Frontend**: HTML/PHP with Tailwind CSS, Bootstrap Icons, Chart.js for analytics
- **Backend**: PHP 8.2+ with MySQL (MariaDB) database
- **Server**: XAMPP (Apache + MySQL) for local development
- **Database**: `CINY_DB` with tables: `admins`, `blog_post`, `draft`, `inquiry`, `tokens`, `visitor_logs`

## Key Components
- **Public Pages**: `index.php`, `About_us.php`, `Activities.php`, `Blog.php`, `Contact_us.php`, `Donate.php`, `post.php`
- **Admin Panel**: `Admin/` directory with dashboard, post editor, inquiry management
- **Backend Scripts**: `Backend/` with database config, upload handlers, analytics
- **Assets**: `Assets/` for images, uploads, static files

## Database Schema Patterns
- **blog_post**: Stores published posts with `Image_path` as JSON array of relative paths
- **draft**: Similar to blog_post but for unpublished content
- **admins**: User management with session tokens
- **inquiry**: Contact form submissions
- **visitor_logs**: Analytics tracking (IP, user agent, page visits)

## Development Workflow
- **Local Setup**: Run via XAMPP on `localhost/Tailwind_Css/Concerned_youths/`
- **No Build Process**: Direct PHP execution, no compilation required
- **Database**: Import `ciny_db.sql` for initial schema/data
- **File Uploads**: Images stored in `Assets/uploads/` with 5MB limit (JPG/PNG/GIF only)

## Coding Conventions
- **File Naming**: Mixed case PHP files (`Admin_Dashboard.php`), snake_case for some (`post_upload.php`)
- **Database**: mysqli with prepared statements for security
- **Sessions**: Used for admin authentication (`$_SESSION['username']`, `$_SESSION['admin_id']`)
- **Includes**: `require_once 'Backend/Config.php'` for database connection
- **Error Handling**: Basic try/catch, error_log for debugging
- **Image Handling**: Multiple file uploads, unique filenames with `uniqid()`, JSON storage in DB

## Common Patterns
- **Post Display**: Query `blog_post` table, decode JSON `Image_path`, display with pagination
- **Admin Auth**: Check `$_SESSION['username']` at top of admin pages, redirect to login if missing
- **Search/Filter**: Use prepared statements with LIKE for title/content search, category filtering
- **Analytics**: Call `trackPageVisit($conn)` on page load to log visits
- **File Paths**: Absolute paths for server operations (`dirname(__DIR__) . '/Assets/uploads/'`), relative for web (`'Assets/uploads/' . $filename`)

## Security Considerations
- **Input Sanitization**: `trim()` for basic cleaning, prepared statements prevent SQL injection
- **File Upload Security**: Type/size validation, unique filenames prevent conflicts
- **Session Management**: Proper session start/destroy for admin logout
- **Access Control**: Admin-only pages check session before displaying content

## UI/UX Patterns
- **Responsive Design**: Mobile-first with Tailwind classes, separate mobile/desktop backgrounds
- **Animations**: CSS fade-in effects with intersection observer
- **Navigation**: Sticky header with mobile sidebar toggle
- **Forms**: Client-side validation with PHP server-side checks

## Deployment Notes
- **Static Assets**: Ensure `Assets/` directory is writable for uploads
- **Database**: Update connection credentials in `Backend/Config.php` for production
- **Paths**: Adjust file paths if deploying to subdirectory
- **HTTPS**: Update any hardcoded HTTP URLs to HTTPS in production

## Example Code Patterns

**Database Query with Prepared Statement:**
```php
$sql = "SELECT post_id, Title, Content FROM blog_post WHERE Categories = ? ORDER BY Date_posted DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
```

**Image Upload Handling:**
```php
$uploaded_paths = [];
foreach ($_FILES['post_images']['name'] as $i => $file_name) {
    // validation...
    $new_file_name = uniqid('post_img_', true) . '.' . $file_ext;
    $file_destination = $upload_dir . $new_file_name;
    if (move_uploaded_file($_FILES['post_images']['tmp_name'][$i], $file_destination)) {
        $uploaded_paths[] = 'Assets/uploads/' . $new_file_name;
    }
}
// Store as JSON: json_encode($uploaded_paths)
```

**Session Check:**
```php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}
```

**Visitor Tracking:**
```php
require_once 'Backend/track_visits.php'; // Automatically tracks on include
```</content>
<parameter name="filePath">c:\xampp\htdocs\Tailwind_Css\Concerned_youths\.github\copilot-instructions.md