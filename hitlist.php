<?php
	try{
		$db = new PDO('sqlite:hitlist.sqlite');
		$db->exec( "CREATE TABLE hitlist ( id INTEGER PRIMARY KEY, ip TEXT UNIQUE, region TEXT )" );

		$stmt = $db->prepare( "INSERT INTO hitlist (ip, region) VALUES (:ip, :region)" );
		$stmt->bindParam( ':ip', $_SERVER['REMOTE_ADDR'] );
		$stmt->bindParam( ':region', $_POST["region"] );
		
		if( !$stmt->execute() ){
			http_response_code( 400 );
			print "We already know you!";
		}
	}
	catch( PDOException $e ){
		http_response_code( 500 );
		print "Exception: " . $e->getMessage();
	}
?>