<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Projects | Web-Based PMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-0">
        <a class="navbar-brand" href="view_projects.php">View Projects</a>
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

<?php
$projects = $conn->query("SELECT * FROM ProjectTBL WHERE ProjectStatus != 'Archived' ORDER BY CreatedTime DESC");
?>

<div class="page-header">
    <div class="section-label">Portfolio</div>
    <div class="page-title">Projects</div>
</div>

<div class="page-body">

<?php if ($projects && $projects->num_rows > 0):
    while ($p = $projects->fetch_assoc()):
        $pid = $p['ProjectID'];

        $skills      = $conn->query("SELECT * FROM SkillsTBL WHERE ProjectID = $pid ORDER BY Category, SkillName");
        $experiences = $conn->query("SELECT * FROM ExperiencesTBL WHERE ProjectID = $pid ORDER BY StartDate DESC");

        $badgeClass = match($p['ProjectStatus']) {
            'Active'      => 'badge-active',
            'Archived'    => 'badge-archived',
            'In Progress' => 'badge-progress',
            default       => 'badge-archived'
        };
?>

<div class="public-project-block">

    <div class="public-project-hero">
        <?php if (!empty($p['BackgroundImage'])): ?>
        <img src="get_image.php?table=ProjectTBL&col=BackgroundImage&id=<?= $pid ?>"
             class="public-project-bg" alt="<?= htmlspecialchars($p['ProjectTitle']) ?>">
        <?php endif; ?>

        <div class="public-project-hero-content">
            <?php if (!empty($p['ProfileImage'])): ?>
            <img src="get_image.php?table=ProjectTBL&col=ProfileImage&id=<?= $pid ?>"
                 class="public-project-profile" alt="">
            <?php endif; ?>
            <div style="flex:1;">
                <div style="margin-bottom:0.5rem;">
                    <span class="badge-status <?= $badgeClass ?>"><?= $p['ProjectStatus'] ?></span>
                </div>
                <h2 class="public-project-title"><?= htmlspecialchars($p['ProjectTitle']) ?></h2>
                <?php if ($p['TechStack']): ?>
                <div class="public-project-stack"><?= htmlspecialchars($p['TechStack']) ?></div>
                <?php endif; ?>
            </div>

            <div style="margin-left:auto;align-self:flex-end;padding-bottom:0.2rem;">
                <a href="project_detail.php?id=<?= $pid ?>" class="btn-view-project">View Project →</a>
            </div>
        </div>
    </div>

    <?php if ($p['ProjectDescription']): ?>
    <div class="public-project-desc" style="position:relative;">
        <div class="project-desc-preview">
            <?= nl2br(htmlspecialchars(mb_substr($p['ProjectDescription'], 0, 220))) ?>
            <?= mb_strlen($p['ProjectDescription']) > 220 ? '<span style="color:var(--muted);">…</span>' : '' ?>
        </div>
    </div>
    <?php endif; ?>

    <?php
    $hasSkills = $skills && $skills->num_rows > 0;
    $hasExp    = $experiences && $experiences->num_rows > 0;
    if ($hasSkills || $hasExp):
    ?>
    <div class="public-project-meta <?= ($hasSkills && $hasExp) ? 'two-col' : 'one-col' ?>">

        <?php if ($hasSkills):
            $grouped = [];
            while ($s = $skills->fetch_assoc()) {
                $cat = $s['Category'] ?: 'General';
                $grouped[$cat][] = $s;
            }
        ?>
        <div class="public-meta-section">
            <div class="public-meta-label">Skills Used</div>
            <?php foreach ($grouped as $cat => $items): ?>
            <div style="margin-bottom:1rem;">
                <div style="font-size:0.65rem;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:0.5rem;"><?= htmlspecialchars($cat) ?></div>
                <div style="display:flex;flex-wrap:wrap;gap:0.4rem;">
                    <?php foreach ($items as $item):
                        $bc = 'badge-' . strtolower($item['Proficiency']);
                    ?>
                    <span class="badge-status <?= $bc ?>"><?= htmlspecialchars($item['SkillName']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($hasExp): ?>
        <div class="public-meta-section">
            <div class="public-meta-label">Related Experience</div>
            <div class="timeline">
                <?php while ($e = $experiences->fetch_assoc()): ?>
                <div class="timeline-item">
                    <div class="timeline-job"><?= htmlspecialchars($e['JobTitle']) ?></div>
                    <div class="timeline-company"><?= htmlspecialchars($e['Company']) ?></div>
                    <div class="timeline-dates">
                        <?= $e['StartDate'] ? date('M Y', strtotime($e['StartDate'])) : '—' ?>
                        &mdash;
                        <?= $e['EndDate'] ? date('M Y', strtotime($e['EndDate'])) : '<span style="color:var(--success);">Present</span>' ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

</div>

<?php endwhile;
else: ?>
<div class="alert alert-info">No projects to display yet.</div>
<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>