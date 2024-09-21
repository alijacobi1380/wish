<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

function addnotif($userid, $text, $Rid = null)
{
    DB::table('notifications')->insert([
        'Date' => Carbon::now(),
        'UserID' => $userid,
        'Text' => $text,
        'RID' => $Rid
    ]);
}
