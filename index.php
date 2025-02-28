<?php 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$db = new MySQLi('localhost', 'root', '2222', 'test');
$db->query("
    CREATE TABLE IF NOT EXISTS `fotos` (
    `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `img` VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
");

$dbResponse = false;

if(isset($_POST['delete'])) {
    $del = $db->query("SELECT * FROM `fotos`")->fetch_all(MYSQLI_ASSOC);
    $db->query("TRUNCATE TABLE `fotos`");
    $db->query("ALTER TABLE `fotos` AUTO_INCREMENT=0");

    foreach($del as $link) {
        unlink($link['img']);
    }
}
else {
    $dbResponse = $db->query("SELECT * FROM `fotos`")->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php 
    if($dbResponse) {
        echo'<hr>';
        echo '<center>
            <form method="post" action="index.php">
                <br><input type="submit" name="delete" value="Löschen alle Daten aus DatenBank"/><br>
            </form>
        </center>';
        echo'<br><hr>';
        foreach($dbResponse as $link) {
            echo '<img src="'.$link['img'].'" width="300px" alt="">&#160;&#160;&#160;';
        }
    }
    else {
        echo 'Es gibt keine Fotos in Datenbank';
        echo '
            <form method="get" action="erstellen.php">
                <br><input type="submit" name="erstellen" value="Fotos erstellen"/><br>
            </form>
        ';
    }
    ?>

</body>
</html>