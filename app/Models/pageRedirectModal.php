<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class pageRedirectModal extends Model
{
    use HasFactory;
    protected $table = "page_redirect";

    public function incrementHitCount($id)
    {
        return pageRedirectModal::find($id)->increment("hits");
    }

    public function getRedirectUrlsFromCurrentUrl($currentUrl)
    {
        return pageRedirectModal::where("is_disabled","0")->where("from","=",$currentUrl)->orWhere("from","=","/".$currentUrl)->orderBy('id',"ASC")->get();
    }
    
}
