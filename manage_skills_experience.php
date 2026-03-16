<?php
require 'db.php';

$msg = '';

if (isset($_POST['action']) && $_POST['action'] === 'add_skill') {
    $pid      = (int)$_POST['project_id'];
    $name     = trim($_POST['skillname']);
    $category = trim($_POST['category']);
    $prof     = $_POST['proficiency'];
    $conn->query("INSERT INTO SkillsTBL (ProjectID, SkillName, Category, Proficiency) VALUES ($pid, '".$conn->real_escape_string($name)."', '".$conn->real_escape_string($category)."', '$prof')");
    $msg = '<div class="alert alert-success">Skill added successfully.</div>';
}

if (isset($_GET['delete_skill'])) {
    $id = (int)$_GET['delete_skill'];
    $conn->query("DELETE FROM SkillsTBL WHERE SkillID = $id");
    $msg = '<div class="alert alert-success">Skill deleted.</div>';
}

if (isset($_POST['action']) && $_POST['action'] === 'edit_skill') {
    $id       = (int)$_POST['edit_id'];
    $pid      = (int)$_POST['project_id'];
    $name     = trim($_POST['skillname']);
    $category = trim($_POST['category']);
    $prof     = $_POST['proficiency'];
    $conn->query("UPDATE SkillsTBL SET ProjectID=$pid, SkillName='".$conn->real_escape_string($name)."', Category='".$conn->real_escape_string($category)."', Proficiency='$prof' WHERE SkillID=$id");
    $msg = '<div class="alert alert-success">Skill updated.</div>';
}

if (isset($_POST['action']) && $_POST['action'] === 'add_exp') {
    $pid     = (int)$_POST['project_id'];
    $job     = trim($_POST['jobtitle']);
    $company = trim($_POST['company']);
    $start   = $_POST['startdate'];
    $end     = !empty($_POST['enddate']) ? "'".$_POST['enddate']."'" : "NULL";
    $conn->query("INSERT INTO ExperiencesTBL (ProjectID, JobTitle, Company, StartDate, EndDate) VALUES ($pid, '".$conn->real_escape_string($job)."', '".$conn->real_escape_string($company)."', '$start', $end)");
    $msg = '<div class="alert alert-success">Experience added successfully.</div>';
}

if (isset($_GET['delete_exp'])) {
    $id = (int)$_GET['delete_exp'];
    $conn->query("DELETE FROM ExperiencesTBL WHERE ExpID = $id");
    $msg = '<div class="alert alert-success">Experience deleted.</div>';
}

if (isset($_POST['action']) && $_POST['action'] === 'edit_exp') {
    $id      = (int)$_POST['edit_id'];
    $pid     = (int)$_POST['project_id'];
    $job     = trim($_POST['jobtitle']);
    $company = trim($_POST['company']);
    $start   = $_POST['startdate'];
    $end     = !empty($_POST['enddate']) ? "'".$_POST['enddate']."'" : "NULL";
    $conn->query("UPDATE ExperiencesTBL SET ProjectID=$pid, JobTitle='".$conn->real_escape_string($job)."', Company='".$conn->real_escape_string($company)."', StartDate='$start', EndDate=$end WHERE ExpID=$id");
    $msg = '<div class="alert alert-success">Experience updated.</div>';
}

$projects    = $conn->query("SELECT ProjectID, ProjectTitle FROM ProjectTBL ORDER BY ProjectTitle");
$skills      = $conn->query("SELECT s.*, p.ProjectTitle FROM SkillsTBL s JOIN ProjectTBL p ON s.ProjectID = p.ProjectID ORDER BY p.ProjectTitle, s.Category, s.SkillName");
$experiences = $conn->query("SELECT e.*, p.ProjectTitle FROM ExperiencesTBL e JOIN ProjectTBL p ON e.ProjectID = p.ProjectID ORDER BY p.ProjectTitle, e.StartDate DESC");

