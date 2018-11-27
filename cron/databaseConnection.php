<?php
// database
$sql = "set names utf8";

// client
$dbClient = new PDO('mysql:host=justmystyledb.cqsxfkuo6ft5.us-east-1.rds.amazonaws.com;dbname=jms_client', "jmsdbuser", "uitretrreqtu8tr65j8");
$dbClient->exec($sql);

// aurora
$dbAurora = new PDO('mysql:host=jms-aurora-prod.cqsxfkuo6ft5.us-east-1.rds.amazonaws.com:3306;dbname=jmsdbprod', "jmsdbuser", "uitretrreqtu8tr65j8");
$dbAurora->query($sql);

// stats
$dbStat = new PDO('mysql:host=justmystyledb.cqsxfkuo6ft5.us-east-1.rds.amazonaws.com:3306;dbname=jms_stats', "jmsdbuser", "uitretrreqtu8tr65j8");
$dbStat->query($sql);
date_default_timezone_set('UTC');
