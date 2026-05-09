<?php
/** * @var string $title 
 * @var string $fullName 
 * @var array $roles 
 * @var array $editUser
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Tái sử dụng CSS để đồng bộ */
        :root { --sidebar-bg: #2c3e50; --sidebar-hover: #34495e; --main-bg: #f4f7f6; --text-light: #ecf0f1; --navy-blue: #152b48; }
        body { background-color: var(--main-bg); font-family: 'Segoe UI', Tahoma, sans-serif; overflow-x: hidden; }
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .dashboard-container { padding: 25px; flex-grow: 1; }
        .form-card { border-radius: 10px; border: none; max-width: 800px; }
        .form-label { font-weight: 600; color: #495057; }
        .btn-navy { background-color: var(--navy-blue); color: white; }
        .btn-navy:hover { background-color: #0e1d30; color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="bi bi-capsule me-2"></i> PMS</div>
        <div class="nav-category">Main</div>
        <a href="/"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <div class="nav-category">System</div>
        <a href="/users" class="active"><i class="bi bi-people"></i> Manage Users</a>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 me-2"></i><strong><?= htmlspecialchars($fullName) ?> (Manager)</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </div>

        <div class="dashboard-container">
            <div class="mb-4">
                <a href="/users" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to User List
                </a>
            </div>

            <div class="card form-card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 text-navy fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Staff Account</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    
                    <form action="/users/edit" method="POST">
                        <input type="hidden" name="user_id" value="<?= $editUser['user_id'] ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required value="<?= htmlspecialchars($editUser['full_name']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control bg-light" id="username" value="<?= htmlspecialchars($editUser['username']) ?>" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-muted fw-normal" style="font-size: 0.85rem;">(Optional)</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role_id" class="form-label">System Role</label>
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['role_id'] ?>" <?= ($role['role_id'] == $editUser['role_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($role['role_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label">Account Status</label>
                            <select class="form-select" id="status" name="status" style="max-width: 200px;">
                                <option value="Active" <?= ($editUser['status'] == 'Active') ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= ($editUser['status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <hr class="text-muted">
                        <div class="text-end">
                            <a href="/users" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-navy px-4"><i class="bi bi-save me-1"></i> Update Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>