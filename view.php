<?php
include('databases.php');
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM pets WHERE id = :id');
$stmt->execute([':id' => $id]);
$pet = $stmt->fetch();

if (!$pet) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pet['pet_name']) ?> - PetRecord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #e4f0f7 100%); }
        .pet-hero-img { 
            height: 380px; 
            object-fit: cover; 
            border-radius: 16px; 
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-paw me-2"></i>PetRecord
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-primary">Pet Details</h4>
                <a href="index.php" class="btn btn-secondary">← Back to Registry</a>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-5">
                        <?php if (!empty($pet['image_path'])): ?>
                            <img src="uploads/<?= htmlspecialchars($pet['image_path']) ?>" 
                                 class="pet-hero-img img-fluid shadow" alt="">
                        <?php else: ?>
                            <div class="pet-hero-img bg-light d-flex align-items-center justify-content-center border">
                                <i class="fas fa-paw fa-6x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-7">
                        <h2 class="fw-bold"><?= htmlspecialchars($pet['pet_name']) ?></h2>
                        <p class="lead text-muted mb-3">
                            <?= htmlspecialchars($pet['species']) ?> • <?= htmlspecialchars($pet['breed']) ?>
                        </p>
                        <p><strong>Age:</strong> <?= $pet['age'] ?> years old</p>
                        <p><strong>Gender:</strong> <?= htmlspecialchars($pet['gender']) ?></p>
                        <hr>
                        <p><strong>Description:</strong></p>
                        <p><?= nl2br(htmlspecialchars($pet['description'])) ?></p>

                        <div class="mt-4 d-flex gap-3">
                            <a href="edit.php?id=<?= $pet['id'] ?>" class="btn btn-warning flex-fill">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="delete.php" method="POST" class="flex-fill">
                                <input type="hidden" name="_method" value="delete">
                                <input type="hidden" name="id" value="<?= $pet['id'] ?>">
                                <button type="submit" class="btn btn-danger w-100" 
                                        onclick="return confirm('Are you sure you want to delete this pet?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>