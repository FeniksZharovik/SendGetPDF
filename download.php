<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT name, data FROM pdf_files WHERE id = $id");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_name = $row['name'];
        $file_data = $row['data'];

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$file_name\"");
        echo $file_data;
    } else {
        echo "File tidak ditemukan!";
    }
}
?>
