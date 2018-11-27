<?php
// environment configuration
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require 'databaseConnection.php';

// update stats table 
$stmt = $dbStat->prepare("INSERT INTO agg_sessions (user_id, agg_sessions_date, agg_sessions_type, agg_sessions_count) VALUES (?, ?, ?, ?) 
	ON DUPLICATE KEY UPDATE agg_sessions_count = VALUES(agg_sessions_count)");
$stmtClient = $dbClient->prepare("INSERT INTO agg_sessions (customer_id, user_id, agg_sessions_date, agg_sessions_type, agg_sessions_count) VALUES (?, ?, ?, ?, ?) 
	ON DUPLICATE KEY UPDATE agg_sessions_count = VALUES(agg_sessions_count)");

// user sessions by date and api type
foreach($dbAurora->query("SELECT u.customer_id,u.user_id,CAST(s.server_api_call_date AS DATE) AS api_date,count(s.server_api_call_id) AS itemCount,
	IF(s.server_api_call_url = 'nextItem','matching','other') AS apiType 
	FROM user u,server_api_call s WHERE u.user_id = s.user_id AND s.server_api_call_date > '2017-01-01 00:00:00' AND u.user_test_account = 0 
	GROUP BY u.customer_id,u.user_id,apiType,api_date ORDER BY u.user_name ASC") as $row){
	// main portal
	if($row['customer_id'] == 0) $stmt->execute(array($row['user_id'], $row['api_date'], $row['apiType'], $row['itemCount']));
	
	// client portal 	
	$stmtClient->execute(array($row['customer_id'], $row['user_id'], $row['api_date'], $row['apiType'], $row['itemCount']));
}