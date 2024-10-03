<link rel="stylesheet" href="../../CSS/DVDs.css">
<h1 class="header">Add DVDs</h1>
<?php

$dvdTitle = "";
$catCode = "";
$thisFile = "../../PHP/ManageDVDs/AddDVD.php";
$defaultCategory = str_replace(" ", "&nbsp;", "Select Category");
$text = "";


if(isset($_POST['submitdetails'])){
    $dvdTitle = $_POST['Title'];
    $dvdTitle = str_replace(" ", "&nbsp;", $dvdTitle);

    $defaultCategory = str_replace(" ", "&nbsp;", $_POST['categories']);
}

include '../../HTML/NavBar.html';

if (isset($_POST['submitdetails'])) {

    try {

        if ($dvdTitle == ''){
            $text = "DVD Title must be enetered";
        }

        elseif(substr($_POST['categories'], 0, 6) == 'Select'){
            $text = "Must select a category";
        }

        elseif (strlen($dvdTitle) > 30){
            $text = "DVD title cannot containt more than 30 characters";
        }

        else{
            $catCode = substr($_POST['categories'], 0, 3);

            $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO dvds (Title, catCode) VALUES(:title, :catCode)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':title', $_POST['Title']);
            $stmt->bindValue(':catCode', $catCode);
            $stmt->execute();
            
            $text = "DVD has been succesfully added";
        
            $dvdTitle = "";
            $defaultCategory = str_replace(" ", "&nbsp;", "Select Category");
        }
        
    }
        
        catch (PDOException $e) {
        
        //$errorCode = $e -> getCode();
        //$output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
       
        $text = "An unkown error occured";
        
    }        
}    


include '../../HTML/DVDInfo.html';

//echo info text
?>
<p class = "message">
<?php echo $text?>
</p>
<?php

include '../../HTML/footer.html';
?>