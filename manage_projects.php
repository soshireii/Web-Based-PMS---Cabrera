<?php
require 'db.php';

$msg = '';

if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $title  = trim($_POST['title']);
    $desc   = trim($_POST['description']);
    $stack  = trim($_POST['techstack']);
    $status = $_POST['status'];

    $profileImg = !empty($_FILES['profile_image']['tmp_name'])
        ? file_get_contents($_FILES['profile_image']['tmp_name']) : null;
    $bgImg = !empty($_FILES['bg_image']['tmp_name'])
        ? file_get_contents($_FILES['bg_image']['tmp_name']) : null;

    $stmt = $conn->prepare("INSERT INTO ProjectTBL (ProjectTitle, ProjectDescription, TechStack, ProjectStatus, ProfileImage, BackgroundImage) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $desc, $stack, $status, $profileImg, $bgImg);
    if ($stmt->execute()) {
        $msg = '<div class="alert alert-success">Project added successfully.</div>';
    } else {
        $msg = '<div class="alert alert-error">Error saving project: ' . htmlspecialchars($conn->error) . '</div>';
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM ProjectTBL WHERE ProjectID = $id");
    $msg = '<div class="alert alert-success">Project deleted.</div>';
}

if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id     = (int)$_POST['edit_id'];
    $title  = trim($_POST['title']);
    $desc   = trim($_POST['description']);
    $stack  = trim($_POST['techstack']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE ProjectTBL SET ProjectTitle=?, ProjectDescription=?, TechStack=?, ProjectStatus=? WHERE ProjectID=?");
    $stmt->bind_param("ssssi", $title, $desc, $stack, $status, $id);
    $stmt->execute();

    if (!empty($_FILES['profile_image']['tmp_name'])) {
        $data = file_get_contents($_FILES['profile_image']['tmp_name']);
        $stmt2 = $conn->prepare("UPDATE ProjectTBL SET ProfileImage=? WHERE ProjectID=?");
        $stmt2->bind_param("si", $data, $id);
        $stmt2->execute();
    }
    if (!empty($_FILES['bg_image']['tmp_name'])) {
        $data = file_get_contents($_FILES['bg_image']['tmp_name']);
        $stmt3 = $conn->prepare("UPDATE ProjectTBL SET BackgroundImage=? WHERE ProjectID=?");
        $stmt3->bind_param("si", $data, $id);
        $stmt3->execute();
    }

    $msg = '<div class="alert alert-success">Project updated.</div>';
}

$projects = $conn->query("SELECT * FROM ProjectTBL ORDER BY CreatedTime DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects | Web-Based PMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-0">
        <a class="navbar-brand" href="index.php">Manage Projects</a>
        <button class="navbar-toggler border-0 d-lg-none" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="toggler-bar"></span>
            <span class="toggler-bar"></span>
            <span class="toggler-bar toggler-bar--short"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="manage_projects.php">Projects</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_skills_experience.php">Skills & Experience</a></li>
                <li class="nav-item"><a class="nav-link" href="contact_form.php">Contact Form</a></li>
                <li class="nav-item"><a class="nav-link" href="view_projects.php">View Portfolio</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="section-label">Management</div>
    <div class="page-title">Projects</div>
</div>

<div class="page-body">
    <?= $msg ?>

    <div class="table-wrapper">
        <div class="table-header">
            <span class="table-title">All Projects</span>
            <button class="btn-action btn-primary-action" onclick="openModal('addModal')">+ Add Project</button>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Images</th>
                    <th>Title</th>
                    <th>Tech Stack</th>
                    <th>Status</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($projects && $projects->num_rows > 0):
                while ($p = $projects->fetch_assoc()):
                    $badgeClass = match($p['ProjectStatus']) {
                        'Active'      => 'badge-active',
                        'Archived'    => 'badge-archived',
                        'In Progress' => 'badge-progress',
                        default       => 'badge-archived'
                    };
            ?>
            <tr>
                <td>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <?php if (!empty($p['ProfileImage'])): ?>
                            <img src="get_image.php?table=ProjectTBL&col=ProfileImage&id=<?= $p['ProjectID'] ?>"
                                 class="img-preview" alt="Profile">
                        <?php else: ?>
                            <div class="img-preview" style="display:flex;align-items:center;justify-content:center;">
                                <span style="font-size:0.55rem;color:var(--muted);letter-spacing:0.05em;">NONE</span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($p['BackgroundImage'])): ?>
                            <img src="get_image.php?table=ProjectTBL&col=BackgroundImage&id=<?= $p['ProjectID'] ?>"
                                 class="img-preview" alt="Background">
                        <?php else: ?>
                            <div class="img-preview" style="display:flex;align-items:center;justify-content:center;">
                                <span style="font-size:0.55rem;color:var(--muted);letter-spacing:0.05em;">NONE</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </td>
                <td><?= htmlspecialchars($p['ProjectTitle']) ?></td>
                <td style="color:var(--muted);font-size:0.82rem;"><?= htmlspecialchars($p['TechStack']) ?></td>
                <td><span class="badge-status <?= $badgeClass ?>"><?= $p['ProjectStatus'] ?></span></td>
                <td style="color:var(--muted);font-size:0.78rem;"><?= date('M d, Y', strtotime($p['CreatedTime'])) ?></td>
                <td>
                    <div class="actions-cell">
                        <button class="btn-action"
                            onclick="openEdit(
                                <?= $p['ProjectID'] ?>,
                                '<?= addslashes(htmlspecialchars($p['ProjectTitle'])) ?>',
                                '<?= addslashes(htmlspecialchars($p['ProjectDescription'])) ?>',
                                '<?= addslashes(htmlspecialchars($p['TechStack'])) ?>',
                                '<?= $p['ProjectStatus'] ?>'
                            )">Edit</button>
                        <a href="?delete=<?= $p['ProjectID'] ?>" class="btn-action btn-danger-action"
                           onclick="return confirm('Delete this project and all its skills/experiences?')">Delete</a>
                    </div>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr class="empty-row"><td colspan="6">No projects yet. Add your first project.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="addModal">
    <div class="modal-panel">
        <div class="modal-head">
            <span class="modal-head-title">Add New Project</span>
            <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">Project Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. E-commerce App" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Brief project description..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tech Stack</label>
                        <input type="text" name="techstack" class="form-control" placeholder="PHP, MySQL, JS">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" class="form-control" accept="image/*"
                               onchange="previewImg(this, 'prev_profile_add')">
                        <img id="prev_profile_add" class="img-preview-lg" style="display:none;" alt="">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Background Image</label>
                        <input type="file" name="bg_image" class="form-control" accept="image/*"
                               onchange="previewImg(this, 'prev_bg_add')">
                        <img id="prev_bg_add" class="img-preview-lg" style="display:none;" alt="">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-action" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn-action btn-primary-action">Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editModal">
    <div class="modal-panel">
        <div class="modal-head">
            <span class="modal-head-title">Edit Project</span>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="form-group">
                    <label class="form-label">Project Title</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit_desc" class="form-control"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tech Stack</label>
                        <input type="text" name="techstack" id="edit_stack" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Replace Profile Image <span style="color:var(--muted);font-size:0.7em;">(optional)</span></label>
                        <input type="file" name="profile_image" class="form-control" accept="image/*"
                               onchange="previewImg(this, 'prev_profile_edit')">
                        <img id="prev_profile_edit" class="img-preview-lg" style="display:none;" alt="">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Replace Background Image <span style="color:var(--muted);font-size:0.7em;">(optional)</span></label>
                        <input type="file" name="bg_image" class="form-control" accept="image/*"
                               onchange="previewImg(this, 'prev_bg_edit')">
                        <img id="prev_bg_edit" class="img-preview-lg" style="display:none;" alt="">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-action" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn-action btn-primary-action">Update Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

function openEdit(id, title, desc, stack, status) {
    document.getElementById('edit_id').value     = id;
    document.getElementById('edit_title').value  = title;
    document.getElementById('edit_desc').value   = desc;
    document.getElementById('edit_stack').value  = stack;
    document.getElementById('edit_status').value = status;
    document.getElementById('prev_profile_edit').style.display = 'none';
    document.getElementById('prev_bg_edit').style.display      = 'none';
    openModal('editModal');
}

function previewImg(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) overlay.classList.remove('open');
    });
});
</script>
</body>
</html>