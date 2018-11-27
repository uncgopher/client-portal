<?php

namespace App\Http\Controllers;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * category list
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        $results = array();
		$items = DB::connection("aurora")->select('SELECT * FROM category WHERE category_active = 1 AND category_final = 1 ORDER BY category_code ASC');
		foreach($items as $item){
			$results[] = array('id'=>$item->category_code, 'text'=>'['.$item->category_code.'] '.$item->category_name);
		}
		
		return response()->json($results);
    }

    /**
     * refresh the item from shopstyle
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh_item(Request $request)
    {
        // make sure item exists and isn't Amazon
		$item = DB::connection("aurora")->select('SELECT * FROM item WHERE item_id = ? AND item_retailer_id > 1 LIMIT 1', [$request->item_id]);
		if(count($item) == 1){
			// refresh
			$item_retailer_item_id = $item[0]->item_retailer_item_id;	
			$item_id = $item[0]->item_id;
			$machine = 'machine_'.$item[0]->category_code;
			
			// build URL 
			$client = new Client();
			$response = $client->get("http://api.shopstyle.com/api/v2/products/".$item_retailer_item_id."?pid=uid900-39039030-95");
			$itemPage = json_decode($response->getBody(), true);
							
			// if not in stock, set as inactive
			if($itemPage['inStock'] == false){
				// delete item from machine learning
				DB::connection("aurora")->update('UPDATE item SET item_active = 0, item_date_deactivated = "'.date('Y-m-d H:i:s').'" WHERE item_id = ? LIMIT 1', [$item_id]);
				DB::connection("aurora")->delete("DELETE FROM $machine WHERE item_id1 = $item_id OR item_id2 = $item_id");
			}else{
				// check for sale, update price
				if(isset($itemPage['salePrice']) AND $itemPage['salePrice'] > 0){
					// on sale
					DB::connection("aurora")->update('UPDATE item SET item_size_count = ?, item_price = ?, item_sale = 1, item_date_updated = "'.date('Y-m-d H:i:s').'" WHERE item_id = ? LIMIT 1', 
						[count($itemPage['sizes']), intval($itemPage['salePrice']*100), $item_id]);
				}else{
					// not on sale 
					DB::connection("aurora")->update('UPDATE item SET item_size_count = ?, item_price = ?, item_sale = 0, item_date_updated = "'.date('Y-m-d H:i:s').'" WHERE item_id = ? LIMIT 1', 
						[count($itemPage['sizes']), intval($itemPage['price']*100), $item_id]);
				}
			}
		}		
		return redirect()->route('emails');
    }

    /**
     * change_category
     *
     * @return \Illuminate\Http\Response
     */
    public function change_category(Request $request)
    {
        // make sure item has been reported
		$item = DB::connection("aurora")->select('SELECT * FROM item WHERE item_id = ? AND 
			item_id IN (SELECT item_id FROM report_item WHERE report_item_active = 1) LIMIT 1', [$request->item_id]);
		if(count($item) == 1){				
			// remove it from the old matches table 
			DB::connection("aurora")->delete('DELETE FROM machine_'.$item[0]->category_code.' WHERE item_id1 = ? OR item_id2 = ?', [$request->item_id, $request->item_id]);
			
			// change an item's category and set it to get run through the matching again
			DB::connection("aurora")->update('UPDATE item SET category_code = ?, item_machine_learned = 0 WHERE item_id = ? AND 
				item_id IN (SELECT item_id FROM report_item WHERE report_item_active = 1) LIMIT 1', [$request->category_code, $request->item_id]);
		}		
		return redirect()->route('user_reports');
    }

    /**
     * valid report 
     *
     * @return \Illuminate\Http\Response
     */
    public function valid_report($item_id)
    {
		// make sure item has been reported
		$item = DB::connection("aurora")->select('SELECT * FROM item WHERE item_id = ? AND 
			item_id IN (SELECT item_id FROM report_item WHERE report_item_active = 1) LIMIT 1', [$item_id]);
		if(count($item) == 1){				
			// remove it from the matches table 
			DB::connection("aurora")->delete('DELETE FROM machine_'.$item[0]->category_code.' WHERE item_id1 = ? OR item_id2 = ?', [$item_id, $item_id]);
			
			// verify the item is set to inactive and update the report table 
			DB::connection("aurora")->update('UPDATE item SET item_active = 0 WHERE item_id = ? LIMIT 1', [$item_id]);
			DB::connection("aurora")->update('UPDATE report_item SET report_item_active = 2 WHERE item_id = ?', [$item_id]);
		}		
		return redirect()->route('user_reports');
    }

    /**
     * general user stats 
     *
     * @return \Illuminate\Http\Response
     */
    public function stats()
    {
		// new users in last 7 days 
		$item = DB::select('SELECT count(*) as numTotal FROM agg_users WHERE agg_users_create >= ?', [date('Y-m-d', strtotime('-7 days'))]);
		$new7 = $item[0]->numTotal;
		
		// active users in last 7 days 
		$item = DB::select('SELECT count(*) as numTotal FROM agg_users WHERE agg_users_latest >= ?', [date('Y-m-d', strtotime('-7 days'))]);
		$active7 = $item[0]->numTotal;
		
		// new users today
		$item = DB::select('SELECT count(*) as numTotal FROM agg_users WHERE agg_users_create >= ?', [date('Y-m-d')]);
		$new = $item[0]->numTotal;
		
		// active users today
		$item = DB::select('SELECT count(*) as numTotal FROM agg_users WHERE agg_users_latest >= ?', [date('Y-m-d')]);
		$active = $item[0]->numTotal;
		
		// yesterday's DAU/MAU
		$item = DB::select('SELECT count(DISTINCT user_id) as numTotal FROM agg_sessions WHERE agg_sessions_date >= ? AND agg_sessions_date < ?', 
			[date('Y-m-d', strtotime('-1 day')), date('Y-m-d')]);
		$dau = $item[0]->numTotal;
		$item = DB::select('SELECT count(DISTINCT user_id) as numTotal FROM agg_sessions WHERE agg_sessions_date >= ? AND agg_sessions_date < ?', 
			[date('Y-m-d', strtotime('-30 day')), date('Y-m-d')]);
		$mau = $item[0]->numTotal;
		$sticky = round(($dau/$mau)*100, 1);
		
		// URL short links 
		$item = DB::select('SELECT * FROM short_link WHERE short_link_id = 1');
		$short1 = number_format($item[0]->short_link_count);
		$item = DB::select('SELECT * FROM short_link WHERE short_link_id = 2');
		$short2 = number_format($item[0]->short_link_count);
		
		return response()->json(array("new"=>number_format($new), "active"=>number_format($active), "new7"=>number_format($new7), "active7"=>number_format($active7),
			"sticky"=>number_format($sticky, 1), "dau"=>number_format($dau), "mau"=>number_format($mau), "short1"=>$short1, "short2"=>$short2));
    }

    /**
     * api activity (matches and active users)
     *
     * @return \Illuminate\Http\Response
     */
    public function activity_api()
    {
		// users & matches in last 14 days 
		$items = DB::select('SELECT count(DISTINCT user_id) as numUsers,sum(agg_sessions_count) as numMatches,agg_sessions_date FROM agg_sessions 
			WHERE agg_sessions_date >= ? GROUP BY agg_sessions_date ORDER BY agg_sessions_date ASC', [date('Y-m-d', strtotime('-14 days'))]);
		$matches = array();$users = array();$dataDate = array();
		foreach($items as $item){
			$matches[] = intval($item->numMatches);
			$users[] = intval($item->numUsers);
			$dataDate[] = "'".date('M-j', strtotime($item->agg_sessions_date))."'";
		}
		
		return response()->json(array("matches"=>$matches, "users"=>$users, "dataDate"=>$dataDate));
    }

    /**
     * users that liked the selected item_id
     *
     * @return \Illuminate\Http\Response
     */
    public function matched_users($item_id)
    {
		// users with the match
		$items = DB::connection("aurora")->select('SELECT u.user_email,u.user_name FROM user u,item_match m WHERE u.user_id = m.user_id AND m.item_match_active = 1 AND u.user_test_account = 0 AND 
			m.item_match_result = 1 AND u.user_notifications_email = 1 AND (u.user_promotional_email < "'.date('Y-m-d H:i:s', strtotime('-5 days')).'" or u.user_promotional_email IS NULL) 
			AND m.item_id = ? AND CONCAT(u.user_id,"_",m.item_id) NOT IN (SELECT CONCAT(user_id,"_",item_id) FROM promotional_email) AND u.customer_id = 0', [$item_id]);
		$matches = "<table class='table' width='100%'>";
		foreach($items as $item){
			$matches .= "<tr><td>".$item->user_name."</td><td>".$item->user_email."</td></tr>";
		}
		$matches .= "</table>";
		
		return response()->json(array("matches"=>$matches));
    }

    /**
     * users that liked the selected item_id
     *
     * @return \Illuminate\Http\Response
     */
    public function email_matched_users(Request $request)
    {
		// values cannot be empty
		if(!isset($request->item_id) OR $request->item_id == '' OR 
			!isset($request->email_title) OR $request->email_title == '' OR 
			!isset($request->email_body) OR $request->email_body == ''){
				print "MISSING PARAMETER";
				exit();
			}
		
		// send users with the match an email
		require '/var/www/portal/resources/email/mail.php';		
		if($request->test){
			$items = DB::connection("aurora")->select('SELECT * FROM user u,item m WHERE u.user_id IN (2) AND m.item_id = ?', [$request->item_id]);
		}else{
			$items = DB::connection("aurora")->select('SELECT * FROM user u,item_match m,item i WHERE u.user_id = m.user_id AND m.item_match_active = 1 AND i.item_id = m.item_id AND 
				u.user_test_account = 0 AND m.item_match_result = 1 AND u.user_notifications_email = 1 AND u.customer_id = 0 AND 
				(u.user_promotional_email < "'.date('Y-m-d H:i:s', strtotime('-5 days')).'" or u.user_promotional_email IS NULL) 
				AND m.item_id = ? AND CONCAT(u.user_id,"_",m.item_id) NOT IN (SELECT CONCAT(user_id,"_",item_id) FROM promotional_email)', [$request->item_id]);
		}
		foreach($items as $item){			
			// update user's promotion email date 
			DB::connection("aurora")->update('UPDATE user SET user_promotional_email = "'.date('Y-m-d H:i:s').'" WHERE user_id = ? LIMIT 1', [$item->user_id]);
			DB::connection("aurora")->insert('INSERT INTO promotional_email (item_id, user_id, promotional_email_title) VALUES (?, ?, ?)', [$request->item_id, $item->user_id, $request->email_title]);
			$promotion_email_id = DB::connection("aurora")->getPdo()->lastInsertId();
			
			// send email
			$phpmail->clearAllRecipients();
			$phpmail->addAddress($item->user_email);
			$phpmail->Subject = $request->email_title;
			$phpmail->Body    = "
			<!DOCTYPE html>
			<html lang='en'>
			<head>
				<title>Just My Style Giveaway!</title>
				<link href='https://www.justmystyleapp.com/bootstrap.min.css' rel='stylesheet'>
				<link href='https://www.justmystyleapp.com/stylish-portfolio.css' rel='stylesheet'>
				<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
				<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
				<style>.smicon{height:30px;width:30px;}.list-inline{padding-left: 0;margin-left: -5px;list-style: none;}.list-inline>li{display: inline-block;padding-right: 5px;padding-left: 5px;}</style>
			</head>
			<body>
				<header id='top' class='header' style='text-align:center;'>
					<img src='https://www.justmystyleapp.com/jms.png' width='100%' height='auto'>
				</header>
				<div class='container'>
					<div class='row'>
						<div class='col-lg-10 col-lg-offset-1'>
							<p style='font-size:16px;padding:20px 0;'>
								Hey ".$item->user_name.",
								<br/><br/>
								".str_replace("\n", "<br>", $request->email_body)."
								<br/><br/>
								<a href='https://www.justmystyleapp.com/item/".$request->item_id.'/'.$item->user_id.'/'."'>
									<img src='https://resources.justmystyleapp.com/img/".$item->item_retailer_id."_".$item->item_retailer_item_id.".jpg' style='max-height:300px;'>
								</a>
								<br/><br/>
								Sincerely,<br/><br/>
								<img src='https://www.justmystyleapp.com/signature2.png'>
								<br/><br/>
								Rebecca Hilton<br/>
								Founder, Just My Style<br/>
								<a href='https://www.justmystyleapp.com/'>https://www.JustMyStyleApp.com</a>
								<ul class='list-inline'>
									<li>
										<a href='https://www.instagram.com/justmystyle_app/' target='_blank'><img src='https://www.justmystyleapp.com/assets/img/instagram.png' class='smicon' title='Instagram'></a>
									</li>
									<li>
										<a href='https://twitter.com/JustMyStyle_app' target='_blank'><img src='https://www.justmystyleapp.com/assets/img/twitter.png' class='smicon' title='Twitter'></a>
									</li>
									<li>
										<a href='https://www.facebook.com/JustMyStyleApp/' target='_blank'><img src='https://www.justmystyleapp.com/assets/img/facebook.png' class='smicon' title='Facebook'></a>
									</li>
								</ul>
							</p>
						</div>
					</div>
					<div class='row text-center'>
						<br/><br/><a href='https://www.justmystyleapp.com/unsubscribe?u=".$item->user_id."&i=".str_replace('$2y$10$', '', $item->user_password)."'>Unsubscribe</a> 
						<img src='https://www.justmystyleapp.com/images/$promotion_email_id/mini.gif'>
					</div>
				</div>
			</body>
			</html>
			";
			$phpmail->send();
		}
		
		return redirect()->route('emails');
    }
}
