<?php
require_once 'config.php';

function getMovies() {
    $conn = getDBConnection();
    $stmt = $conn->query("SELECT id, title, director, genre FROM movies ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function saveMovie($movie) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO movies (title, director, genre) VALUES (:title, :director, :genre)");
    $stmt->execute([
        ':title' => $movie['title'],
        ':director' => $movie['director'],
        ':genre' => $movie['genre']
    ]);
    return $conn->lastInsertId();
}

function updateMovie($id, $movie) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE movies SET title = :title, director = :director, genre = :genre WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':title' => $movie['title'],
        ':director' => $movie['director'],
        ':genre' => $movie['genre']
    ]);
}

function deleteMovieById($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

function clearAllMovies() { // Line 40
    $conn = getDBConnection();
    $conn->exec("DELETE FROM movies");
}

function getMovieById($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, title, director, genre FROM movies WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>