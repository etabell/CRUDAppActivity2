<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'functions.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleFormSubmission();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie - Movie Collection Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-secondary shadow rounded p-4">
                    <h2 class="text-center text-warning">
                        <i class="fas fa-plus"></i> Add New Movie
                    </h2>
                    <form id="movieForm" method="POST" class="row g-2">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label"><i class="fas fa-film"></i> Movie Title</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter movie title" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="director" class="form-label"><i class="fas fa-user"></i> Director</label>
                            <input type="text" name="director" id="director" class="form-control" placeholder="Enter director name" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="genre" class="form-label"><i class="fas fa-tags"></i> Genre</label>
                            <input type="text" name="genre" id="genre" class="form-control" placeholder="Enter genre" required>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex flex-column gap-2" id="buttonContainer">
                                <button type="submit" id="addBtn" class="btn btn-success add-movie-btn">
                                    <i class="fas fa-plus"></i> Add Movie
                                </button>
                                <a href="index.php" class="btn btn-secondary w-100">
                                    <i class="fas fa-arrow-left"></i> Back to Movie List
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Display messages as JavaScript alerts -->
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            alert("<?php echo addslashes($_SESSION['message']); ?>");
        </script>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
</body>
</html>