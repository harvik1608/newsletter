<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $fillable = ["Notice_Date","Survey_dt","10d_noticeDt","ProWorks_StDate","AO1_Name","AO2_Name","AO_Name","AOS_1","AOS_2","AOWE","AOmyour","AO_her_their","Aoisare","Aohashave","AO_ComAdd","AO_PropAdd","AO_Phone","AO_Email","BO1_Name","BO2_Name","BO_Name","BOS_1","BOS_2","BOWE","BOmyour","BO_her_their","BOisare","BOhashave","BO_ComAdd","BO_PropAdd","BO_Phone","BO_Email","AO_Surv_Name","AO_Surv_Add","BO_Surv_Name","BO_Surv_Add","PropWorks","Third_SurvName","Third_SurvAdd","PW_Sections","is_active","created_by","updated_by","deleted_by","created_at","updated_at","deleted_at"];

    public function generatedLetters()
    {
        return $this->hasMany(Generated_letter::class, 'client_id');
    }
}
