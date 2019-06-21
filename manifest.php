<?PHP

$manifest = array( 
	'name' => 'Update Currencies',
	'is_uninstallable' => 'No',
	'type' => 'module',
	'acceptable_sugar_versions' =>
		  array (),
	'acceptable_sugar_flavors' =>
		  array('CE'),
	'author' => 'Simonchuk Artem',
	'version' => '1.0',
	'published_date' => '2019-06-23',
      );
$installdefs = array(
	'id'=> 'curr_uppdate',
	'copy' => array(
		array('from'=> '<basepath>/custom/modules/Currencies/','to'=>'custom/modules/Currencies/'),
		array('from'=> '<basepath>/custom/themes/SuiteP/','to'=>'custom/themes/SuiteP/'),
   )
 );

?>