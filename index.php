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
		//echo "Connect to ".$dbname." at ".$host." successfully"."<br>";
	} catch(PDOException $pe) {
		die("Could not connect to the database".$pe->getMessage());
	}

	//Nhận dữ liệu từ esp và xử lí 
	echo "Hello"."<br>";
	if(isset($_GET["temp"])&&isset($_GET["humi"])) {
		$temp = $_GET['temp'];
		$humi = $_GET['humi'];
		//echo $temp.$humi ;
		
		$result = warning($temp);
		if($result) {
			update_warn(1);
			echo "_warning+";
		} else {
			update_warn(0);
			echo "_safe+";
		} 
		

		if(allow_insert($temp)) {
			
			$insert = $conn->prepare("INSERT INTO ".$table_name." (temp,humi) VALUES (:temp,:humi)");
			$insert->bindParam(':temp',$temp);
			$insert->bindParam(':humi',$humi);
		
			$insert->execute();
			echo "<br>Insert Success:";
			var_dump($temp);
			var_dump($humi);

		}
		
		/* $insert = $conn->prepare("INSERT INTO ".$table_name." (temp,humi) VALUES (:temp,:humi)");
			$insert->bindParam(':temp',$temp);
			$insert->bindParam(':humi',$humi);
		
			$insert->execute();
			echo "<br>Insert Success:";
			var_dump($temp);
			var_dump($humi); */
	}

	//Lưu trữ yêu cầu nhập liệu từ người dùng
	if(isset($_GET["change"])&&isset($_GET["spinner_dura"])&&isset($_GET["spinner_temp"])) {
		$change = $_GET['change'];
		$pre_limit =(int) ($_GET['spinner_dura'] / 5);
		$pre_temp = (float) $_GET['spinner_temp'];

		$update = $GLOBALS['conn']->prepare("UPDATE present set pre_temp = '$pre_temp' , pre_limit = '$pre_limit' , pre_count = '1' where STT = '1' ");
		if($change==1501) {
			$update->execute();
		}
	}

	//Lưu trữ yêu cầu mức cảnh báo từ người dùng
	if(isset($_GET["change"])&&isset($_GET["type"])) {
		$change = $_GET['change'];
		$type = $_GET['type'];
		$num =  $_GET['spinner_num'];
		$medi = $_GET['spinner_medi'] ; 
		$min = $_GET['spinner_min'] ;
		$max = $_GET['spinner_max'] ;
		
		$update = $GLOBALS['conn']->prepare("UPDATE present set pre_num='$num', pre_medi='$medi', pre_min = '$min', pre_max = '$max', pre_type = '$type'  where STT = '1' ");
	
		if($change==1502) {
			$update->execute();
		}
	}
	////////mcu LCD
	if(isset($_GET["check"])) {
		$check = $_GET['check'];
		if($check==1503) {
			$res = "";
			$select = $GLOBALS['conn']->prepare("SELECT * FROM ".$table_name." ORDER BY STT DESC LIMIT 1");
			$select->execute();
			$select->setFetchMode(PDO::FETCH_NUM);
			$result = $select->fetchALL();
			foreach($result as $row) {
				$res = $res."*t:".$row[1]."&h:".$row[2];				
			}
			$present = $GLOBALS['conn']->prepare("SELECT * FROM present");
			$present->execute();
			$present->setFetchMode(PDO::FETCH_NUM);
			$rs = $present->fetchALL();
			foreach($rs as $row) {
				
				if($row[9]==1) {
					$res = $res."_warning+"; 
				}
				else {
					$res = $res."_safe+";
				}				
			}
			echo $res;
		}
	}
	
	//Xử lí, xác dịnh tính hợp lệ của dữ liệu so với yêu cầu nhập liệu từ người dùng
	function allow_insert($temp) {
		//Lấy nhiệt độ gần nhất để so sánh
		$select = $GLOBALS['conn']->prepare("SELECT * FROM ".$GLOBALS['table_name']." ORDER BY STT DESC LIMIT 1");
		$select->execute();
		$select->setFetchMode(PDO::FETCH_NUM);
		$result = $select->fetchALL();
		foreach($result as $row) {
			$value = array (
				'temp' =>$row[1],
				'time' =>$row[2]
			);			
		}

		//Lấy dữ liệu đánh giá
		$select_pre = $GLOBALS['conn']->prepare("SELECT * FROM present");
		$select_pre->execute();
		$select_pre->setFetchMode(PDO::FETCH_NUM);
		$result_pre = $select_pre->fetchALL();
		foreach ($result_pre as $row) {
			$pre_temp =(float) $row[1];
			$pre_count = (int) $row[2];
			$pre_limit = (int) $row[3];
		}
		
		//Đánh giá
		$temp_last = (float) $value['temp'];
		if($pre_count==$pre_limit) {
			update_count(1);
			if(abs($temp-$temp_last)>= $pre_temp) {
				return true;
			}
		} else {
			$pre_count += 1;
			update_count($pre_count);
		}
		return false;
	}

	function update_count($pre_count) {
		$update = $GLOBALS['conn']->prepare("UPDATE present set pre_count = '$pre_count' where STT = '1' ");
		$update->execute();
	}



	function warning ($temp) {
		$select_pre = $GLOBALS['conn']->prepare("SELECT * FROM present");
		$select_pre->execute();
		$select_pre->setFetchMode(PDO::FETCH_NUM);
		$result_pre = $select_pre->fetchALL();
		foreach ($result_pre as $row) {
			$num =(int) $row[4];
			$medi = (float) $row[5];
			$min = (int) $row[6];
			$max = (int) $row[7];
			$type = (int) $row[8];
		}

		if($type == 1) {
			return assess_medi($temp,$num,$medi);
		}
		if($type == 2) {
			return assess_range($temp,$min,$max);
		}
		if($type == 3) {
			return (assess_medi($temp,$num,$medi)||assess_range($temp,$min,$max));
		}
		
	}


	function assess_medi($temp,$num,$medi) {
		$num = (string) $num;
		$select = $GLOBALS['conn']->prepare("SELECT * FROM ".$GLOBALS['table_name']." ORDER BY STT DESC LIMIT ".$num);
		$select->execute();
		$select->setFetchMode(PDO::FETCH_NUM);
		$result = $select->fetchALL();
		$data = array();
		foreach($result as $row) {
			$result = (float)$row[1];
			array_push($data,$result);		
		}
		$data = array_filter($data);
		$svg = (float) array_sum($data)/count($data);
		$temp = (float) $temp ;
		if(abs($temp-$svg)>=$medi) {
			return true;
		}
		return false;
	}

	function assess_range($temp,$min,$max) {
		if($temp<$min||$temp>$max) {
			return true;
		}
		return false;
	}

	function update_warn($warn) {
		$update = $GLOBALS['conn']->prepare("UPDATE present set pre_warn = '$warn' where STT = '1' ");
		$update->execute();
	}
?>

	

	

	
