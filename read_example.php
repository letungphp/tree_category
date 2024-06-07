<?php
ini_set('max_execution_time', '3000');
include_once 'Database.php';
include_once 'Category.php';

$database = new Database();
$db = $database->getConnection();

$category = new Category($db);


$startTime = microtime(true);

$parent_id = rand(0,1000000); //read random parent id
echo "Parent ID : $parent_id <br>";
$stmt = $category->readSubtree($parent_id);
$num = $stmt->rowCount();

$endTime = microtime(true);
$totalTime = $endTime - $startTime;

echo "Query in $totalTime seconds.<br>";

if($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        echo "ID: {$id}, Name: {$name}, Parent Path: {$parent_path}\n <br>";
    }
} else {
    echo "No categories found.\n <br>";
}

$endTime = microtime(true);
$totalTime = $endTime - $startTime;

echo "Completed in $totalTime seconds.";

?>
