<link rel="stylesheet" href="../../CSS/DVDs.css">
<link rel="stylesheet" href="../../CSS/cart.css">
<h1 class="header">Rent Items</h1>
<?php
session_start();

if(!isset($_SESSION['cartIDs'])){
    $cart = array();
    $_SESSION['cartIDs'] = $cart;
}

$memID = $_GET['mem'];
$thisFile = "../../PHP/ManageRentals/rentItem.php?mem=".$memID;
$title = "";


if(isset($_POST['dvdSearch'])){
    $_SESSION['search'] = $_POST['dvdName'];
}

if(isset($_SESSION['search'])){
    $title = $_SESSION['search'];
}

$text = "";

if(isset($_GET['dvd'])){
    if(!in_array($_GET['dvd'], $_SESSION['cartIDs'])){ 
        array_push($_SESSION['cartIDs'], $_GET['dvd']);
    }
}

include '../../HTML/NavBar.html';

if(isset($_GET['mem'])){

    try{
        
        $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT count(*) FROM Rentals where MemberID = :memID AND status = \'U\'';
        $result = $pdo->prepare($sql);
        $result->bindValue(':memID', $_GET['mem']);
        $result->execute();

        if($result->fetchColumn() == 0){
            include '../../HTML/dvdSearch.html';
        }

        else{
            ?>
            <p class="message">
                Member has unreturned items!
            </p>
            <p class="message"><a href="../../HTML/mainMenu.html" class="textLink">
                Return To Main Menu
            </p></a>
            <?php
        }
    }

    catch (PDOException $e) {
        $text = "Unknown error occured";
        //$text = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    }

}

if(isset($_POST['dvdSearch']) || isset($_SESSION['search'])){
    try {
        
        if(isset($_SESSION['cartIDs'])){
            $array = array();
        
            foreach($_SESSION['cartIDs'] as $item => $value){
                array_push($array,$value);
            }
        }

        if(count($array) > 0){
            $sql = 'SELECT count(*) FROM DVDs where Title LIKE :Title AND status = \'A\' AND DVDID NOT IN ('.implode(',', $array).')';
        }
        else{ 
            $sql = 'SELECT count(*) FROM DVDs where Title LIKE :Title AND status = \'A\'';
        }

        $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $result = $pdo->prepare($sql);
        $result->bindValue(':Title', '%'.$title.'%');
        $result->execute();

        if($result->fetchColumn() > 0){

            if(count($array) > 0){
                $sql = 'SELECT dvds.DVDID, dvds.Title, dvds.Status, Categories.Description FROM DVDs JOIN Categories ON dvds.CatCode = Categories.CatCode WHERE Title LIKE :Title AND status = \'A\' AND DVDID NOT IN ('.implode(',', $array).')';
            }
            else{ 
                $sql = 'SELECT dvds.DVDID, dvds.Title, dvds.Status, Categories.Description FROM DVDs JOIN Categories ON dvds.CatCode = Categories.CatCode WHERE Title LIKE :Title AND status = \'A\'';
            }

            $result = $pdo->prepare($sql);
            $result->bindValue(':Title', '%'.$title.'%');
            $result->execute();
            ?>
            <table id="searchResults">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>

            <?php

            while ($row = $result->fetch()) {

                ?>
                <tr>
                    <td><?php echo $row[0]; ?></td>
                    <td><?php echo $row[1]; ?></td>
                    <td><?php echo $row[2]; ?></td>
                    <td><?php echo $row[3]; ?></td>
                
                    <td style="padding:0px;"><a href="<?php echo $thisFile."&dvd=".$row[0];?>">Add To Cart</a></td>
                </tr>
            <?PHP } ?>

            </table>

            <?php
        }

        else {
            $text = "No available dvds were found matching the provided name";
        } 
    }
    
    catch (PDOException $e) {
        $text = "Unknown error occured";
        //$text = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    }
}

if(isset($_GET['remove'])){
    $index = array_search($_GET['remove'],$_SESSION['cartIDs']);
    unset($_SESSION['cartIDs'][$index]);
}

?>
<p class = "message">
    <?php echo $text ?>
</p>
<?php

if(isset($_SESSION['cartIDs'])){
    $array = array();
    
    foreach($_SESSION['cartIDs'] as $item => $value){
        array_push($array,$value);
    }

    try{
        if(count($array) > 0){

            $arrayIN = implode(',', $array);

            $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = 'SELECT dvdid,title FROM DVDs WHERE DVDID IN ('.$arrayIN.')';
            $result = $pdo->prepare($sql);
            $result->execute();

            if(count($array) > 0){
            ?>
            <div id="cartContainer">
            <p style="font-size:20px;">Cart</p>
            <table id="cartItems">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>

            <?php
            }

            while($row = $result->fetch()){

                ?>
                <tr>

                    <td><?php echo $row[0]; ?></td>
                    <td><?php echo $row[1]; ?></td>
                
                    <td style="padding:0px;"><a href="<?php echo $thisFile."&remove=".$row[0];?>">Remove Item</a></td>
                </tr>
                <?php
            }
            ?></table><?php

            include '../../HTML/formRent.html';
        
        }
    }

    catch (PDOException $e) {
        $text = "Unknown error occured";
        //$text = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    }
}

include '../../HTML/footer.html';


?>
