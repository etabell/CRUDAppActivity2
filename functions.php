<?php
require_once 'db.php';

function highlightMatch($text, $query) {
    if (empty($query)) return htmlspecialchars($text);
    $regex = preg_quote($query, '/');
    return preg_replace("/($regex)/i", '<span class="highlight">$1</span>', htmlspecialchars($text));
}

function renderMovies($movies, $query) {
    $output = '';
    foreach ($movies as $movie) {
        $index = $movie['id'];
        $output .= "<tr>";
        $output .= "<td>" . highlightMatch($movie['title'], $query) . "</td>";
        $output .= "<td>" . highlightMatch($movie['director'], $query) . "</td>";
        $output .= "<td>" . highlightMatch($movie['genre'], $query) . "</td>";
        $output .= "<td>";
        $output .= "<a href=\"editmovie.php?id=$index\" class=\"btn btn-warning btn-sm\"><i class=\"fas fa-edit\"></i> Edit</a> ";
        $output .= "<a href=\"?action=delete&index=$index&search=" . urlencode($query) . "\" class=\"btn btn-danger btn-sm\" onclick=\"return confirm('Are you sure you want to delete the movie \\'" . htmlspecialchars($movie['title']) . "\\'?');\"><i class=\"fas fa-trash\"></i> Delete</a>";
        $output .= "</td>";
        $output .= "</tr>";
    }
    return $output;
}

function getFilteredMovies($query) {
    $conn = getDBConnection();
    if (empty($query)) {
        return getMovies();
    }
    $query = "%$query%";
    $stmt = $conn->prepare("SELECT id, title, director, genre FROM movies WHERE title LIKE :query OR director LIKE :query OR genre LIKE :query ORDER BY created_at DESC");
    $stmt->execute([':query' => $query]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function handleFormSubmission() {
    $title = trim($_POST['title']);
    $director = trim($_POST['director']);
    $genre = trim($_POST['genre']);
    $editIndex = isset($_POST['edit_index']) ? (int)$_POST['edit_index'] : null;

    if ($title && $director && $genre) {
        $movie = ['title' => $title, 'director' => $director, 'genre' => $genre];

        if ($editIndex !== null && getMovieById($editIndex)) {
            updateMovie($editIndex, $movie);
            $_SESSION['message'] = "Movie updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            saveMovie($movie);
            $_SESSION['message'] = "Movie added successfully!";
            $_SESSION['message_type'] = "success";
        }

        header("Location: index.php" . (isset($_GET['search']) ? "?search=" . urlencode($_GET['search']) : ""));
        exit;
    }
}

function deleteMovie($index) {
    $movie = getMovieById($index);
    if ($movie) {
        deleteMovieById($index);
        $_SESSION['message'] = "🗑️ Movie \"" . htmlspecialchars($movie['title']) . "\" has been deleted successfully!";
        $_SESSION['message_type'] = "success";
    }
    header("Location: index.php" . (isset($_GET['search']) ? "?search=" . urlencode($_GET['search']) : ""));
    exit;
}

function clearAllMoviesHandler() {
    clearAllMovies();
    $_SESSION['message'] = "All movies have been cleared!";
    $_SESSION['message_type'] = "success";
    header("Location: index.php");
    exit;
}
?>