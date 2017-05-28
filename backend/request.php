<?php

$servername = "";
$username = "";
$password = "";
$dbname = "";

try {

	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $conn->prepare("SELECT * , DATE_FORMAT( due_by,  '%d/%m/%y %H:%i' ) AS due_by_br, DATE_FORMAT( due_by,  '%d' ) AS due_by_br_day, DATE_FORMAT( due_by,'%b') AS due_by_br_month, 
        (CASE WHEN DATE_FORMAT( updated_at, '%d/%m/%y' ) = DATE_FORMAT( NOW( ) , '%d/%m/%y' ) THEN '1' ELSE '0' END) AS Hoje FROM tickets WHERE view=207296 ORDER BY due_by"); 

	$stmt->execute();

	// set the resulting array to associative
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	header('Content-Type: application/json');
	echo json_encode($res);

}catch(PDOException $e) {
	$fp = fopen('info_request.txt', 'a');
	fwrite($fp, "Error: " . $e->getMessage().PHP_EOL);
	fclose($fp);
}

$conn = null;

?>
