<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT data FROM pdf_files WHERE id = $id");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        header("Content-Type: application/pdf");
        echo $row['data'];
    } else {
        echo "File tidak ditemukan!";
    }
}
?>
