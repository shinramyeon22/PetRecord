<?php
include('databases.php');
$id = $_GET['id'] ?? null;
if (!$id) header('Location: index.php');

$stmt = $pdo->prepare('SELECT * FROM pets WHERE id = :id');
$stmt->execute([':id' => $id]);
$pet = $stmt->fetch();

if (!$pet) header('Location: index.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $pet_name    = trim($_POST['pet_name'] ?? '');
    $species     = trim($_POST['species'] ?? '');
    $breed       = trim($_POST['breed'] ?? '');
    $age         = trim($_POST['age'] ?? '');
    $gender      = trim($_POST['gender'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($pet_name)) $errors[] = "Pet name is required.";
    if (empty($species)) $errors[] = "Species is required.";
    if (empty($breed)) $errors[] = "Breed is required.";
    if (empty($age) || !is_numeric($age)) $errors[] = "Valid age is required.";
    if (empty($gender)) $errors[] = "Gender is required.";
    if (empty($description)) $errors[] = "Description is required.";

    if (empty($errors)) {
        $image_path = $pet['image_path'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $file_name;

            $allowed = ['jpg','jpeg','png','gif'];
            if (in_array($file_ext, $allowed) && move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $file_name;
            }
        }

        $sql = "UPDATE pets SET pet_name=:pet_name, species=:species, breed=:breed, age=:age, 
                gender=:gender, description=:description, image_path=:image_path WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pet_name' => $pet_name,
            ':species' => $species,
            ':breed' => $breed,
            ':age' => (int)$age,
            ':gender' => $gender,
            ':description' => $description,
            ':image_path' => $image_path,
            ':id' => $id
        ]);

        header('Location: view.php?id=' . $id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?= htmlspecialchars($pet['pet_name']) ?> - PetRecord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h4>Edit Pet Record</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label>Pet Name <span class="text-danger">*</span></label>
                                <input type="text" name="pet_name" class="form-control" value="<?= htmlspecialchars($pet['pet_name']) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Species <span class="text-danger">*</span></label>
                                    <select name="species" class="form-select" required>
                                        <option value="Dog" <?= $pet['species']=='Dog'?'selected':'' ?>>Dog</option>
                                        <option value="Cat" <?= $pet['species']=='Cat'?'selected':'' ?>>Cat</option>
                                        <option value="Bird" <?= $pet['species']=='Bird'?'selected':'' ?>>Bird</option>
                                        <option value="Rabbit" <?= $pet['species']=='Rabbit'?'selected':'' ?>>Rabbit</option>
                                        <option value="Fish" <?= $pet['species']=='Fish'?'selected':'' ?>>Fish</option>
                                        <option value="Other" <?= $pet['species']=='Other'?'selected':'' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Breed <span class="text-danger">*</span></label>
                                    <input type="text" name="breed" class="form-control" value="<?= htmlspecialchars($pet['breed']) ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Age <span class="text-danger">*</span></label>
                                    <input type="number" name="age" class="form-control" value="<?= $pet['age'] ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="Male" <?= $pet['gender']=='Male'?'selected':'' ?>>Male</option>
                                        <option value="Female" <?= $pet['gender']=='Female'?'selected':'' ?>>Female</option>
                                        <option value="Unknown" <?= $pet['gender']=='Unknown'?'selected':'' ?>>Unknown</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($pet['description']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Current Photo</label><br>
                                <?php if ($pet['image_path']): ?>
                                    <img src="uploads/<?= htmlspecialchars($pet['image_path']) ?>" class="img-fluid rounded mb-2" style="max-height:200px;">
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small>Leave blank to keep current photo</small>
                            </div>

                            <button type="submit" name="submit" class="btn btn-warning w-100">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>

                            <!-- Back Button -->
                            <div class="text-center mt-3">
                                <a href="view.php?id=<?= $pet['id'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel & Back to Pet Details
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>