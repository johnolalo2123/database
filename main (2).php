<?php
$host = 'localhost'; 
$db = 'file_operations';
$user = 'root'; 
$pass = ''; 


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['filename'])) {
        $filename = $_GET['filename'];
        
 
        $stmt = $pdo->prepare("SELECT * FROM files WHERE filename = ?");
        $stmt->execute([$filename]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            echo "File contents from database: " . nl2br($file['content']);
        } else {
            echo "File does not exist in the database.";
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['filename'])) {
        $filename = $_POST['filename'];

        $stmt = $pdo->prepare("SELECT * FROM files WHERE filename = ?");
        $stmt->execute([$filename]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

   
        if ($file) {
            echo "File contents from database: " . nl2br($file['content']);
        } else {
            echo "File does not exist in the database.";
        }


        if (isset($_POST['write']) && !empty($_POST['write'])) {
            // Insert or update the file content in the database
            $stmt = $pdo->prepare("INSERT INTO files (filename, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = ?");
            $stmt->execute([$filename, $_POST['write'], $_POST['write']]);
            echo "File written to database successfully!";
        }

        if (isset($_POST['read'])) {
            $stmt = $pdo->prepare("SELECT * FROM files WHERE filename = ?");
            $stmt->execute([$filename]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($file) {
                $lines = explode("\n", $file['content']); // Break the content into lines
                echo "File contents (line by line):<br>";
                foreach ($lines as $line) {
                    echo htmlspecialchars($line) . "<br>";
                }
            } else {
                echo "No content found in the database.";
            }
        }
    }
}

