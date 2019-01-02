<?php
//Zever.php Created by Bman

// 	Manually run command
//  	 /usr/bin/php /home/pi/zeversolar/zever.php

//		Cron Script.... 
//		crontab -e
//		*/5 * * * * /usr/bin/php /home/pi/zeversolar/zever.php

// Configuration Options
$dataManagerIP = "192.168.x.xx";							//Inverter IP Address
$pvOutputApiKEY = "APIKey";                   //PVOutput Api Key found in https://pvoutput.org/account.jsp
$pvOutputSID = "SystemID";                    //Your pvoutput SystemID
//															//add Timezone info here??		
$country = "Australia";
$capitalCity ="Adelaide";			
//Settings that can be Used in V7-V12	
// Inverter					inverterVoltageLive		inverterDCVoltage	inverterHz	inverterACAmps	inverterDCAmps

$v7 = "phase1Volts";
$v8 = "phase2Volts";
$v9 = "phase3Volts";
$v10 = "phase1Amps";
$v11 = "phase2Amps";
$v12 = "phase3Amps";

// !!!!!!!!!!!!!!!! DONT EDIT BELOW HERE !!!!!!!!!!!!!!!!!!!
// Define Date & Time
date_default_timezone_set("$country/$capitalCity");
$system_time= time();
$date = date('Ymd', time());
$time = date('H:i', time());

// Inverter API URL
$pvOutputApiURL = "http://pvoutput.org/service/r2/addstatus.jsp?";
$inverterDataURL = "http://".$dataManagerIP."/home.cgi";


//Collect Data From Inverter
    $context = stream_context_create(array('http'=>array('protocol_version'=>'1.1')));
    $result = file_get_contents('$dataManagerIP/home.cgi', false, $context);
    //echo $result;

$lines=explode("\n",$result);
$inverterPowerLive = $lines[10];
$kwh = $lines[11];


//Fix for Zeversolar kWh reading
$kWh_parts=explode('.', $kWh);
if (strlen($kWh_parts[1]) == 1) {
	$kWh_parts[1] = '0' . $kWh_parts[1];
	$kWh=join('.',$kWh_parts);
}
echo $kWh;

//Convert to Watt for PVOutput
$inverterEnergyDayTotal = $kwh * 1000;

// Push to PVOutput
$pvOutputURL = $pvOutputApiURL
                . "key=" .  $pvOutputApiKEY
                . "&sid=" . $pvOutputSID
                . "&d=" .   $date
                . "&t=" .   $time
                . "&v1=" .  $inverterEnergyDayTotal
                . "&v2=" .  $inverterPowerLive
                . "&v6=" .  $inverterVoltageLive
                . "&v7=" .  $$v7
                . "&v8=" .  $$v8
                . "&v9=" .  $$v9
                . "&v10=" . $$v10
                . "&v11=" . $$v11
                . "&v12=" . $$v12;
file_get_contents(trim($pvOutputURL));													
//Print Values to Console
Echo "\n";
Echo "d \t $date\n";
Echo "t \t $time\n";
Echo "v1 \t $inverterEnergyDayTotal\n";
Echo "v2 \t $inverterPowerLive\n";
Echo "v6 \t $inverterVoltageLive\n";
Echo "v7 \t ${$v7}\n";
Echo "v8 \t ${$v8}\n";
Echo "v9 \t ${$v9}\n";
Echo "v10 \t ${$v10}\n";
Echo "v11 \t ${$v11}\n";
Print "v12 \t ${$v12}\n";
Echo "\n";
Echo "Sending data to PVOutput.org \n";
Echo "$pvOutputURL \n";
Echo "\n";

?>

