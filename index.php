<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdf_file"])) {
    $file_name = $_FILES["pdf_file"]["name"];
    $file_tmp = $_FILES["pdf_file"]["tmp_name"];
    $file_data = addslashes(file_get_contents($file_tmp));

    $sql = "INSERT INTO pdf_files (name, data) VALUES ('$file_name', '$file_data')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('PDF berhasil diunggah!');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload & Tampilkan PDF</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <link rel="shortcut icon" href="assets/ic-pdf-100.png" type="image/png">
    <script>
        function showPDF(id) {
            document.getElementById('pdfViewer').src = 'view.php?id=' + id;
            document.getElementById('pdfModal').classList.remove('hidden');
        }

        function closePDF() {
            document.getElementById('pdfModal').classList.add('hidden');
            document.getElementById('pdfViewer').src = "";
        }

        function generateThumbnail(pdfUrl, canvasId) {
            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                return pdf.getPage(1);
            }).then(page => {
                var scale = 1.5;
                var viewport = page.getViewport({ scale: scale });

                var canvas = document.getElementById(canvasId);
                var context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                var renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                return page.render(renderContext).promise;
            }).catch(error => {
                console.error("Error loading PDF:", error);
            });
        }
    </script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-3xl mx-auto bg-white p-6 shadow-lg rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Upload PDF</h2>
        <form method="post" enctype="multipart/form-data" class="space-y-4">
            <input type="file" name="pdf_file" accept="application/pdf" required class="border p-2 rounded w-full">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
        </form>
    </div>

    <div class="max-w-6xl mx-auto bg-white p-6 shadow-lg rounded-lg mt-6">
        <h2 class="text-xl font-semibold mb-4">Daftar PDF</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php
            $result = $conn->query("SELECT id, name FROM pdf_files");
            while ($row = $result->fetch_assoc()) {
                $pdfUrl = "view.php?id=" . $row['id'];
                echo "<div class='bg-gray-200 p-4 rounded-lg shadow-lg'>
                        <canvas id='canvas_" . $row['id'] . "' class='w-full h-48 object-cover rounded'></canvas>
                        <h3 class='text-lg font-semibold mt-2'>" . $row['name'] . "</h3>
                        <div class='mt-3 flex justify-between'>
                            <button onclick='showPDF(" . $row['id'] . ")' class='bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600'>Lihat Selengkapnya</button>
                            <a href='download.php?id=" . $row['id'] . "' class='bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600'>Download</a>
                        </div>
                        <script>
                            generateThumbnail('$pdfUrl', 'canvas_" . $row['id'] . "');
                        </script>
                     </div>";
            }
            ?>
        </div>
    </div>

    <!-- Modal PDF -->
    <div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full relative">
            <button onclick="closePDF()" class="absolute top-3 right-3 text-gray-700 hover:text-red-600 text-2xl">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Pratinjau PDF</h2>
            <iframe id="pdfViewer" src="" width="100%" height="500px" class="border"></iframe>
        </div>
    </div>

</body>
</html>
