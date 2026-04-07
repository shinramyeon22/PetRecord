<?php
include('databases.php');
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
        $image_path = '';
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

        $sql = "INSERT INTO pets (pet_name, species, breed, age, gender, description, image_path) 
                VALUES (:pet_name, :species, :breed, :age, :gender, :description, :image_path)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pet_name' => $pet_name,
            ':species' => $species,
            ':breed' => $breed,
            ':age' => (int)$age,
            ':gender' => $gender,
            ':description' => $description,
            ':image_path' => $image_path
        ]);

        header('Location: index.php?success=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Pet - PetRecord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Pet Record</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $err): echo "• $err<br>"; endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pet Name <span class="text-danger">*</span></label>
                                    <input type="text" name="pet_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Species <span class="text-danger">*</span></label>
                                    <select name="species" class="form-select" required>
                                        <option value="">Select Species</option>
                                        <option value="Dog">Dog</option>
                                        <option value="Cat">Cat</option>
                                        <option value="Bird">Bird</option>
                                        <option value="Rabbit">Rabbit</option>
                                        <option value="Fish">Fish</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Breed <span class="text-danger">*</span></label>
                                    <input type="text" name="breed" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Age (years) <span class="text-danger">*</span></label>
                                    <input type="number" name="age" min="0" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Unknown">Unknown</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Pet Photo (optional)</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg">Save Pet Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>