<?php
	/* Type table */
	$types = array(
		"1" => "Number",
		"2" => "text",
		"3" => "date",		
		"4" => "text",
		"5" => "text",
	);
	
	function sendReq($url, $reurl){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// get headers too with this line
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$result = curl_exec($ch);
		// get cookie
		// multi-cookie variant contributed by @Combuster in comments
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
		$cookies = "";
		foreach($matches[1] as $item) {
			$cookies .= $item . ";";
		}
		curl_setopt($ch, CURLOPT_COOKIE, $cookies);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $reurl);
		$res = curl_exec($ch);
		return $res;
		curl_close($ch);
	}

	$dtlist = array();

	if (isset($_GET['token']) && isset($_GET['id']) && isset($_GET['Container'])){
		$token = $_GET['token']; //6D52F8F1-4600-4D5F-8B0A-02BA9F430916
		$id = $_GET['id'];
		$Container = $_GET['Container'];			
		$url = "http://staging.chasedatacorp.com/Admin/CustomData.aspx?v=2&Token=" . $token . "&id=" . $id . "&Container=" . $Container;
		$reurl = "http://staging.chasedatacorp.com/Admin/CustomData.aspx?v=2&T=" . $token . "&id=" . $id . "&Container=" . $Container;
		$res = sendReq($url, $reurl);

		if ($Container ==""){			
			$conlist = array();
			$containers = json_decode($res);
			foreach ($containers as $ele) {
				$tableData = array();
				$url = "http://staging.chasedatacorp.com/Admin/CustomData.aspx?v=2&Token=" . $token . "&id=" . $id . "&Container=" . $ele->id; 
				$reurl = "http://staging.chasedatacorp.com/Admin/CustomData.aspx?v=2&T=" . $token . "&id=" . $id . "&Container=" . $ele->id;
				$res = sendReq($url, $reurl);
				$customTable = json_decode($res);
				
				foreach ($customTable as $row) {
					array_push($tableData, array( "name" => $row->FieldName, "value" => $row->FieldName, "type" =>$types[$row->FieldType]));
				}

				$dtlist[$ele->id] = $tableData;				
				array_push($conlist, array("name" =>$ele->TableName, "value" =>  $ele->id));				
			}
		}		
	}

	echo json_encode(
		array(
			"container" => $conlist,
			"lists" => $dtlist, 
		)
	);
	# echo preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',json_encode($dtlist));
	// echo preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',json_encode($list));
