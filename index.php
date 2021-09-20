<!-- 
Saeed Falana
saeed.falana@gmail.com
-->
<html>
<title>read xml file and convert date to csv file using php</title>
<?php
$username = 'admin';
$password = 'hik12345';
$url = "https://192.168.1.64/ISAPI/Traffic/channels/1/vehicleDetect/plates";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch,CURLOPT_TIMEOUT, 30);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "<AfterTime version=\"2.0\"><picTime>2021-06-08T00:00:00Z</picTime></AfterTime>");
$first_response = curl_exec($ch);
$info = curl_getinfo($ch);
preg_match('/WWW-Authenticate: Digest (.*)/', $first_response, $matches);
		if(!empty($matches)) 
		{
			$auth_header = $matches[1];
			$auth_header_array = explode(',', $auth_header);
			$parsed = array();
			foreach ($auth_header_array as $pair)
			{
				$vals = explode('=', $pair);
				$parsed[trim($vals[0])] = trim($vals[1], '" ');
			}
			$response_realm = (isset($parsed['realm'])) ? $parsed['realm'] : "";
			$response_nonce = (isset($parsed['nonce'])) ? $parsed['nonce'] : "";
			$response_opaque = (isset($parsed['opaque'])) ? $parsed['opaque'] : "";
			$authenticate1 = md5($username.":".$response_realm.":".$password);
			$authenticate2 = md5("POST:".$url);
			$authenticate_response = md5($authenticate1.":".$response_nonce.":".$authenticate2);
			$request = sprintf('Authorization: Digest username="%s", realm="%s", nonce="%s", opaque="%s", uri="%s", response="%s"',
			$username, $response_realm, $response_nonce, $response_opaque, $url, $authenticate_response);
			$request_header = array($request);
			$request_header[] = 'Content-Type:application/json';
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch,CURLOPT_TIMEOUT, 30);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, "<AfterTime version=\"2.0\"><picTime>2020-06-20T00:00:00Z</picTime></AfterTime>");
			curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
			$result['response']= curl_exec($ch);
			// here we will Creat  xml file and store file inside Server 
				file_put_contents('outp.xml',($result['response']));
				$XML_Output=$result['response'];
		}
		$filexml='outp.xml';//input file
		$csv_file_name='cars.csv';//output file
		$xml = simplexml_load_file($filexml);
		if (file_exists($filexml)) 
		{
			$xml = simplexml_load_file($filexml);
			if(file_exists($csv_file_name))
			{
				$f = fopen($csv_file_name, 'a+');
				$headers = array(); 
			foreach ($xml->Plate->children() as  $field) 
			{ 
				// put the field name into array
				$headers[] = $field->getName(); 
				// print headers to CSV
			}
			
				if ($headers[0]!="captureTime") 
				{
				fputcsv($f, $headers, ',', '"');
				
				}
				foreach ($xml->Plate as $car) 
				{
					fputcsv($f, get_object_vars($car),',','"');
				}	
			}
			else
			{
				$f = fopen($csv_file_name, 'a+');
				$headers = array(); 
				foreach ($xml->Plate->children() as  $field) 
				{ 
					// put the field name into array
					$headers[] = $field->getName(); 
					// print headers to CSV
				}
					fputcsv($f, $headers, ',', '"');
					
					foreach ($xml->Plate as $car) 
					{
						fputcsv($f, get_object_vars($car),',','"');
					}

			}
		}
fclose($f);
?>
