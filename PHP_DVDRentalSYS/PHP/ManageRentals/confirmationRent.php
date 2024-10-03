<link rel="stylesheet" href="../../CSS/DVDs.css">
<link rel="stylesheet" href="../../CSS/cart.css">
<link rel="stylesheet" href="../../CSS/Navigation.css">
<link rel="stylesheet" href="../../CSS/form.css">

<?php
session_start();

if(isset($_SESSION['cartIDs'])){
    
    //for every item in cart array update dvd status and rentalFile and rented items file
    try{
        $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //make record for rentals and retrieve rentID
        $sql = "INSERT INTO Rentals (Status, MemberID, Cost) VALUES('U', :memID, 0)";
        $result = $pdo->prepare($sql);
        $result->bindValue(':memID', $_GET['mem']); 
        $result->execute();

        $sql = "Select MAX(rentID) FROM rentals";
        $result = $pdo->prepare($sql);
        $result->execute();
        $row = $result->fetch();

        foreach($_SESSION['cartIDs'] as $item => $value){
            $sql = "update DVDs set Status = 'R' WHERE DVDID = :dvdID";
            $result = $pdo->prepare($sql);
            $result->bindValue(':dvdID', $value); 
            $result->execute();

            $sql = "INSERT INTO renteditems (rentID, dvdid, cost) VALUES(:rentID, :dvdID, :cost)";
            $result = $pdo->prepare($sql);
            $result->bindValue(':dvdID', $value); 
            $result->bindValue(':rentID', $row[0]);
            $result->bindValue(':cost', 0);
            $result->execute();
        }

        session_destroy();
    }

    catch (PDOException $e) {
        //echo $e->getMessage();
        ?>
        <p class="message">Unknown Error has occured</p>
        <?php
    }
}

?>
<p class="message" style="margin-top:40px;">Items have been rented and are no longer available</p>
<p class="message"><a class="textLink" href = "../../HTML/mainMenu.html">return to Main Menu</a></p>
<?
?>
