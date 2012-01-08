<?php

/*
 * @andyjiang
 * 
 * this function will get the reference date ($data_ref) and
 * return the time difference from now ($current_time)
 * 
 */

function dateTimeDiff($data_ref)
{
    // Get the current date
    $current_date = date('Y-m-d H:i:s');

    // Extract from $current_date
    $current_year = substr($current_date,0,4);
    $current_month = substr($current_date,5,2);
    $current_day = substr($current_date,8,2);

    // Extract from $data_ref
    $ref_year = substr($data_ref,0,4);
    $ref_month = substr($data_ref,5,2);
    $ref_day = substr($data_ref,8,2);

    // create a string yyyymmdd 20071021
    $tempMaxDate = $current_year . $current_month . $current_day;
    $tempDataRef = $ref_year . $ref_month . $ref_day;

    $tempDifference = $tempMaxDate-$tempDataRef;

    // If the difference is less than 7 days
    if($tempDifference < 7) {

        // Extract $current_date H:m:ss
        $current_day = substr($current_date, 8, 2);
        $current_hour = substr($current_date,11,2);
        $current_min = substr($current_date,14,2);
        $current_seconds = substr($current_date,17,2);

        // Extract $data_ref Date H:m:ss
        $ref_day = substr($data_ref, 8, 2);
        $ref_hour = substr($data_ref,11,2);
        $ref_min = substr($data_ref,14,2);
        $ref_seconds = substr($data_ref,17,2);

        $dDf = $current_day-$ref_day;
        $hDf = $current_hour-$ref_hour;
        $mDf = $current_min-$ref_min;
        $sDf = $current_seconds-$ref_seconds;

        // Show time difference ex: 2 min 54 sec ago.
        if($dDf<1) {
            if($hDf>0) {
                if($mDf<0) {
                    $mDf = 60 + $mDf;
                    $hDf = $hDf - 1;
                    if ($hDf == 0){
                        $dateOfRecord = $mDf . ' min ago';
                    } else {
                        $dateOfRecord = $hDf . ' hr ago'; // . $mDf . ' min ago';
                    }
                } else {
                    $dateOfRecord = $hDf. ' hr ago'; // . $mDf . ' min ago';
                }
            } else {
                if($mDf>0){
                    if ($sDf < 0) {
                        $mDf = $mDf - 1;
                        $sDf = $sDf + 60;
                        if ($mDf == 0) {
                            $dateOfRecord = $sDf . ' sec ago';
                        } else {
                            $dateOfRecord = $mDf . ' min ago';
                        }
                    } else {
                        $dateOfRecord = $mDf . ' min ago';
                    }
                } else {
                    $dateOfRecord = $sDf . ' sec ago';
                }
            }
        } else {
            if ($dDf > 1) {
                $dateOfRecord = $dDf . ' days ago';
            } else {
                // if dDf == 1
                if ($hDf < 0) {
                    $hDf = $hDf + 24;
                    $dDf = $dDf - 1;
                    if ($dDf == 0) {
                        $dateOfRecord = $hDf . ' hr ago';
                    } else {
                        $dateOfRecord = $dDf . ' day ago';
                    }
                } else {
                    $dateOfRecord = $dDf . ' day ago'; // and ' . $hDf . ' hr ago';
                }
            }
        }
    } else {
        $dateOfRecord = "";
    }
    
    return $dateOfRecord;
}

?>
