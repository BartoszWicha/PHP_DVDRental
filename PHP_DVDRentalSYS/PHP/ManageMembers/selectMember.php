<link rel="stylesheet" href="../../CSS/DVDs.css">
<h1 class="header">Select Member</h1>

<?php
session_start();

if(session_status() === 2){
    session_destroy();
}

$surname = "";
$memID = "";
$text = "";

$action = "";
$redirectFile = "";

if($_GET['action'] == 1){
    $redirectFile = "../../PHP/ManageRentals/rentItem.php";
    $action = 1;
}

if($_GET['action'] == 2){
    $redirectFile = "../../PHP/ManageRentals/returnItem.php";
    $action = 2;
}

$thisFile = "../../PHP/ManageMembers/selectMember.php?action=".$action;

if(isset($_POST['memSearch'])){
    $surname = $_POST['surname'];
}

if(isset($_GET['Surname'])){
    $surname = $_GET['Surname'];
}

include '../../HTML/NavBar.html';
include '../../HTML/memSearch.html';

if(isset($_POST['memSearch'])){

    try {

        $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT count(*) FROM Members where SurName LIKE :surname AND status != \'C\'';
        $result = $pdo->prepare($sql);
        $result->bindValue(':surname', "%".$surname."%");
        $result->execute();

        if($result->fetchColumn() > 0){

            $sql = 'SELECT * FROM Members where SurName LIKE :surname AND status != \'C\'';
            $result = $pdo->prepare($sql);
            $result->bindValue(':surname', "%".$surname."%");
            $result->execute();

            ?>
            <table id="searchResults">
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>

            <?php

            while ($row = $result->fetch()) {
                ?>

                <tr>
                    <td><?php echo $row[1]." ".$row[2]; ?></td>
                    <td><?php echo $row[3]; ?></td>
                
                    <td ><a href="<?php echo $redirectFile?>?mem=<?php echo $row[0]?>">Select</a></td>
                </tr>
                
                <?PHP
            }

            ?>
            </table>
            <?php
        }

        else{
            $text = "No members Matching the surname were found";
        } 
    }

    catch (PDOException $e) {
        $text = "Unknown error occured";
        //$text = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    }
}


?>
<p class = "message">
    <?php echo $text ?>
</p>
<?php

include '../../HTML/footer.html';

?>