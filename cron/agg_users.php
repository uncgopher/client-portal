<?php
// environment configuration
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require 'databaseConnection.php';

// basic user info 
$aggResults = array();
foreach($dbAurora->query("SELECT u.*,CAST(s.server_api_call_date AS DATE) AS api_date,count(s.server_api_call_id) AS itemMatches 
FROM user u,server_api_call s WHERE u.user_id = s.user_id AND s.server_api_call_date > '2017-01-01 00:00:00' AND 
u.user_test_account = 0 GROUP BY u.customer_id,u.user_id,api_date ORDER BY u.user_name ASC") as $row){
	$aggResults[$row['customer_id']][$row['user_id']]['name'] = $row['user_name'];
	$aggResults[$row['customer_id']][$row['user_id']]['email'] = $row['user_email'];
	$aggResults[$row['customer_id']][$row['user_id']]['gender'] = $row['user_gender'];
	$aggResults[$row['customer_id']][$row['user_id']]['created'] = $row['user_date_created'];
	$aggResults[$row['customer_id']][$row['user_id']]['dob'] = $row['user_birthday'];
	$aggResults[$row['customer_id']][$row['user_id']]['zip'] = $row['user_zip'];
	$aggResults[$row['customer_id']][$row['user_id']]['calls'] += $row['itemMatches'];
	$aggResults[$row['customer_id']][$row['user_id']]['sessions']++;
	if(isset($aggResults[$row['customer_id']][$row['user_id']]['earliest']) == false OR strtotime($aggResults[$row['customer_id']][$row['user_id']]['earliest']) > strtotime($row['api_date'])) 
		$aggResults[$row['customer_id']][$row['user_id']]['earliest'] = $row['api_date'];
	if(isset($aggResults[$row['customer_id']][$row['user_id']]['latest']) == false OR strtotime($aggResults[$row['customer_id']][$row['user_id']]['latest']) < strtotime($row['api_date'])) 
		$aggResults[$row['customer_id']][$row['user_id']]['latest'] = $row['api_date'];
}

// update stats table 
$stmt = $dbStat->prepare("INSERT INTO agg_users (agg_users_id, agg_users_name, agg_users_email, agg_users_gender, agg_users_sessions, agg_users_calls, agg_users_create, agg_users_earliest, 
agg_users_latest, agg_users_dob, agg_users_zip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE agg_users_sessions = VALUES(agg_users_sessions), 
agg_users_calls = VALUES(agg_users_calls), agg_users_latest = VALUES(agg_users_latest)");
$stmtClient = $dbClient->prepare("INSERT INTO agg_users 
	(agg_users_id, customer_id, agg_users_name, agg_users_email, agg_users_gender, agg_users_sessions, agg_users_calls, agg_users_create, agg_users_earliest, 
		agg_users_latest, agg_users_dob, agg_users_zip) 
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
	ON DUPLICATE KEY UPDATE agg_users_sessions = VALUES(agg_users_sessions), agg_users_calls = VALUES(agg_users_calls), agg_users_latest = VALUES(agg_users_latest)");
foreach($aggResults as $customer_id => $customerInfo){
	// clients
	foreach($customerInfo as $key => $row){
		$dob = ($row['dob'] == "" ? null : $row['dob']);
		$stmtClient->execute(array($key, $customer_id, $row['name'], $row['email'], $row['gender'], $row['sessions'], $row['calls'], $row['created'], $row['earliest'], $row['latest'], $dob, $row['zip']));
	}
	
	// main portal
	if($customer_id == 0){
		foreach($customerInfo as $key => $row){
			$dob = ($row['dob'] == "" ? null : $row['dob']);
			$stmt->execute(array($key, $row['name'], $row['email'], $row['gender'], $row['sessions'], $row['calls'], $row['created'], $row['earliest'], $row['latest'], $dob, $row['zip']));
		}
	}
}