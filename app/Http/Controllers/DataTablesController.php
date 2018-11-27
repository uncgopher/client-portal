<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataTablesController extends Controller
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
     * agg_users table
     *
     * @return \Illuminate\Http\Response
     */
    public function agg_users()
    {
        $results = array();
		$users = DB::table('agg_users')->get();
		foreach($users as $user){
			$results[] = array("DT_RowId"=>$user->agg_users_id, "gender"=>($user->agg_users_gender==1?'Female':'Male'), "name"=>htmlentities($user->agg_users_name), 
				"email"=>htmlentities($user->agg_users_email), "sessions"=>$user->agg_users_sessions, "calls"=>$user->agg_users_calls, "created"=>$user->agg_users_create, 
				"latest"=>$user->agg_users_latest, "zip"=>$user->agg_users_zip);
		}
		
		return response()->json(array('data'=>$results));
    }

    /**
     * user comments 
     *
     * @return \Illuminate\Http\Response
     */
    public function agg_comments()
    {
        $results = array();
		$comments = DB::connection("aurora")->select('SELECT c.*,u.user_email,u.user_name FROM user u,contact c WHERE c.user_id = u.user_id AND c.contact_active = 1');
		foreach($comments as $comment){
			$results[] = array("DT_RowId"=>$comment->contact_id, "email"=>($comment->contact_email==""?$comment->user_email:$comment->contact_email), "name"=>htmlentities($comment->user_name), 
				"message"=>htmlentities($comment->contact_msg), "ip"=>$comment->contact_ip, "date"=>$comment->contact_date);
		}
		
		return response()->json(array('data'=>$results));
    }

    /**
     * user store/retailer/brand requests 
     *
     * @return \Illuminate\Http\Response
     */
    public function agg_requests()
    {
        $results = array();
		$items = DB::connection("aurora")->select('SELECT r.*,u.user_email,u.user_name FROM user u,retailer_request r WHERE r.user_id = u.user_id AND 
			r.retailer_request_active = 1 AND r.retailer_request_name NOT IN (SELECT item_retailer_name FROM item_retailer WHERE item_retailer_active = 1)');
		foreach($items as $item){
			$results[] = array("DT_RowId"=>$item->retailer_request_id, "email"=>$item->user_email, "name"=>htmlentities($item->user_name), 
				"retailer"=>htmlentities($item->retailer_request_name), "ip"=>$item->retailer_request_ip, "date"=>$item->retailer_request_date);
		}
		
		return response()->json(array('data'=>$results));
    }

    /**
     * promotional emails
     *
     * @return \Illuminate\Http\Response
     */
    public function emails()
    {
        $results = array();
		$items = DB::connection("aurora")->select('SELECT * FROM promotional_email p,user u,item i WHERE u.user_id = p.user_id AND p.item_id = i.item_id');
		foreach($items as $item){
			$results[] = array("DT_RowId"=>$item->promotional_email_id, "email"=>$item->user_email, "user"=>htmlentities($item->user_name), 
				"item"=>'['.$item->item_id.'] '.htmlentities($item->item_name), "title"=>$item->promotional_email_title, "date"=>$item->promotional_email_date);
		}
		
		return response()->json(array('data'=>$results));
    }

    /**
     * user reports
     *
     * @return \Illuminate\Http\Response
     */
    public function agg_reports()
    {
        $results = array();
		$items = DB::connection("aurora")->select('SELECT r.item_id,r.report_reason_id,COUNT(r.report_item_id) as numReports,rr.report_reason_name,c.category_name,i.* 
			FROM report_item r,report_reason rr,item i, category c 
			WHERE i.category_code = c.category_code AND r.report_reason_id = rr.report_reason_id AND r.item_id = i.item_id AND r.report_item_active = 1 
			GROUP BY r.item_id, r.report_reason_id ORDER BY r.item_id ASC');
		foreach($items as $item){
			$results[] = array("DT_RowId"=>$item->item_id, "item_name"=>htmlentities($item->item_name), "reason"=>$item->report_reason_name, "numReports"=>$item->numReports, 
				"url"=>$item->item_retailer_url, "image"=>$item->item_retailer_id.'_'.$item->item_retailer_item_id.'.jpg', "item_category"=>$item->category_name, 
				"status"=>$item->item_active);
		}
		
		return response()->json(array('data'=>$results));
    }

    /**
     * popular likes 
     *
     * @return \Illuminate\Http\Response
     */
    public function agg_likes()
    {
        $results = array();
		$items = DB::connection("aurora")->select('select i.item_id, item_name, item_retailer_name, item_price, sum(item_match_result) as numTotal, i.item_retailer_url,
				i.item_retailer_id, i.item_retailer_item_id, c.category_name
			from item_match im
			inner join category c on im.category_code = c.category_code 
			inner join user u on u.user_id = im.user_id and user_test_account = 0
			inner join item i on i.item_id = im.item_id and item_active = 1 and i.customer_id = 0
			inner join item_retailer ir on ir.item_retailer_id = i.item_retailer_id
			where item_match_active = 1 and item_match_result = 1 and item_match_date_rated > "'.date('Y-m-d H:i:s', strtotime('-90 days')).'"
			group by item_id, item_name, item_retailer_name, item_price order by numTotal desc limit 100');
		foreach($items as $item){
			$results[] = array("DT_RowId"=>$item->item_id, "item_id"=>$item->item_id, "item_name"=>htmlentities($item->item_name), "retailer"=>$item->item_retailer_name, 
				"likes"=>$item->numTotal, 
				"url"=>$item->item_retailer_url, "image"=>$item->item_retailer_id.'_'.$item->item_retailer_item_id.'.jpg', "price"=>number_format(intval($item->item_price)/100,2), 
				"item_category"=>$item->category_name);
		}
		
		return response()->json(array('data'=>$results));
    }

    /**
     * last 7 days in errors 
     *
     * @return \Illuminate\Http\Response
     */
    public function agg_errors()
    {
        $results = array();
		$items = DB::connection("aurora")->select('SELECT * FROM aa_dev WHERE aa_dev_date > "'.date('Y-m-d H:i:s', strtotime('-7 days')).'" ORDER BY aa_dev_date DESC');
		foreach($items as $item){
			$results[] = array("DT_RowId"=>$item->aa_dev_id, "user_id"=>$item->aa_dev_user_id, "date"=>$item->aa_dev_date, 
				"url"=>htmlentities($item->aa_dev_api_call), "msg"=>htmlentities($item->aa_dev_error_msg));
		}
		
		return response()->json(array('data'=>$results));
    }

    /**
     * link tracking stats 
     *
     * @return \Illuminate\Http\Response
     */
    public function agg_links()
    {
        $results = array();
		$items = DB::select('SELECT * FROM short_link ORDER BY short_link_created DESC');
		foreach($items as $item){
			$results[] = array("DT_RowId"=>$item->short_link_id, "date"=>$item->short_link_created, "name"=>$item->short_link_name, "count"=>number_format($item->short_link_count), 
				"comment"=>$item->short_link_comment);
		}
		
		return response()->json(array('data'=>$results));
    }
}
