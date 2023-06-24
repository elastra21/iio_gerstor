<?php
	$filePath ="../config/home.json";
	if($_SERVER['REQUEST_METHOD']=='POST'){
		$jsonString = file_get_contents($filePath);
		$data = json_decode($jsonString, true);
		$operation = $_POST['operation'];
		$section = $_POST['section'];
		$field = $_POST['field'];
		switch ($operation) {
			case 'add':
				$newData = $_POST['data'];
				$jsonData = json_decode($newData);

				if (is_null($jsonData)) $jsonData = $_POST['data'];
				
				if($field == 'personal'){
					$myLastElement = end($data[$section][$field]);
					$lastId = json_encode($myLastElement['id']);
					echo $lastId +1;
					$jsonData->id = $lastId + 1;
				}

				if(isset($_FILES["image"]["tmp_name"])){
					$image_name = json_decode($newData,true);
					$image = $_FILES['image']['tmp_name'];
					$image_name = $image_name['image'];

					$path = isset($_POST['path']) ? $_POST['path'] : "../archivos/$field/$image_name";
					move_uploaded_file($image, $path);
				}

				if(isset($_POST['subField'])){
					$subField = $_POST['subField'];
					array_push($data[$section][$field][$subField], $jsonData);
				}
				else if ($section == $field){
					array_push($data[$section], $jsonData);
				}
				else{
					array_push($data[$section][$field], $jsonData);
				}
				echo $newData;
				break;
			case 'replace':
				$newData = $_POST['data'];
				$jsonData = json_decode($newData);

				if(isset($_POST['subField'])){
					$subField = $_POST['subField'];
					$data[$section][$field][$subField] = $jsonData;
				}
				else if ($section == $field){
					$data[$section] = $jsonData;
				}
				else {
					$data[$section][$field] = $jsonData;
				}
				if(isset($_FILES["image"]["tmp_name"])){
					$image_name = json_decode($newData,true);
					$image = $_FILES['image']['tmp_name'];
					$image_name = $image_name['image'];
					$path = isset($_POST['path']) ? $_POST['path'] : "../archivos/$field/$image_name";
					
					move_uploaded_file($image, $path);
				}
				
				break;
			case 'remove':
				$index = $_POST['index'];

				if(isset($_POST['subField'])){
					$subField = $_POST['subField'];
					unset($data[$section][$field][$subField][$index]);
					$data[$section][$field][$subField] = array_values($data[$section][$field][$subField]);
				}
				else if ($section == $field){
					unset($data[$section][$index]);
					$data[$section] = array_values($data[$section]);
				}
				else{
					unset($data[$section][$field][$index]);
					$data[$section][$field] = array_values($data[$section][$field]);
				}
				echo $index;
				break;
			case 'edit':
				$index = $_POST['index'];
				$newData = $_POST['data'];
				$jsonData = json_decode($newData);

				if (is_null($jsonData)) $jsonData = $_POST['data'];
				
				if(isset($_FILES["image"]["tmp_name"])){
					$image_name = json_decode($newData,true);
					$image = $_FILES['image']['tmp_name'];
					
					$path = isset($_POST['path']) ? $_POST['path'] : "../archivos/$field/$image_name";
					move_uploaded_file($image, $path);
				}
				if(isset($_POST['subField'])){
					$subField = $_POST['subField'];
					$data[$section][$field][$subField][$index] = $jsonData;
				}
				else if ($section == $field){
					$data[$section][$index] = $jsonData;
				}
				else{
					$data[$section][$field][$index] = $jsonData;
				}

				echo "edit";
				break;
			default:
			echo "the fuck are you doing";
			break;
		}
		$currentDate = new DateTime();
		$timezone = new DateTimeZone('America/Los_Angeles');
		$currentDate->setTimezone($timezone);
		$dateString = $currentDate->format('Y-m-d H:i:s');

		$data["version"]["no"] = $data["version"]["no"] + 1;
		$data["version"]["last_update"] = $dateString;

		$newJsonString = json_encode($data);

		rename($filePath, "./versions/$dateString.json");
		file_put_contents($filePath, $newJsonString);
	}
	else {
        echo "Error";
    }
?>
