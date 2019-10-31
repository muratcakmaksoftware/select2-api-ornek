<?php
	
	require_once "db.php";

	if(isset($_POST["select"])){		
		$select = $_POST["select"];

		$jsonResult = array('Response' => "False");		
		switch($select){
			case "usersFilter":
	
				if(isset($_POST["search"])){
					$search = $_POST["search"];
					if(strlen($search) >= 3){
						try
						{
							$result = $db->prepare("SELECT * FROM users WHERE isim LIKE ?");
							$result->execute(array("%$search%"));							
							$data = array();												
							while($row = $result->fetch(PDO::FETCH_ASSOC)) 
							{								
								$data[] = $row; //array('id' => $row["id"], 'text' => $row["isim"]);
							}				
							
							if(count($data) > 0)
								$jsonResult = array('Response' => "True", 'results' => $data);
							else
								$jsonResult = array('Response' => "False", 'results' => "Aranılan Bulunamadı!");
						}
						catch(Exception $e)
						{
							$jsonResult = array('Response' => "False", 'results' => $e->getMessage());
						}
					}
					else{
						$jsonResult = array('Response' => "False", 'results' => "Arama karakteri yetersiz!");
					}
				}
				else{
					$jsonResult = array('Response' => "False", 'results' => "Parametre Eksik!");
				}
	
				break;
			case "usersGet":
				if(isset($_POST["userid"])){
					$userid = $_POST["userid"];
					try
					{
						$result = $db->prepare("SELECT * FROM users WHERE id LIKE ?");
						$result->execute(array($userid));
						$data = "";
						while($row = $result->fetch(PDO::FETCH_ASSOC)) 
						{								
							$data = $row;
						}				
						
						if($data != "")
							$jsonResult = array('Response' => "True", 'results' => $data);
						else
							$jsonResult = array('Response' => "False", 'results' => "ID Bilgisi Yanlış!");
					}
					catch(Exception $e)
					{
						$jsonResult = array('Response' => "False", 'results' => $e->getMessage());
					}
				}
				else{
					$jsonResult = array('Response' => "False", 'results' => "Parametre Eksik!");
				}
				break;
		}	
		
	}
	else{
		$jsonResult = array('Response' => "False", 'results' => "Parametre Hatası");
	}
	

	echo json_encode($jsonResult, JSON_UNESCAPED_UNICODE);
?>