<?php
chdir(dirname(__FILE__) . '/../');
include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");
set_time_limit(0);
// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
$ctl = new control_modules();
include_once(DIR_MODULES . 'narodmon3/narodmon3.class.php');
$narodmon3_module = new narodmon3();
$narodmon3_module->getConfig();
// In data
$tmp1 = SQLSelectOne("SELECT ID FROM nm_outdata LIMIT 1");
// Out data
$tmp2 = SQLSelectOne("SELECT ID FROM nm_indata LIMIT 1");
if ((!$tmp1['ID']) && (!$tmp2['ID']))
   exit; // no devices added -- no need to run this cycle
 
echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;
$latest_check=0;
$checkEvery=5; // poll every 5 seconds
while (1)
{
   if ((time()-$latest_check)>$checkEvery) {
    setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
    $latest_check=time();
    //echo date('Y-m-d H:i:s').' Polling devices...\n';
    $narodmon3_module->processCycle();
   }
   if (file_exists('./reboot') || IsSet($_GET['onetime'])){
      $db->Disconnect();
      exit;
   }
   sleep(1);
}
DebMes("Unexpected close of cycle: " . basename(__FILE__));
