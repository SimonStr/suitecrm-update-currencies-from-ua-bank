<?php
//scripts/pre_install.php
if (! defined('sugarEntry') || ! sugarEntry) die('Not A Valid Entry Point');
function pre_uninstall() {
    global $db;
    
    $db->query("DELETE FROM schedulers WHERE id = 'c8f6103f-d6ac-1ee4-f4fd-5d0b88a6df69'");

    $db->query("ALTER TABLE currencies DROP COLUMN percent;");
}