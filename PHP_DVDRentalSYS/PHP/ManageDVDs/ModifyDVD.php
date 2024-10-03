<link rel="stylesheet" href="../../CSS/DVDs.css">
<h1 class="header">Modify DVD</h1>
<?php
$dvdID = $_GET['dvd'];
$thisFile = "../../PHP/ManageDVDs/ModifyDVD.php?dvd=".$dvdID;
$dvdTitle = "";
$catCode = "";
$text = "";

include '../../HTML/NavBar.html';

try{
    $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT dvds.Title, dvds.catCode, Categories.Description FROM DVDs JOIN Categories ON dvds.CatCode = Categories.CatCode WHERE :dvdID = dvdID';
    $result = $pdo->prepare($sql);
    $result->bindValue(':dvdID', $dvdID);
    $result->execute();

    $row = $result->fetch();

    $dvdTitle = str_replace(' ', '&nbsp;', $row[0]);
    $defaultCategory = str_replace(' ', '&nbsp;', $row[1]." - ".$row[2]);

    if(isset($_POST['submitdetails'])){
        $dvdTitle = $_POST['Title'];
        $defaultCategory = $_POST['categories'];
    }
}

catch(PDOException $e){
    ?><p class="message">unknown error occured</p><?php
    //echo("something went wrong". $e->getFile(). $e->getMessage());
}

include '../../HTML/DVDInfo.html';

if(isset($_POST['submitdetails'])){

    try{

        if ($dvdTitle == ''){
            $text = "All Fields in the form must be enetered";
        }

        elseif (strlen($dvdTitle) > 30){
            $text = "DVD title cannot containt more than 30 characters";
        }

        else{
            $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "update DVDs set Title = :Title, CatCode = :catCode WHERE DVDID = :dvdID";
            $result = $pdo->prepare($sql);
            $result->bindValue(':Title', $_POST['Title']); 
            $result->bindValue(':dvdID', $dvdID); 
            $result->bindValue(':catCode', $_POST['categories']); 
            $result->execute();
        
            $sql = 'SELECT dvds.DVDID, dvds.Title, dvds.Status, Categories.Description FROM DVDs JOIN Categories ON dvds.CatCode = Categories.CatCode WHERE DVDID = :dvdID';
            $result = $pdo->prepare($sql);
            $result->bindValue(':dvdID', $dvdID);
            $result->execute();
            $row = $result->fetch();
            ?>
                <p class="message">DVD has been updated as follows</p>

                <table id="searchResults">
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

                <p class="message" style = "margin-top:70px;"><a href = "../../PHP/ManageDVDs/SearchDVDTitle.php" class="textLink">Go back to searching for dvds</a></p>
            <?php
        }
    }
    
            
    catch(PDOException $e){
        echo("something went wrong". $e->getFile(). $e->getMessage());
    }
}

?>
<p class="message">
    <?php echo $text; ?>
</p>
<?php


include '../../HTML/footer.html';
?>
