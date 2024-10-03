<link rel="stylesheet" href="../../CSS/DVDs.css">
<link rel="stylesheet" href="../../CSS/cart.css">
<h1 class="header">Return Items</h1>
<?php

//get member id and check if unreturned tiems if no display message if yes display ui for returning

session_start();

if(!isset($_SESSION['toReturnIDs'])){
    $toReturn = array();
    $_SESSION['toReturnIDs'] = $toReturn;
}

$memID = $_GET['mem'];
$thisFile = "../../PHP/ManageRentals/returnItem.php?mem=".$memID;

include '../../HTML/NavBar.html';

if(isset($_GET['dvd'])){
    if(!in_array($_GET['dvd'], $_SESSION['toReturnIDs'])){ 
        array_push($_SESSION['toReturnIDs'], $_GET['dvd']);
    }
}

//if remove isset 
if(isset($_GET['remove'])){
    $index = array_search($_GET['remove'],$_SESSION['toReturnIDs']);
    unset($_SESSION['toReturnIDs'][$index]);
}

if(isset($_GET['mem'])){

    try{
        
        $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'SELECT count(*) FROM Rentals where MemberID = :memID AND status = \'U\'';
        $result = $pdo->prepare($sql);
        $result->bindValue(':memID', $_GET['mem']);
        $result->execute();

        if($result->fetchColumn()){

            //get rentID
            $sql = 'SELECT RentID FROM Rentals where MemberID = :memID AND status = \'U\'';
            $result = $pdo->prepare($sql);
            $result->bindValue(':memID', $_GET['mem']);
            $result->execute();
            $rentID = $result->fetchColumn();
            $_SESSION['RentID'] = $rentID;

            //get all dvds
            if(isset($_SESSION['toReturnIDs'])){
                $array = array();
            
                foreach($_SESSION['toReturnIDs'] as $item => $value){
                    array_push($array,$value);
                }
            }

            if(count($array) > 0){
                $sql = 'SELECT * FROM RentedItems WHERE RentID = :rentID and DateReturned IS null AND dvdid NOT IN ('.implode(',', $array).')';
            }
            else{
                $sql = 'SELECT * FROM RentedItems WHERE RentID = :rentID and DateReturned IS null';
            }

            $result = $pdo->prepare($sql);
            $result->bindValue(':rentID', $rentID);
            $result->execute();

            //for every item in rentedItems
            //add to ui

            if($result->fetchColumn()){
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

                if(count($array) > 0){
                    $sql = 'SELECT * FROM RentedItems WHERE RentID = :rentID and DateReturned IS null AND dvdid NOT IN ('.implode(',', $array).')';
                }
                else{
                    $sql = 'SELECT * FROM RentedItems WHERE RentID = :rentID and DateReturned IS null';
                }

                $result = $pdo->prepare($sql);
                $result->bindValue(':rentID', $rentID);
                $result->execute();

                while ($row = $result->fetch()) {
                    $sql = 'SELECT dvds.DVDID, dvds.Title, dvds.Status, Categories.Description FROM DVDs JOIN Categories ON dvds.CatCode = Categories.CatCode WHERE dvdid= :dvdid';
                    $result2 = $pdo->prepare($sql);
                    $result2->bindValue(':dvdid', $row[1]);
                    $result2->execute();
                    $row2 = $result2->fetch();

                    ?>
                    <tr>
                        <td><?php echo $row2[0]; ?></td>
                        <td><?php echo $row2[1]; ?></td>
                        <td><?php echo $row2[2]; ?></td>
                        <td><?php echo $row2[3]; ?></td>
                    
                        <td style="padding:0px;"><a href="<?php echo $thisFile."&dvd=".$row2[0];?>">Return</a></td>
                    </tr>

                    <?php
                }

                ?>
                </table>
                <?php
            }
        }

        else{
            ?>
            <p class="message">
                Member has no items to return
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

if(isset($_SESSION['toReturnIDs'])){
    $array = array();

    try{
        foreach($_SESSION['toReturnIDs'] as $item => $value){
            array_push($array,$value);
        }

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
                <p style="font-size:20px;">Items Being Returned</p>
                <table id="cartItems">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>

                <?php
                //for every item in array att to table
                while($row = $result->fetch()){

                    ?>
                    <tr>

                        <td><?php echo $row[0]; ?></td>
                        <td><?php echo $row[1]; ?></td>
                    
                        <td style="padding:0px;"><a href="<?php echo $thisFile."&remove=".$row[0];?>">Remove Item</a></td>
                    </tr>
                    <?php
                }

                ?></table>
                <?php 
                include '../../HTML/formReturn.html';
                // add a submit button to redirect to confirmation.php

            }
        }
    }

    catch (PDOException $e) {
        ?>
        <p class="message">Unknown Error has occured</p>
        <?php
    }
}

?>