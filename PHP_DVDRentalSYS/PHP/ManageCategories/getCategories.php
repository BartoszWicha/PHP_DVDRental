<?php
try {

    $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT count(*) FROM categories';
    $result = $pdo->prepare($sql);
    $result->execute();
    if($result->fetchColumn() > 0)
    {
        $sql = 'SELECT * FROM categories';
        $result = $pdo->query($sql);
        while ($row = $result->fetch()) {
            $value = str_replace(" ", "&nbsp;", $row[0]. " - " .$row[1]);
            ?><option value=<?php echo $value?>><?php echo $row[0]. " - " .$row[1]?></option><?php
        }
    }
}
    
catch (PDOException $e) {
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}
?>