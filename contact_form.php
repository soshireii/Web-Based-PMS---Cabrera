<?php
require 'db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($name && $email && $subject && $message) {
        $stmt = $conn->prepare("INSERT INTO contacts (ContactName, ContactEmail, ContactSubject, ContactMessage) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        $stmt->execute();
        $msg = 'success';
    } else {
        $msg = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form | Web-Based PMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-0">
        <a class="navbar-brand" href="index.php">Contacts</a>
        <button class="navbar-toggler border-0 d-lg-none" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="toggler-bar"></span>
            <span class="toggler-bar"></span>
            <span class="toggler-bar toggler-bar--short"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_projects.php">Projects</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_skills_experience.php">Skills & Experience</a></li>
                <li class="nav-item"><a class="nav-link active" href="contact_form.php">Contact Form</a></li>
                <li class="nav-item"><a class="nav-link" href="view_projects.php">View Portfolio</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="section-label">Get In Touch</div>
    <div class="page-title">Contact</div>
</div>

<div class="page-body">
    <div class="contact-page-grid">

        <!-- Left: Info -->
        <div class="contact-info-col">
            <p class="contact-intro">
                Have a project in mind or just want to say hello? Fill out the form and I'll get back to you as soon as possible.
            </p>

            <div class="contact-detail">
                <div class="label">Email</div>
                <div class="value">j.cabrera.555524@umindanao.edu.ph</div>
            </div>
            <div class="contact-detail">
                <div class="label">Location</div>
                <div class="value">Philippines</div>
            </div>
            <div class="contact-detail">
                <div class="label">Availability</div>
                <div class="value">Open for freelance work</div>
            </div>
        </div>

        <!-- Right: Form -->
        <div class="contact-form-col">

            <?php if ($msg === 'success'): ?>
            <div class="alert alert-success">
                Message sent! I'll get back to you shortly.
            </div>
            <?php elseif ($msg === 'error'): ?>
            <div class="alert alert-error">
                Please fill in all fields before submitting.
            </div>
            <?php endif; ?>

            <form method="POST" id="contactForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Your name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="What's this about?" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="6" placeholder="Tell me more..." required></textarea>
                </div>
                <div style="margin-top:0.5rem;">
                    <button type="submit" class="btn-submit">Send Message</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Inbox preview for admin (read-only) -->
<?php
$contacts = $conn->query("SELECT * FROM contacts ORDER BY ContactID DESC");
if ($contacts && $contacts->num_rows > 0):
?>
<div class="page-body" style="padding-top:0;">
    <div class="table-wrapper">
        <div class="table-header">
            <span class="table-title">Inbox <span style="color:var(--muted);font-weight:400;">(<?= $contacts->num_rows ?> message<?= $contacts->num_rows != 1 ? 's' : '' ?>)</span></span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($c = $contacts->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($c['ContactName']) ?></td>
                <td style="color:var(--muted);font-size:0.82rem;"><?= htmlspecialchars($c['ContactEmail']) ?></td>
                <td style="color:var(--muted);font-size:0.82rem;"><?= htmlspecialchars($c['ContactSubject']) ?></td>
                <td style="color:var(--muted);font-size:0.82rem;max-width:280px;">
                    <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;" title="<?= htmlspecialchars($c['ContactMessage']) ?>">
                        <?= htmlspecialchars($c['ContactMessage']) ?>
                    </div>
                </td>
                <td>
                    <a href="?delete_msg=<?= $c['ContactID'] ?>" class="btn-action btn-danger-action"
                       onclick="return confirm('Delete this message?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif;

// Handle delete from inbox
if (isset($_GET['delete_msg'])) {
    $id = (int)$_GET['delete_msg'];
    $conn->query("DELETE FROM contacts WHERE ContactID = $id");
    header("Location: contact_form.php");
    exit;
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>