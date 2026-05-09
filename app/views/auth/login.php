<?php
/**
 * @var string $title
 * @var string $error
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
        :root {
            --navy-blue: #152b48;
            --navy-hover: #0e1d30;
            --medical-teal: #00897b; 
        }
        body {
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 480px; 
            padding: 3rem 2.5rem;
        }
        .text-navy {
            color: var(--navy-blue) !important;
            font-weight: 700;
            font-size: 1.6rem;
            white-space: nowrap; 
        }
        .btn-navy {
            background-color: var(--navy-blue);
            color: #ffffff;
            font-weight: 600;
            padding: 0.6rem;
            transition: all 0.3s ease;
        }
        .btn-navy:hover {
            background-color: var(--navy-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .form-control {
            padding: 0.6rem 1rem;
        }
        .form-control:focus {
            border-color: var(--navy-blue);
            box-shadow: 0 0 0 0.25rem rgba(21, 43, 72, 0.15);
        }
        .form-label {
            font-weight: 500;
            color: #4a5568;
        }
        .icon-medical {
            color: var(--medical-teal);
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-2 text-navy">
            <i class="bi bi-capsule icon-medical"></i>Pharmacy Management System
        </h3>
        <p class="text-center text-muted mb-4">Please sign in to your account</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= $error ?></div>
            </div>
        <?php endif; ?>
        
        <form action="/login" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required autofocus placeholder="Enter your username">
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                    <label class="form-check-label text-muted" style="font-size: 0.9rem;" for="rememberMe">
                        Remember me
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-navy w-100 mb-3">Sign In</button>
            
            <p class="text-center text-muted mt-3" style="font-size: 0.85rem;">
                Forgot password? Contact your Manager.
            </p>
        </form>
    </div>
</body>
</html>