<?php
require_once('database.php');

// Get category ID
if (!isset($category_id)) {
$category_id = filter_input(INPUT_GET, 'category_id', 
FILTER_VALIDATE_INT);
if ($category_id == NULL || $category_id == FALSE) {
$category_id = 1;
}
}

// Get name for current category
$queryCategory = "SELECT * FROM categories
WHERE categoryID = :category_id";
$statement1 = $db->prepare($queryCategory);
$statement1->bindValue(':category_id', $category_id);
$statement1->execute();
$category = $statement1->fetch();
$statement1->closeCursor();
$category_name = $category['categoryName'];

// Get all categories
$queryAllCategories = 'SELECT * FROM categories
ORDER BY categoryID';
$statement2 = $db->prepare($queryAllCategories);
$statement2->execute();
$categories = $statement2->fetchAll();
$statement2->closeCursor();

// Get records for selected category
$queryRecords = "SELECT * FROM records
WHERE categoryID = :category_id
ORDER BY recordID";
$statement3 = $db->prepare($queryRecords);
$statement3->bindValue(':category_id', $category_id);
$statement3->execute();
$records = $statement3->fetchAll();
$statement3->closeCursor();

?>
<div class="container">
<?php
include('includes/header.php');
?>


<section>
<div class="wrapper">
<form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-dark" type="submit" name="submit" >Search</button>
      </form>
	<?php 
		if(isset($_POST['submit'])){ 
			if(isset($_GET['go'])){ 
				if(preg_match("/^[  a-zA-Z]+/", $_POST['name'])){ 
					$name=$_POST['name']; 
					//connect  to the database 
					$db=mysql_connect  ("localhost", "root", "") or die ('I cannot connect to the database  because: ' . mysql_error()); 
					//-select  the database to use 
					$mydb=mysql_select_db("ca2"); 
					//-query  the database table 
                    $searchValue = $_POST['search'];
					$sql = "SELECT * FROM records WHERE price LIKE '%$searchValue%'";
					//-run  the query against the mysql query function 
					$result=mysql_query($sql); 
					//-create  while loop and loop through result set 
					if(mysql_num_rows($result) > 0){
						while($row=mysql_fetch_array($result)){ 
						$category_id=$row['category_id'];
							$name =$row['name']; 
							$allergens =$row['allergens']; 
							$price=$row['price'];
							$image=$row['image'];
						
							//-display the result of the array 
							echo "<table class='table table-bordered table-striped table-hover '>";
								echo "<thead>";
									echo "<tr>";
										echo "<th>Image</th>";
										echo "<th>Name</th>";
										echo "<th>Allergens</th>";
										echo "<th>Price</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
									echo "<tr>";
										echo "<td>" . $row['image'] . "</td>";
										echo "<td>" . $row['name'] . "</td>";
										echo "<td>" . $row['allergens'] . "</td>";
										echo "<td>" . $row['price'] . "</td>";
									echo "</tr>";
								echo "</tbody>";                            
							echo "</table>";
							echo "<p><a href='index.php' class='btn btn-primary'>Back</a></p>";
						} 
					} else {
						echo "<p>No matches found</p>";
						echo "<p><a href='index.php' class='btn btn-primary'>Back</a></p>";
					}
				} else { 
					echo  "<p>Please enter a search query</p>"; 
					echo "<p><a href='index.php' class='btn btn-primary'>Back</a></p>";
				} 
			} 
		} 
	?> 
	</div>
<!-- display a table of records -->
<h2 class = "categoryfont"><?php echo $category_name; ?></h2>
<table class="table">
    <thead class="table-dark">
<tr>
<th>Image</th>
<th>Name</th>
<th>Allergens</th>
<th>Price</th>
<th>Delete</th>
<th>Edit</th>
</tr>
</thead>
    <tbody>
<?php foreach ($records as $record) : ?>
<tr>
<td><img src="image_uploads/<?php echo $record['image']; ?>" width="100px" height="100px" /></td>
<td><?php echo $record['name']; ?></td>
<td><?php echo $record['allergens']; ?></td>
<td class="right"><?php echo $record['price']; ?></td>
<td><form action="delete_record.php" method="post"
id="delete_record_form">
<input type="hidden" name="record_id"
value="<?php echo $record['recordID']; ?>">
<input type="hidden" name="category_id"
value="<?php echo $record['categoryID']; ?>">
<input class="btn btn-danger" type="submit" value="Delete">
</form></td>
<td><form action="edit_record_form.php" method="post"
id="delete_record_form">
<input type="hidden" name="record_id"
value="<?php echo $record['recordID']; ?>">
<input type="hidden" name="category_id"
value="<?php echo $record['categoryID']; ?>">
<input class="btn btn-primary" type="submit" value="Edit">
</form></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</section>
<?php
include('includes/footer.php');
?>