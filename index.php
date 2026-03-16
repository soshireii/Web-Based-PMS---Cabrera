<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Web-Based PMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
$projects    = $conn->query("SELECT COUNT(*) AS c FROM ProjectTBL")->fetch_assoc()['c'] ?? 0;
$skills      = $conn->query("SELECT COUNT(*) AS c FROM SkillsTBL")->fetch_assoc()['c'] ?? 0;
$experiences = $conn->query("SELECT COUNT(*) AS c FROM ExperiencesTBL")->fetch_assoc()['c'] ?? 0;
$contacts    = $conn->query("SELECT COUNT(*) AS c FROM contacts")->fetch_assoc()['c'] ?? 0;
?>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-0">
        <a class="navbar-brand" href="index.php">Dashboard</a>
        <button class="navbar-toggler border-0 d-lg-none" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="toggler-bar"></span>
            <span class="toggler-bar"></span>
            <span class="toggler-bar toggler-bar--short"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_projects.php">Projects</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_skills_experience.php">Skills & Experience</a></li>
                <li class="nav-item"><a class="nav-link" href="contact_form.php">Contact Form</a></li>
                <li class="nav-item"><a class="nav-link" href="view_projects.php">View Portfolio</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="section-label">Admin Panel</div>
    <div class="page-title">Dashboard</div>
</div>

<div class="page-body">

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-number"><?= $projects ?></div>
            <div class="stat-label">Projects</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $skills ?></div>
            <div class="stat-label">Skills</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $experiences ?></div>
            <div class="stat-label">Experiences</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $contacts ?></div>
            <div class="stat-label">Messages</div>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="table-header">
            <span class="table-title">Recent Projects</span>
            <a href="manage_projects.php" class="btn-action">Manage All</a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Tech Stack</th>
                    <th>Status</th>
                    <th>Added</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $rows = $conn->query("SELECT ProjectTitle, TechStack, ProjectStatus, CreatedTime FROM ProjectTBL ORDER BY CreatedTime DESC LIMIT 8");
            if ($rows && $rows->num_rows > 0):
                while ($r = $rows->fetch_assoc()):
                    $badgeClass = match($r['ProjectStatus']) {
                        'Active'      => 'badge-active',
                        'Archived'    => 'badge-archived',
                        'In Progress' => 'badge-progress',
                        default       => 'badge-archived'
                    };
            ?>
            <tr>
                <td><?= htmlspecialchars($r['ProjectTitle']) ?></td>
                <td style="color:var(--muted);font-size:0.82rem;"><?= htmlspecialchars($r['TechStack']) ?></td>
                <td><span class="badge-status <?= $badgeClass ?>"><?= $r['ProjectStatus'] ?></span></td>
                <td style="color:var(--muted);font-size:0.78rem;"><?= date('M d, Y', strtotime($r['CreatedTime'])) ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr class="empty-row"><td colspan="4">No projects yet. <a href="manage_projects.php" style="color:var(--text-light);">Add your first project →</a></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>