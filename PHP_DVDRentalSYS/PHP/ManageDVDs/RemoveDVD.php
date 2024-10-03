<link rel="stylesheet" href="../../CSS/DVDs.css">
<h1 class="header">Remove DVD</h1>
<?php

$dvdID = $_GET['dvd'];
$thisFile = "../../PHP/ManageDVDs/RemoveDVD.php?".$dvdID;

include '../../HTML/NavBar.html';

if(isset($_POST['yesDelete'])){
    
    try {
        
        $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT Count(*) from rentedItems WHERE DVDID = :dvdID';
        $result = $pdo->prepare($sql);
        $result->bindValue(':dvdID', $dvdID);
        $result->execute();

        if($result->fetchColumn() > 0){
            
            $sql = "update DVDs set Status = 'U' WHERE DVDID = :dvdID";
            $result = $pdo->prepare($sql); 
            $result->bindValue(':dvdID', $dvdID);
            $result->execute();
        }
        
        else{         
            $sql = 'DELETE FROM dvds WHERE DVDID = :dvdID';
            $result = $pdo->prepare($sql);
            $result->bindValue(':dvdID', $dvdID);
            $result->execute();
        }
    }

    catch (PDOException $e) {
        //echo $e->getMessage();
        ?>
        <p class="message">Unknown Error has occured</p>
        <?php
    }

    ?>
    <p class="message">
        DVD has been Deleted
    </p>
    <p class="message"><a href="../../HTML/mainMenu.html" class="textLink">
        Return to Main Menu
    </p></a>
    <?php
}

elseif(isset($_POST['noDelete'])){
    ?>
    <p class="message">
        Deletion has been Canceled
    </p>
    <p class="message"><a href="../../HTML/mainMenu.html" class="textLink">
        Return to Main Menu
    </p></a>
    <?php
}

else{
    
    include "../../HTML/DeleteDVDForm.html";
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT dvds.DVDID, dvds.Title, dvds.Status, Categories.Description FROM DVDs JOIN Categories ON dvds.CatCode = Categories.CatCode WHERE DVDID = :dvdID';
        $result = $pdo->prepare($sql);
        $result->bindValue(':dvdID', $dvdID);
        $result->execute();
        $row = $result->fetch();
        ?>
                <table id="searchResults" style = "margin-top:0px;">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Category</th>
                    </tr>
                    <tr>     
                        <td><?php echo $row[0];?> </td>
                        <td><?php echo $row[1];?> </td>
                        <td><?php echo $row[2];?> </td>
                        <td><?php echo $row[3];?> </td> 
                    </tr>
                </table>
            </div>
        <?php
    }

    catch (PDOException $e) {
        ?>
        <p class="message">Unknown Error has occured</p>
        <?php
    }

}


include '../../HTML/footer.html';
?>