$projectList = [];
$conn->query("SELECT ProjectID, ProjectTitle FROM ProjectTBL ORDER BY ProjectTitle")->data_seek(0);
$pRows = $conn->query("SELECT ProjectID, ProjectTitle FROM ProjectTBL ORDER BY ProjectTitle");
while ($p = $pRows->fetch_assoc()) {
    $projectList[] = $p;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills & Experience | Web-Based PMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-0">
        <a class="navbar-brand" href="index.php">Manage Skills & Experience</a>
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
                <li class="nav-item"><a class="nav-link active" href="manage_skills_experience.php">Skills & Experience</a></li>
                <li class="nav-item"><a class="nav-link" href="contact_form.php">Contact Form</a></li>
                <li class="nav-item"><a class="nav-link" href="view_projects.php">View Portfolio</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="section-label">Management</div>
    <div class="page-title">Skills & Experience</div>
</div>

<div class="page-body">
    <?= $msg ?>

    <?php if (empty($projectList)): ?>
    <div class="alert alert-info">
        No projects found. <a href="manage_projects.php" style="color:#fff;">Add a project first</a> before managing skills and experiences.
    </div>
    <?php else: ?>

    <div class="tab-bar">
        <button class="tab-btn active" onclick="switchTab('skills', this)">Skills</button>
        <button class="tab-btn" onclick="switchTab('experience', this)">Experience</button>
    </div>

    <div id="tab-skills">
        <div class="table-wrapper">
            <div class="table-header">
                <span class="table-title">All Skills</span>
                <button class="btn-action btn-primary-action" onclick="openModal('addSkillModal')">+ Add Skill</button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Skill Name</th>
                        <th>Category</th>
                        <th>Proficiency</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($skills && $skills->num_rows > 0):
                    $i = 1;
                    while ($s = $skills->fetch_assoc()):
                        $badgeClass = 'badge-' . strtolower($s['Proficiency']);
                ?>
                <tr>
                    <td style="color:var(--muted);font-size:0.78rem;"><?= $i++ ?></td>
                    <td>
                        <span style="font-size:0.75rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);font-weight:600;">
                            <?= htmlspecialchars($s['ProjectTitle']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($s['SkillName']) ?></td>
                    <td style="color:var(--muted);font-size:0.82rem;"><?= htmlspecialchars($s['Category']) ?></td>
                    <td><span class="badge-status <?= $badgeClass ?>"><?= ucfirst($s['Proficiency']) ?></span></td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-action"
                                onclick="openEditSkill(
                                    <?= $s['SkillID'] ?>,
                                    <?= $s['ProjectID'] ?>,
                                    '<?= addslashes(htmlspecialchars($s['SkillName'])) ?>',
                                    '<?= addslashes(htmlspecialchars($s['Category'])) ?>',
                                    '<?= $s['Proficiency'] ?>'
                                )">Edit</button>
                            <a href="?delete_skill=<?= $s['SkillID'] ?>" class="btn-action btn-danger-action"
                               onclick="return confirm('Delete this skill?')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr class="empty-row"><td colspan="6">No skills yet. Add a skill to one of your projects.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-experience" style="display:none;">
        <div class="table-wrapper">
            <div class="table-header">
                <span class="table-title">Work History</span>
                <button class="btn-action btn-primary-action" onclick="openModal('addExpModal')">+ Add Experience</button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($experiences && $experiences->num_rows > 0):
                    $i = 1;
                    while ($e = $experiences->fetch_assoc()):
                ?>
                <tr>
                    <td style="color:var(--muted);font-size:0.78rem;"><?= $i++ ?></td>
                    <td>
                        <span style="font-size:0.75rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);font-weight:600;">
                            <?= htmlspecialchars($e['ProjectTitle']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($e['JobTitle']) ?></td>
                    <td style="color:var(--muted);"><?= htmlspecialchars($e['Company']) ?></td>
                    <td style="color:var(--muted);font-size:0.82rem;"><?= $e['StartDate'] ? date('M Y', strtotime($e['StartDate'])) : '—' ?></td>
                    <td style="color:var(--muted);font-size:0.82rem;">
                        <?= $e['EndDate'] ? date('M Y', strtotime($e['EndDate'])) : '<span style="color:var(--success);font-size:0.7rem;letter-spacing:0.1em;text-transform:uppercase;">Present</span>' ?>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-action"
                                onclick="openEditExp(
                                    <?= $e['ExpID'] ?>,
                                    <?= $e['ProjectID'] ?>,
                                    '<?= addslashes(htmlspecialchars($e['JobTitle'])) ?>',
                                    '<?= addslashes(htmlspecialchars($e['Company'])) ?>',
                                    '<?= $e['StartDate'] ?>',
                                    '<?= $e['EndDate'] ?>'
                                )">Edit</button>
                            <a href="?delete_exp=<?= $e['ExpID'] ?>" class="btn-action btn-danger-action"
                               onclick="return confirm('Delete this experience?')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr class="empty-row"><td colspan="7">No experiences yet. Add work history to one of your projects.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif;?>
</div>

<?php if (!empty($projectList)): ?>

<div class="modal-overlay" id="addSkillModal">
    <div class="modal-panel">
        <div class="modal-head">
            <span class="modal-head-title">Add Skill to Project</span>
            <button class="modal-close" onclick="closeModal('addSkillModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="add_skill">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control" required>
                        <option value="">— Select a project —</option>
                        <?php foreach ($projectList as $p): ?>
                        <option value="<?= $p['ProjectID'] ?>"><?= htmlspecialchars($p['ProjectTitle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Skill Name</label>
                    <input type="text" name="skillname" class="form-control" placeholder="e.g. PHP" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="e.g. Backend">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Proficiency</label>
                        <select name="proficiency" class="form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate" selected>Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-action" onclick="closeModal('addSkillModal')">Cancel</button>
                    <button type="submit" class="btn-action btn-primary-action">Save Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editSkillModal">
    <div class="modal-panel">
        <div class="modal-head">
            <span class="modal-head-title">Edit Skill</span>
            <button class="modal-close" onclick="closeModal('editSkillModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="edit_skill">
                <input type="hidden" name="edit_id" id="skill_edit_id">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" id="skill_edit_pid" class="form-control" required>
                        <?php foreach ($projectList as $p): ?>
                        <option value="<?= $p['ProjectID'] ?>"><?= htmlspecialchars($p['ProjectTitle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Skill Name</label>
                    <input type="text" name="skillname" id="skill_edit_name" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" id="skill_edit_cat" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Proficiency</label>
                        <select name="proficiency" id="skill_edit_prof" class="form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-action" onclick="closeModal('editSkillModal')">Cancel</button>
                    <button type="submit" class="btn-action btn-primary-action">Update Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="addExpModal">
    <div class="modal-panel">
        <div class="modal-head">
            <span class="modal-head-title">Add Experience to Project</span>
            <button class="modal-close" onclick="closeModal('addExpModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="add_exp">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control" required>
                        <option value="">— Select a project —</option>
                        <?php foreach ($projectList as $p): ?>
                        <option value="<?= $p['ProjectID'] ?>"><?= htmlspecialchars($p['ProjectTitle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="jobtitle" class="form-control" placeholder="e.g. Web Developer" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" class="form-control" placeholder="e.g. Acme Corp" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="startdate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date <span style="color:var(--muted);font-size:0.7em;">(blank = present)</span></label>
                        <input type="date" name="enddate" class="form-control">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-action" onclick="closeModal('addExpModal')">Cancel</button>
                    <button type="submit" class="btn-action btn-primary-action">Save Experience</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editExpModal">
    <div class="modal-panel">
        <div class="modal-head">
            <span class="modal-head-title">Edit Experience</span>
            <button class="modal-close" onclick="closeModal('editExpModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="edit_exp">
                <input type="hidden" name="edit_id" id="exp_edit_id">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" id="exp_edit_pid" class="form-control" required>
                        <?php foreach ($projectList as $p): ?>
                        <option value="<?= $p['ProjectID'] ?>"><?= htmlspecialchars($p['ProjectTitle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="jobtitle" id="exp_edit_job" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" id="exp_edit_company" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="startdate" id="exp_edit_start" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" name="enddate" id="exp_edit_end" class="form-control">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-action" onclick="closeModal('editExpModal')">Cancel</button>
                    <button type="submit" class="btn-action btn-primary-action">Update Experience</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function switchTab(tab, btn) {
    document.getElementById('tab-skills').style.display     = tab === 'skills'     ? 'block' : 'none';
    document.getElementById('tab-experience').style.display = tab === 'experience' ? 'block' : 'none';
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
}

function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

function openEditSkill(id, pid, name, cat, prof) {
    document.getElementById('skill_edit_id').value   = id;
    document.getElementById('skill_edit_pid').value  = pid;
    document.getElementById('skill_edit_name').value = name;
    document.getElementById('skill_edit_cat').value  = cat;
    document.getElementById('skill_edit_prof').value = prof;
    openModal('editSkillModal');
}

function openEditExp(id, pid, job, company, start, end) {
    document.getElementById('exp_edit_id').value      = id;
    document.getElementById('exp_edit_pid').value     = pid;
    document.getElementById('exp_edit_job').value     = job;
    document.getElementById('exp_edit_company').value = company;
    document.getElementById('exp_edit_start').value   = start;
    document.getElementById('exp_edit_end').value     = end || '';
    openModal('editExpModal');
}

document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) overlay.classList.remove('open');
    });
});
</script>
</body>
</html>