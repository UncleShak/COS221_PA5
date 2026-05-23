<?php
// test_db.php
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();
if ($conn) {
    echo "Connected successfully";
} else {
    echo "Connection returned null";
}
?>