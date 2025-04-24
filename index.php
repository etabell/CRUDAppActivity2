<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'functions.php';

// Handle delete and clear actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['index'])) {
        deleteMovie((int)$_GET['index']);
    } elseif ($_GET['action'] === 'clear') {
        clearAllMoviesHandler();
    }
}

// Get all movies for initial load
$movies = getFilteredMovies('');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Collection Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Highlight style for matched text */
        .highlight {
            background-color: #ffca28;
            color: #000;
            padding: 1px 2px;
            border-radius: 2px;
        }
    </style>
</head>
<body class="bg-dark text-light">
    <div class="container p-4 bg-secondary shadow rounded">
        <h1 class="text-center text-warning mb-4">
            <i class="fas fa-film"></i> Movie Collection Tracker
        </h1>

        <!-- Search Bar -->
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="searchBar" class="form-control" placeholder="Search by title, director, or genre...">
        </div>

        <!-- Add Movie Button -->
        <div class="mb-3">
            <a href="addmovie.php" class="btn btn-success add-movie-btn w-100">
                <i class="fas fa-plus"></i> Add New Movie
            </a>
        </div>

        <!-- Movie Table -->
        <table class="table table-dark table-striped table-bordered mt-4">
            <thead class="table-warning text-dark">
                <tr>
                    <th>Title</th>
                    <th>Director</th>
                    <th>Genre</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="movieList">
                <?php echo renderMovies($movies, ''); ?>
            </tbody>
        </table>

        <!-- Clear All Button -->
        <a href="?action=clear" class="btn btn-danger w-100 mt-3" onclick="return confirm('Are you sure you want to delete all movies?');">
            <i class="fas fa-trash"></i> Clear All Movies
        </a>
    </div>

    <!-- Logout Button -->
    <a href="auth.php?action=logout" class="btn btn-danger logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>

    <!-- JavaScript for Client-Side Search and Table Highlighting -->
    <script>
        // Store movies data from PHP
        const movies = <?php echo json_encode($movies); ?>;

        // Search functionality
        const searchBar = document.getElementById('searchBar');
        const movieList = document.getElementById('movieList');

        // Function to highlight matching text
        function highlightText(text, query) {
            if (!query) return text;
            const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }

        // Handle search filtering and highlighting
        searchBar.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const filteredMovies = movies.filter(movie => 
                movie.title.toLowerCase().includes(query) ||
                movie.director.toLowerCase().includes(query) ||
                movie.genre.toLowerCase().includes(query)
            );

            // Render filtered movies with highlighted text
            movieList.innerHTML = filteredMovies.map((movie, index) => `
                <tr>
                    <td>${highlightText(movie.title, query)}</td>
                    <td>${highlightText(movie.director, query)}</td>
                    <td>${highlightText(movie.genre, query)}</td>
                    <td>
                        <a href="editmovie.php?index=${index}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="?action=delete&index=${index}" class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete ${movie.title}?');">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            `).join('');
        });

        // Display message as JavaScript alert
        <?php if (isset($_SESSION['message'])): ?>
            alert("<?php echo addslashes($_SESSION['message']); ?>");
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
    </script>
</body>
</html>