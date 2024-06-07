<?php

ini_set('max_execution_time', '30000');
include_once 'Database.php';
include_once 'Category.php';

$db = new Database();
$conn = $db->getConnection();
$category = new Category($conn);

$category->truncate();

$startTime = microtime(true);

$parent = [0];
for ($i = 1; $i <= 1000000; $i++) {
    $parent_id = array_rand($parent);
    $categoryName = 'Category ' . $i;
    $pid = $category->create($categoryName,$parent_id);
    $parent[] = $pid;

    echo "$i records inserted\n";

}
$endTime = microtime(true);
$totalTime = $endTime - $startTime;

echo "Insertion of 1 million records completed in $totalTime seconds.\n";
?>
