<?php

require_once('include/MVC/Controller/SugarController.php');

class CurrenciesController extends SugarController {

    function action_update(){

        global $db, $sugar_config;

        $currencies = [];
        $res = $db->query("SELECT id, iso4217, percent, date_modified FROM currencies WHERE deleted = '0' AND status = 'Active'");

        while ( $currency_iso = $db->fetchByAssoc($res)) {
            $currencies[] = $currency_iso;
        }

        if (!empty($currencies)) {

            $today = date("Y-m-d H:i:s");
            $result = file_get_contents('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');
            $conversion_data = json_decode($result);

            if ( $sugar_config["default_currency_iso4217"] == "UAH") {

                foreach ($currencies as $key => $currency) {
                    $currency_id = $currency["id"];
                    foreach ($conversion_data as $conversion_datum) {
                        if ($currency["iso4217"] == $conversion_datum->cc) {
                            $rate = "";
                            if (isset($currency["percent"])) {
                                $rate = $conversion_datum->rate+intval($currency["percent"])/100;
                            } else {
                                $rate = $conversion_datum->rate;
                            }
                            $currencies[$key]["rate"] = $rate;

                            $db->query("UPDATE currencies SET conversion_rate = '{$rate}', date_modified = '{$today}' WHERE id = '{$currency_id}'");
                        }
                    }
                }

            } else {
                $check_UAH = $db->fetchByAssoc($db->query("SELECT id FROM currencies WHERE deleted = '0' AND status = 'Active' AND iso4217 = 'UAH'"));

                if (!$check_UAH) {
                    file_put_contents('suitecrm.log', $today." - The UAH is not in the system as a currency\n\r", FILE_APPEND | LOCK_EX);
                    die();
                }

                $currency_iso4217 = $sugar_config["default_currency_iso4217"];

                $currency_converce = json_decode(file_get_contents('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?valcode='.$currency_iso4217.'&date='.date("Ymd").'&json'));
                $currency_converce = $currency_converce[0]->rate;

                $bank_isoes = [];
                foreach ($conversion_data as $datum) {
                    $bank_isoes[] = $datum->cc;
                }

                foreach ($currencies as $key => $currency) {
                    $currency_id = $currency["id"];
                    if (in_array($currency["iso4217"], $bank_isoes)) {
                        foreach ($conversion_data as $conversion_datum) {
                            if ($currency["iso4217"] == $conversion_datum->cc) {

                                $rate = ($currency_converce*1)/$conversion_datum->rate;
                                $currencies[$key]["rate"] = $rate;

                                $db->query("UPDATE currencies SET conversion_rate = '{$rate}', date_modified = '{$today}' WHERE id = '{$currency_id}'");
                            }
                        }
                    } else if ($currency["iso4217"] == "UAH") {
                        $rate = 1/$currency_converce;
                        $currencies[$key]["rate"] = $rate;
                        $db->query("UPDATE currencies SET conversion_rate = '{$rate}', date_modified = '{$today}' WHERE id = '{$currency_id}'");
                    } else {
                        $query_id = create_guid();

                        $db->query("INSERT INTO job_queue (assigned_user_id, id, name, deleted, date_entered, date_modified, scheduler_id, execute_time, status, resolution ) VALUES (1, '{$query_id}', 'Update currencies conversion', '0', '{$today}', '{$today}', 'c8f6103f-d6ac-1ee4-f4fd-5d0b88a6df69', '{$today}', 'error', 'failed')");
                        file_put_contents('suitecrm.log', $today." - ISO-4217 code ".$currency["iso4217"]." of added currency not found in the National Bank api\n\r", FILE_APPEND | LOCK_EX);
                        die();
                    }

                }

            }
        }

        die();
    }

    function action_getTime()
    {
        global $db, $mod_strings;
        $currency_id = $_POST["id"];
        $date = $db->getOne("SELECT date_modified FROM currencies WHERE id = '{$currency_id}'");

        echo "<span style='margin-left: 5px;' id='last_up_date'>".$mod_strings['LBL_UPDATED'].$date."</span>";
        exit;
    }
}