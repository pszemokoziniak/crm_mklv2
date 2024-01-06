<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Redirect;

trait StoreActivityLog {

    public function storeActivityLog($action, $link_id, $client_id, $link_action, $changes, $user_id)
    {
            $data = new ActivityLog();
            $data->action = $action;
            $data->link_id = $link_id;
            $data->client_id = $client_id;
            $data->link_action = $link_action;
            $data->changes = $changes;
            $data->user_id = $user_id;
            $data->save();
    }
}

