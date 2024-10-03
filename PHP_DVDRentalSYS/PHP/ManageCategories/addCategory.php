<h1 class="header">Add Category</h1>
<link rel="stylesheet" href="../../CSS/DVDs.css">
<?php

include '../../HTML/NavBar.html';

$rates = "0.00";
$description = " ";

if (isset($_POST['submitdetails'])){
    $rates = $_POST['rates'];
    $description = $_POST['description'];
}

if (isset($_POST['submitdetails'])) {

    try {

        if ($description == '' or $rates == '0.00'){
            $text = "All Fields in the form must be enetered";
        }

        elseif (strlen($description) > 30){
            $text = "Description cannot containt more than 30 characters";
        }

        elseif ($rates <= 0){
            $text = "Value for rates must be higher than 0";
        }

        else{
            $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO Categories (Description, Rates) VALUES(:desc, :rates)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':desc', $description);
            $stmt->bindValue(':rates', $rates);
            $stmt->execute();
            
            $text="Category has been succesfully added";

            $description = "";
            $rates = "0.00";
        }
        
    }  
    catch (PDOException $e) {
        
        $text = 'An error has occurred';
        echo $text;
        //$output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
        
    }        
}    


include '../../HTML/addCategory.html';

include '../../HTML/footer.html';
?>
    <p class="message">
    <?php echo $text ?>    
    </p> 
<?php
?>