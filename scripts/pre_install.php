<?php
//scripts/pre_install.php
if (! defined('sugarEntry') || ! sugarEntry) die('Not A Valid Entry Point');
function pre_install() {
    global $sugar_config;
    global $db;
    
    $today = date("Y-m-d H:i:s");
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/index.php?module=Currencies&action=update";
    $query = "INSERT INTO schedulers (id, deleted, date_entered, date_modified, created_by, modified_user_id, name, job, date_time_start, job_interval, status, catch_up) VALUES ('c8f6103f-d6ac-1ee4-f4fd-5d0b88a6df69', '0', '{$today}', '{$today}', '1', '1', 'Update currencies conversion', '{$actual_link}', '{$today}', '0::0::*::*::*', 'Active', '1')";
    $db->query($query);

    $db->query("ALTER TABLE currencies ADD percent varchar(50) NULL;");
}