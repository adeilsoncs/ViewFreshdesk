<?php

$servername = "";
$username = "";
$password = "";
$dbname = "";

$debugTimeFreshDesk = FALSE;

try {

	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

	// exeption pdo
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
	#View1 - Tickets 180 dias
	// Make URL request
	$page = 1;
	$loop = 1;
	$IdView = '207296'; //View ID

	// Condition to validate if there is content on the page
	while ($loop == 1) {  
    // sinc
		$url = 'https://yourname.freshdesk.com/helpdesk/tickets/view/'. $IdView .'?format=json&page=' . $page;
		echo "<br>";

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_USERPWD, 'APIKEY:X');
		$inicioTime = microtime(true);
		$view1 = curl_exec($curl);
		$tempoExec = microtime(true) - $inicioTime;
		$view1 = json_decode($view1, true);

		if(count($view1) == 0){
			$loop = 0;
			continue;
		}

		if($debugTimeFreshDesk){
			$sql = "insert into requests(status_9,data) values(".$tempoExec.",now())";
			$conn->exec($sql);
			$idRequest = $conn->lastInsertId();
		}

		#process infos
		$idsDelete = "";
		foreach($view1 as $tickets){
			$idsDelete .= ",".$tickets['id'];
		}

		$idsDelete = ltrim($idsDelete,',');

		// sql to delete a record
		$sql = "DELETE FROM tickets WHERE id not in (".$idsDelete.") and view=" . $IdView . " and page=". $page;
		echo "<br>" . $sql;
		$conn->exec($sql);

		// prepare sql
		$stmt = $conn->prepare("INSERT INTO tickets (id,display_id,group_id,ticket_type,subject,requester_name,status,status_name,source_name,description,due_by,custom_field, view, page) 
			VALUES (:id, :display_id, :group_id, :ticket_type, :subject, :requester_name, :status, :status_name, :responder_name, :description, :due_by, :custom_field, :view, :page) 
			ON DUPLICATE KEY UPDATE 
			id=:id, display_id=:display_id, group_id=:group_id, subject=:subject, requester_name=:requester_name, status=:status, status_name=:status_name, source_name=:responder_name, description=:description, due_by=:due_by, custom_field=:custom_field, view=:view, page=:page");

		$stmt->bindParam(':id', $id);
		$stmt->bindParam(':display_id', $display_id);
		$stmt->bindParam(':group_id', $group_id);
		$stmt->bindParam(':ticket_type', $ticket_type);
		$stmt->bindParam(':subject', $subject);
		$stmt->bindParam(':requester_name', $requester_name);
		$stmt->bindParam(':status', $status);
		$stmt->bindParam(':status_name', $status_name);
		$stmt->bindParam(':responder_name', $responder_name);
		$stmt->bindParam(':description', $description);
		$stmt->bindParam(':due_by', $due_by);
		$stmt->bindParam(':custom_field', $custom_field);
		$stmt->bindParam(':view', $view);
		$stmt->bindParam(':page', $myPage);

		if(count($view1>0)){
			// insert a row
			foreach($view1 as $tickets){
				$id = $tickets['id'];
				$display_id = $tickets['display_id'];
				$group_id = $tickets['group_id'];
				$ticket_type = $tickets['ticket_type'];
				$subject = $tickets['subject'];
				$requester_name = $tickets['requester_name'];
				$status = $tickets['status'];
				$status_name = $tickets['status_name'];
				$responder_name = $tickets['responder_name'];
				$description = $tickets['description'];
				$due_by = $tickets['due_by'];
				$custom_field = array_values($tickets['custom_field'])[0];
				$view = $IdView;
				$myPage = $page;
				$stmt->execute();
			}
		}

		// next url url
		$page++;
	}

}catch(PDOException $e){

	$fp = fopen('/info_cron.txt', 'a');
	fwrite($fp, "Error: " . $e->getMessage().PHP_EOL);
	fclose($fp);
}

$conn = null;
