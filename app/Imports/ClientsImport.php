<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class ClientsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != "Notice_Date" && trim($row[0]) != "") {
            return new Client([
                "Notice_Date" => $row[0],
                "Survey_dt" => $row[1],
                "10d_noticeDt" => $row[2],
                "ProWorks_StDate" => $row[3],
                "AO1_Name" => $row[4],
                "AO2_Name" => $row[5],
                "AO_Name" => $row[6],
                "AOS_1" => $row[7],
                "AOS_2" => $row[8],
                "AOWE" => $row[9],
                "AOmyour" => $row[10],
                "AO_her_their" => $row[11],
                "Aoisare" => $row[12],
                "Aohashave" => $row[13],
                "AO_ComAdd" => $row[14],
                "AO_PropAdd" => $row[15],
                "AO_Phone" => $row[16],
                "AO_Email" => $row[17],
                "BO1_Name" => $row[18],
                "BO2_Name" => $row[19],
                "BO_Name" => $row[20],
                "BOS_1" => $row[21],
                "BOS_2" => $row[22],
                "BOWE" => $row[23],
                "BOmyour" => $row[24],
                "BO_her_their" => $row[25],
                "BOisare" => $row[26],
                "BOhashave" => $row[27],
                "BO_ComAdd" => $row[28],
                "BO_PropAdd" => $row[29],
                "BO_Phone" => $row[30],
                "BO_Email" => $row[31],
                "AO_Surv_Name" => $row[32],
                "AO_Surv_Add" => $row[33],
                "BO_Surv_Name" => $row[34],
                "BO_Surv_Add" => $row[35],
                "PropWorks" => $row[36],
                "Third_SurvName" => $row[37],
                "Third_SurvAdd" => $row[38],
                "PW_Sections" => $row[39],
                "created_by" => Auth::user()->id,
                "created_at" => date("Y-m-d H:i:s")
            ]);
        }
    }
}
