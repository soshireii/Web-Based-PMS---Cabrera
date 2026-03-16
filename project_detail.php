<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: view_projects.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM ProjectTBL WHERE ProjectID = ? AND ProjectStatus != 'Archived'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$p = $result->fetch_assoc();

if (!$p) {
    header("Location: view_projects.php");
    exit;
}

$pid = $p['ProjectID'];

$skills      = $conn->query("SELECT * FROM SkillsTBL WHERE ProjectID = $pid ORDER BY Category, SkillName");
$experiences = $conn->query("SELECT * FROM ExperiencesTBL WHERE ProjectID = $pid ORDER BY StartDate DESC");

$badgeClass = match($p['ProjectStatus']) {
    'Active'      => 'badge-active',
    'Archived'    => 'badge-archived',
    'In Progress' => 'badge-progress',
    default       => 'badge-archived'
};

$hasSkills = $skills && $skills->num_rows > 0;
$hasExp    = $experiences && $experiences->num_rows > 0;

$grouped = [];
if ($hasSkills) {
    while ($s = $skills->fetch_assoc()) {
        $cat = $s['Category'] ?: 'General';
        $grouped[$cat][] = $s;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($p['ProjectTitle']) ?> | Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-hero {
            position: relative;
            min-height: 340px;
            display: flex;
            align-items: flex-end;
            overflow: hidden;
            border-radius: 0;
        }
        .detail-hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.35;
        }
        .detail-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10,10,10,0.95) 0%, rgba(10,10,10,0.3) 100%);
        }
        .detail-hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-end;
            gap: 2rem;
            padding: 2.5rem 3rem;
            width: 100%;
        }
        .detail-hero-profile {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.15);
            flex-shrink: 0;
        }
        .detail-hero-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(2rem, 5vw, 3.2rem);
            letter-spacing: 0.05em;
            line-height: 1;
            color: #fff;
            margin: 0.4rem 0 0.5rem;
        }
        .detail-hero-stack {
            font-size: 0.78rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            text-decoration: none;
            padding: 1.2rem 3rem 0;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--text-light); }

        .detail-body {
            max-width: 960px;
            margin: 0 auto;
            padding: 2.5rem 2rem 4rem;
        }
        .detail-section {
            margin-bottom: 2.8rem;
        }
        .detail-section-label {
            font-size: 0.62rem;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 1rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid var(--border);
        }
        .detail-description {
            font-size: 0.95rem;
            line-height: 1.85;
            color: var(--text-light);
            white-space: pre-wrap;
        }

        .detail-skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.2rem;
        }
        .detail-skill-group {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem 1.1rem;
        }
        .detail-skill-group-name {
            font-size: 0.6rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.7rem;
        }
        .detail-skill-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .detail-timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .detail-timeline-item {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 0 2rem;
            padding: 1.4rem 0;
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        .detail-timeline-item:last-child { border-bottom: none; }
        .detail-timeline-dates {
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            color: var(--muted);
            padding-top: 0.15rem;
        }
        .detail-timeline-job {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 0.2rem;
        }
        .detail-timeline-company {
            font-size: 0.8rem;
            color: var(--muted);
        }

        .detail-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        .detail-meta-item {}
        .detail-meta-item .label {
            font-size: 0.6rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.3rem;
        }
        .detail-meta-item .value {
            font-size: 0.88rem;
            color: var(--text-light);
        }

        @media (max-width: 640px) {
            .detail-hero-content { padding: 1.5rem; flex-direction: column; align-items: flex-start; gap: 1rem; }
            .detail-timeline-item { grid-template-columns: 1fr; gap: 0.3rem; }
            .detail-timeline-dates { padding-bottom: 0.2rem; }
            .back-link { padding: 1rem 1.5rem 0; }
            .detail-body { padding: 1.5rem 1.2rem 3rem; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-0">
        <a class="navbar-brand" href="view_projects.php">Portfolio</a>
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
                <li class="nav-item"><a class="nav-link" href="contact_form.php">Contact Form</a></li>
                <li class="nav-item"><a class="nav-link active" href="view_projects.php">View Portfolio</a></li>
            </ul>
        </div>
    </div>
</nav>

<a href="view_projects.php" class="back-link">← Back to Portfolio</a>

<div class="detail-hero">
    <?php if (!empty($p['BackgroundImage'])): ?>
    <img src="get_image.php?table=ProjectTBL&col=BackgroundImage&id=<?= $pid ?>"
         class="detail-hero-bg" alt="">
    <?php endif; ?>
    <div class="detail-hero-overlay"></div>

    <div class="detail-hero-content">
        <?php if (!empty($p['ProfileImage'])): ?>
        <img src="get_image.php?table=ProjectTBL&col=ProfileImage&id=<?= $pid ?>"
             class="detail-hero-profile" alt="">
        <?php endif; ?>
        <div>
            <div style="margin-bottom:0.5rem;">
                <span class="badge-status <?= $badgeClass ?>"><?= $p['ProjectStatus'] ?></span>
            </div>
            <h1 class="detail-hero-title"><?= htmlspecialchars($p['ProjectTitle']) ?></h1>
            <?php if ($p['TechStack']): ?>
            <div class="detail-hero-stack"><?= htmlspecialchars($p['TechStack']) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="detail-body">

    <div class="detail-meta-row">
        <div class="detail-meta-item">
            <div class="label">Status</div>
            <div class="value"><span class="badge-status <?= $badgeClass ?>"><?= $p['ProjectStatus'] ?></span></div>
        </div>
        <?php if ($p['TechStack']): ?>
        <div class="detail-meta-item">
            <div class="label">Tech Stack</div>
            <div class="value"><?= htmlspecialchars($p['TechStack']) ?></div>
        </div>
        <?php endif; ?>
        <div class="detail-meta-item">
            <div class="label">Added</div>
            <div class="value"><?= date('F j, Y', strtotime($p['CreatedTime'])) ?></div>
        </div>
    </div>

    <?php if ($p['ProjectDescription']): ?>
    <div class="detail-section">
        <div class="detail-section-label">About This Project</div>
        <div class="detail-description"><?= htmlspecialchars($p['ProjectDescription']) ?></div>
    </div>
    <?php endif; ?>

    <?php if ($hasSkills): ?>
    <div class="detail-section">
        <div class="detail-section-label">Skills Used</div>
        <div class="detail-skills-grid">
            <?php foreach ($grouped as $cat => $items): ?>
            <div class="detail-skill-group">
                <div class="detail-skill-group-name"><?= htmlspecialchars($cat) ?></div>
                <div class="detail-skill-tags">
                    <?php foreach ($items as $item):
                        $bc = 'badge-' . strtolower($item['Proficiency']);
                    ?>
                    <span class="badge-status <?= $bc ?>" title="<?= ucfirst($item['Proficiency']) ?>">
                        <?= htmlspecialchars($item['SkillName']) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($hasExp): ?>
    <div class="detail-section">
        <div class="detail-section-label">Related Experience</div>
        <div class="detail-timeline">
            <?php while ($e = $experiences->fetch_assoc()): ?>
            <div class="detail-timeline-item">
                <div class="detail-timeline-dates">
                    <?= $e['StartDate'] ? date('M Y', strtotime($e['StartDate'])) : '—' ?>
                    &mdash;
                    <?= $e['EndDate'] ? date('M Y', strtotime($e['EndDate'])) : '<span style="color:var(--success);">Present</span>' ?>
                </div>
                <div>
                    <div class="detail-timeline-job"><?= htmlspecialchars($e['JobTitle']) ?></div>
                    <div class="detail-timeline-company"><?= htmlspecialchars($e['Company']) ?></div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <div style="margin-top:3rem;padding-top:1.5rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <a href="view_projects.php" class="btn-action">← Back to Portfolio</a>
        <a href="contact_form.php" class="btn-submit" style="font-size:0.78rem;padding:0.55rem 1.4rem;">Get In Touch</a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>