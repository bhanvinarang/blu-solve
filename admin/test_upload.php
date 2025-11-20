<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>POST Data:</h3>";
    print_r($_POST);
    
    echo "<h3>FILES Data:</h3>";
    print_r($_FILES);
    
    if (isset($_FILES['images'])) {
        echo "<h3>Images Details:</h3>";
        foreach ($_FILES['images']['name'] as $i => $name) {
            echo "Image $i: " . $name . " - Error: " . $_FILES['images']['error'][$i] . "<br>";
        }
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="images[]" multiple>
    <button type="submit">Test Upload</button>
</form>