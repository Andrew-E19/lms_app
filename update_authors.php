<?php

require_once('classes/database.php');
$con = new database();
session_start();

if (empty($id = $_POST['id'])) {
    header("Location: index.php");
} else {
  $id = $_POST['id'];
  $data = $con->viewAuthorsID($id);
}

$sweetAlertConfig = "";

    if (isset($_POST['updateAuthor'])) {
        $authorID = $_POST['id'];
        $authorFirstName = $_POST['authorFirstName'];
        $authorLastName = $_POST['authorLastName'];
        $authorBirthYear = $_POST['authorBirthYear'];
        $authorNationality = $_POST['authorNationality'];

        $result = $con->updateAuthor($authorID, $authorFirstName, $authorLastName, $authorBirthYear, $authorNationality);

        if ($result) {
          $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'success',
                title: 'Update Success',
                text: 'Author info upadated successfully.',
                confirmButtonText: 'Continue'
              }).then(() => {
                window.location.href = 'admin_homepage.php';
              });
            </script>";
        } else {
          $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update author info.',
              });
            </script>";
        }
    }

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> <!-- Correct Bootstrap Icons CSS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Authors</title>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Library Management System (Admin)</a>
      <a class="btn btn-outline-light ms-auto active" href="add_authors.php">Add Authors</a>
      <a class="btn btn-outline-light ms-2" href="add_genres.php">Add Genres</a>
      <a class="btn btn-outline-light ms-2" href="add_books.php">Add Books</a>
      <div class="dropdown ms-2">
        <button class="btn btn-outline-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle"></i> <!-- Bootstrap icon -->
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li>
              <a class="dropdown-item" href="profile.html">
                  <i class="bi bi-person-circle me-2"></i> See Profile Information
              </a>
            </li>
          <li>
            <button class="dropdown-item" onclick="updatePersonalInfo()">
              <i class="bi bi-pencil-square me-2"></i> Update Personal Information
            </button>
          </li>
          <li>
            <button class="dropdown-item" onclick="updatePassword()">
              <i class="bi bi-key me-2"></i> Update Password
            </button>
          </li>
          <li>
                <a class="dropdown-item text-danger" href="logout.php">
                  <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
<div class="container my-5 border border-2 rounded-3 shadow p-4 bg-light">


  <h4 class="mt-5">Update Existing Author</h4>
  <form method="POST" action="" novalidate>
    <input type="hidden" name="id" value="<?php echo $data['author_id']; ?>">
    <div class="mb-3">
      <label for="authorFirstName" class="form-label">First Name</label>
      <input type="text" value="<?php echo $data['author_FN']?>" class="form-control" name="authorFirstName" id="authorFirstName" required>
    </div>
    <div class="mb-3">
      <label for="authorLastName" class="form-label">Last Name</label>
      <input type="text" value="<?php echo $data['author_LN']?>" class="form-control" name="authorLastName" id="authorLastName" required>
    </div>
    <div class="mb-3">
      <label for="authorBirthYear" class="form-label">Birth Date</label>
      <input type="date" value="<?php echo isset($data['author_birthday']) ? date('Y-m-d', strtotime($data['author_birthday'])) : ''; ?>" class="form-control" name="authorBirthYear" id="authorBirthYear" max="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="mb-3">
      <label for="authorNationality" class="form-label">Nationality</label>
    <select class="form-select" name="authorNationality" id="authorNationality" required>
      <?php
        $nationalities = [
          "Filipino", "American", "British", "Canadian", "Chinese", "French",
          "German", "Indian", "Japanese", "Mexican", "Russian", "South African", "Spanish", "Other"
        ];
        foreach ($nationalities as $nat) {
          $selected = ($data['author_nat'] == $nat) ? 'selected' : '';
          echo "<option value=\"$nat\" $selected>$nat</option>";
        }
      ?>
    </select>
    </div>
    <button type="submit" name="updateAuthor" class="btn btn-primary">Update Author</button>
  </form>
  <?php echo $sweetAlertConfig; ?>
</div>

<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script> <!-- Add Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script> <!-- Correct Bootstrap JS -->
</body>
</html>
