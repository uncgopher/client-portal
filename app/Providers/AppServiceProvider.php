<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $reports = DB::connection("aurora")->select('SELECT count(report_item_id) as numCount FROM report_item WHERE report_item_active = 1');
			$insert3 = ($reports[0]->numCount > 0 ? $reports[0]->numCount : '');
			
            $comments = DB::connection("aurora")->select('SELECT count(contact_id) as numCount FROM contact WHERE contact_active = 1');
			$insert1 = ($comments[0]->numCount > 0 ? $comments[0]->numCount : '');
			
            $requests = DB::connection("aurora")->select('SELECT count(retailer_request_id) as numCount FROM retailer_request WHERE retailer_request_active = 1');
			$insert2 = ($requests[0]->numCount > 0 ? $requests[0]->numCount : '');
			
            $event->menu->add(                
				[
					'text' => 'User Comments',
					'url'  => 'db/comments',
					'icon' => 'comments',
					'label'       => $insert1,
					'label_color' => 'warning',
				],
				[
					'text' => 'User Requests',
					'url'  => 'db/requests',
					'icon' => 'shopping-bag',
					'label'       => $insert2,
					'label_color' => 'warning',
				],
				[
					'text' => 'User Reports',
					'url'  => 'db/reports',
					'icon' => 'flag',
					'label'       => $insert3,
					'label_color' => 'danger',
				]
            );
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
