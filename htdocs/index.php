<?php
include('databases.php');

// Success message after adding pet
$success = $_GET['success'] ?? null;

$stmt = $pdo->prepare('SELECT * FROM pets ORDER BY id DESC');
$stmt->execute();
$pets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetRecord - Pet Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4f0f7 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background: linear-gradient(90deg, #1e3a8a, #3b82f6) !important;
        }
        .pet-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .pet-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .pet-img {
            height: 240px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <i class="fas fa-paw me-2"></i>PetRecord
            </a>
            <a href="create.php" class="btn btn-light fw-semibold px-4">
                <i class="fas fa-plus"></i> Add New Pet
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($success == '1'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> New pet record has been added successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">Pet Registry</h1>
            <span class="badge bg-primary fs-6"><?= count($pets) ?> Pets Registered</span>
        </div>

        <?php if (empty($pets)): ?>
            <div class="text-center py-5">
                <i class="fas fa-paw fa-5x text-muted mb-3"></i>
                <h4 class="text-muted">No pets registered yet.</h4>
                <a href="create.php" class="btn btn-primary btn-lg mt-3">Add Your First Pet</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($pets as $pet): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card pet-card h-100">
                        <?php if (!empty($pet['image_path'])): ?>
                            <img src="uploads/<?= htmlspecialchars($pet['image_path']) ?>" 
                                 class="pet-img card-img-top" 
                                 alt="<?= htmlspecialchars($pet['pet_name']) ?>">
                        <?php else: ?>
                            <div class="pet-img bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-paw fa-5x text-secondary opacity-25"></i>
                            </div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($pet['pet_name']) ?></h5>
                            <p class="text-muted mb-2">
                                <?= htmlspecialchars($pet['species']) ?> • <?= htmlspecialchars($pet['breed']) ?>
                            </p>
                            <p class="small text-secondary mb-3">
                                <?= $pet['age'] ?> years • <?= htmlspecialchars($pet['gender']) ?>
                            </p>
                            <div class="mt-auto">
                                <a href="view.php?id=<?= $pet['id'] ?>" class="btn btn-outline-primary w-100">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>