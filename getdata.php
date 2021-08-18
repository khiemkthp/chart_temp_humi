<?php    
    $host = "localhost";
	$dbname = "iot_database";
	$username = "root";
	$password = "";
	$table_name = "senser";
	$options = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	);

	try {
		$conn = new PDO("mysql:host=$host;dbname=$dbname",$username,$password,$options);
	} catch(PDOEception $pe) {
    }
    $select = $conn->prepare("SELECT * FROM ".$table_name." ORDER BY STT DESC LIMIT 1");
    
    if(isset($_GET["check"])) {
        $check = $_GET['check'];
        if($check==1501) {
            $select->execute();
			$select->setFetchMode(PDO::FETCH_NUM);
			$result = $select->fetchALL();
			foreach($result as $row) {
                $value = array (
                    'STT'  =>$row[0],
                    'temp' =>$row[1],
                    'humi' =>$row[2]
                );
                echo json_encode($value);				
            }
        }
        
    }
    
?>