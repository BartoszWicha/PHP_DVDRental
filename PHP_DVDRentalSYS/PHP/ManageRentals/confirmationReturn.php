<link rel="stylesheet" href="../../CSS/DVDs.css">
<link rel="stylesheet" href="../../CSS/cart.css">
<link rel="stylesheet" href="../../CSS/confirmScreen.css">

<?php
//start session
session_start();

$cost = array();
$totalCostOfDvd = array();

$pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$dateRented = "";
$dueDate = "";
$dateToday = "";
$total = 0;
$overallTotal = "";

if(isset($_POST['Cancel'])){
    ?><p class="message">DVD Return has been Cancelled</p>
    <p class="message"><a href="../../PHP/ManageMembers/selectMember.php?action=2" class="textLink">
    Return To return items menu
    </p></a>
    <?php
}

if(isset($_SESSION['toReturnIDs'])){
    try{
        //get date
        $sql = "SELECT DateRented, dueDate, cost from rentals WHERE rentID = :rentID";
        $result = $pdo->prepare($sql);
        $result->bindValue(':rentID', $_SESSION['RentID']); 
        $result->execute();
        $row = $result->fetch();

        $dateRented = new DateTime($row[0]);
        $dueDate = new DateTime($row[1]);
        $dateToday = new DateTime(date("Y-m-d"));
        $overallTotal = 0;

        //for every item returned retrieve catCode and calculate cost
        foreach($_SESSION['toReturnIDs'] as $item => $value){

            // SELECT d.dvdid, c.rates from dvds d join categories c ON d.catCode = c.catCode WHERE dvdid = 2;
            $sql = "SELECT d.dvdid, c.rates, d.Title from dvds d join categories c ON d.catCode = c.catCode WHERE dvdid = :dvdID";
            $result = $pdo->prepare($sql);
            $result->bindValue(':dvdID', $value); 
            $result->execute();
            $row = $result->fetch();
            
            //save cost as session key
            //create new session array
            $cost[$row[0]] = $row[1];
        }

        $difference = date_diff($dateRented, $dateToday);

        foreach($_SESSION['toReturnIDs'] as $item => $value){

            if($difference->format('%a') == 0){ 
                /*Unknown (2023) Program to Find the Number of Days Between Two Dates in PHP, GeeksforGeeks. Available at: 
                https://www.geeksforgeeks.org/program-to-find-the-number-of-days-between-two-dates-in-php/ (Accessed: 28 April 2024).*/
                $totalCostOfDvd[$value] = $cost[$value];
            }
            else{
                $totalCostOfDvd[$value] = $difference->format('%a')*$cost[$value];
            }

            //if todays date > due date apply 30 euro fee for every dvd;
            if($dateToday > $dueDate){
                $totalCostOfDvd[$value] += 30;
            }

            //echo $totalCostOfDvd[$value].'<br>';
            $total += $totalCostOfDvd[$value];
        }
    }
    
    catch (PDOException $e) {
        $text = "Unknown error occured";
        //$text = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    }
}


if(isset($_POST['Confirm'])){

    ?><p class="message">Items have been succesfully returned</p>
    <p class="message"><a href="../../HTML/mainMenu.html" class="textLink">
    Return To Main Menu
    </p></a>
    <?php

    //if session exists
    if(isset($_SESSION['toReturnIDs'])){

        try{
            foreach($_SESSION['toReturnIDs'] as $item => $value){

                //set status for every dvd to A
                $sql = "UPDATE dvds SET status = 'A' WHERE dvdid = :dvdid";
                $result = $pdo->prepare($sql);
                $result->bindValue(':dvdid', $value); 
                $result->execute();
                
                //save cost, todays date in rentedItem where dvdId is :dvdid
                $sql = "UPDATE rentedItems SET cost = :totalCostOfDVD, DateReturned = :todaysDate WHERE dvdid = :dvdid AND rentID = :rentID";
                $result = $pdo->prepare($sql);
                $result->bindValue(':dvdid', $value); 
                $result->bindValue(':totalCostOfDVD', $totalCostOfDvd[$value]); 
                $result->bindValue(':todaysDate', $dateToday->format('Y-m-d')); 
                $result->bindValue(':rentID', $_SESSION['RentID']); 
                $result->execute();
            }

            //retrieve all dvds cost and add up
            $sql = "SELECT cost FROM rentedItems WHERE rentID = :rentID";
            $result = $pdo->prepare($sql);
            $result->bindValue(':rentID', $_SESSION['RentID']); 
            $result->execute();

            while($row = $result->fetch()){
                $overallTotal += $row['cost'];
            }

            $sql = "UPDATE rentals SET cost = :total WHERE rentID = :rentID";
            $result = $pdo->prepare($sql);
            $result->bindValue(':rentID', $_SESSION['RentID']); 
            $result->bindValue(':total', $overallTotal); 
            $result->execute();
            
            $sql = "SELECT COUNT(*) from RentedItems WHERE rentID = :rentID AND dateReturned IS NULL";
            $result = $pdo->prepare($sql);
            $result->bindValue(':rentID', $_SESSION['RentID']);
            $result->execute();
            $row = $result->fetch();
            //if select count(*) from renteditems where rentID is rentid == 0

            if($row[0] <= 0){
                //update set status = 'F' for rentals where rentid is rentid
                $sql = "UPDATE rentals SET status = 'F' WHERE rentID = :rentID";
                $result = $pdo->prepare($sql);
                $result->bindValue(':rentID', $_SESSION['RentID']); 
                $result->execute();
            }
            
            if(session_status() === 2){
                session_destroy();
            } 
        }
        
        catch (PDOException $e) {
            $text = "Unknown error occured";
            //$text = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
            echo $text;
        }
    }
}

if(!isset($_POST['Confirm']) && !isset($_POST['Cancel'])){  
    ?>
        <form action=confirmationReturn.php? method="post">
        <table id="searchResults">
        <tr>
            <th>Title</th>
            <th>Date Rented</th>
            <th>Due Date</th>
            <th>Daily Fee</th>
        </tr>
    <?php

    try{
        foreach($_SESSION['toReturnIDs'] as $item => $value){
            $sql = "SELECT d.dvdid, c.rates, d.Title from dvds d join categories c ON d.catCode = c.catCode WHERE dvdid = :dvdID";
            $result = $pdo->prepare($sql);
            $result->bindValue(':dvdID', $value); 
            $result->execute();
            $row = $result->fetch();

            ?>
            <tr>
            <td><?php echo $row[2]; ?></td>
            <td><?php echo $dateRented->format('d-m-Y') ?></td>
            <td><?php echo $dueDate->format('d-m-Y') ?></td>
            <td> €<?php echo $row[1]; ?></td>
            </tr>
            <?php
        }
    }

    catch (PDOException $e) {
        ?>
        <p class="message">Unknown Error has occured</p>
        <?php
    }

?>   
    </table>
    <p class="message">Total fee is: €<?php  echo number_format((float)$total, 2, '.', '') ?></p> 
    <!--Rich Bradshaw (https://stackoverflow.com/users/16511/ rich-bradshaw) Show a Number to to Decimal Places URL (version 2024-04-28) 
    https://stackoverflow.com/questions/4483540/show-a-number-to-two-decimal-places!-->
    <input id="confirm" type="submit" value="Confirm Payment" name="Confirm">
    <input id="cancel" type="submit" value="Cancel Payment" name="Cancel">                
</form>
<?php
}

?>