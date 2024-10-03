
<link rel="stylesheet" href="../../CSS/DVDs.css">
<h1 class="header">Search DVDs</h1>
<?php
$thisFile = "../../PHP/ManageDVDs/SearchDVDTitle.php";
$title = "";
$text = "";

if(isset($_POST['dvdName'])){
    $title = $_POST['dvdName'];
}
include '../../HTML/NavBar.html';
include '../../HTML/dvdSearch.HTML';

//SQL Statement
if(isset($_POST['dvdSearch'])){
    try {

        $pdo = new PDO('mysql:host=localhost;dbname=dvdrental; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT count(*) FROM DVDs where Title LIKE :Title AND status != \'U\'';
        $result = $pdo->prepare($sql);
        $result->bindValue(':Title', '%'.$title.'%');
        $result->execute();

        if($result->fetchColumn() > 0){

            $sql = 'SELECT dvds.DVDID, dvds.Title, dvds.Status, Categories.Description FROM DVDs JOIN Categories ON dvds.CatCode = Categories.CatCode WHERE Title LIKE :Title AND status != \'U\' OR status != \'R\'';
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
                    <th colspan=2>Actions</th>
                </tr>

            <?php
            while ($row = $result->fetch()) {
            ?>

                <tr>
                    <td rowspan=2><?php echo $row[0]; ?></td>
                    <td rowspan=2><?php echo $row[1]; ?></td>
                    <td rowspan=2><?php echo $row[2]; ?></td>
                    <td rowspan=2><?php echo $row[3]; ?></td>
                
                    <td><a href="../../PHP/ManageDVDs/ModifyDVD.php?dvd=<?php echo $row['DVDID']?>">Update</a></td>
                </tr>
                <tr>
                    <td><a href="../../PHP/ManageDVDs/RemoveDVD.php?dvd=<?php echo $row['DVDID']?>">Delete</a></td>
                </tr>
                
                <?PHP
            }
            ?>
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

    ?>
    <p class = "message">
        <?php echo $text ?>
    </p>
    <?php
}

include '../../HTML/footer.html';

?>
