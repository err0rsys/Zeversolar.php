<?php
//Zever.php Created by Bman - https://github.com/B-Mqn/Zeversolar.php
//head over to https://forum.pvoutput.org to discuss it.

// 		-------Manually run command-------
//  	/usr/bin/php /home/pi/zeversolar/zever.php

//		-------Cron Script-------
//		crontab -e
//	*/5 * * * * /usr/bin/php /home/pi/zeversolar/zever.php

// 		-------View Log-------
//	cat /home/pi/zeversolar/logs/zever_19-05-2019.log			//view log
//	tail -f /home/pi/zeversolar/logs/zever-19-05-2019.log			//View log Realtime
//	nano /home/pi/zeversolar/logs/zever-19-05-2019.log			//Edit Log


//-------------------------------------------------------------------------------------------------
// 		-------Configuration Options-------

$dataManagerIP = "192.168.x.xx";							//Inverter IP Address
$pvOutputApiKEY = "pvoutput key";							//PvOutput Api Key
$pvOutputSID = "pvoutput systemid";							//PvOutput System ID
											//add Timezone info		
$country = "Australia";
$capitalCity ="Adelaide";

//--------------------------------------------------------------------------------------------------

// !!!!!!!!!!!!!!!! DONT EDIT BELOW HERE !!!!!!!!!!!!!!!!!!!

//--------------------------------------------------------------------------------------------------
// Define Date & Time
date_default_timezone_set("$country/$capitalCity");
$system_time= time();
$date = date('Ymd', time());
$time = date('H:i', time());

// Inverter & API URL
$pvOutputApiURL = "http://pvoutput.org/service/r2/addstatus.jsp?";
$inverterDataURL = "http://".$dataManagerIP."/home.cgi";


$context = stream_context_create(array('http'=>array('protocol_version'=>'1.1')));


// x Attepmts at connecting to inverter
$attempts = 0;
do {
  $attempts++;
  echo "Attempt $attempts\n";
  $result = file_get_contents($inverterDataURL, false, $context);
  if (!$result) {
    echo "Attempt $attempts has failed. Waiting 10 seconds.\n";
    sleep(10);
  }
} while( !$result && $attempts < 5);


// Expanding data from api call
echo $result;

$lines=explode("\n",$result);
$inverterPowerLive = $lines[10];
$kwh = $lines[11];


//Echo kWh reading before
Echo "\n";
Echo "$kwh \n";
Echo "\n";


//Addzero if needed & echo result
$kwh_parts=explode('.', $kwh);
if (strlen($kwh_parts[1]) == 1) {
	$kwh_parts[1] = '0' . $kwh_parts[1];
	$kwh=join('.',$kwh_parts);
}

Echo "\n";
Echo "$kwh \n";


//Convert kWh into Wh & Echo Result
$inverterEnergyDayTotal = $kwh * 1000;

if ($kwh > 0) {
    $inverterEnergyDayTotal = $kwh * 1000;
} else {
    $inverterEnergyDayTotal =" ";
}

Echo "$inverterEnergyDayTotal \n";
Echo "\n";


// Push to PVOutput
$pvOutputURL = $pvOutputApiURL
                . "key=" .  $pvOutputApiKEY
                . "&sid=" . $pvOutputSID
                . "&d=" .   $date
                . "&t=" .   $time
                . "&v1=" .  $inverterEnergyDayTotal
                . "&v2=" .  $inverterPowerLive;
file_get_contents(trim($pvOutputURL));


//Print Values to Console
Echo "\n";
Echo "d \t $date\n";
Echo "t \t $time\n";
Echo "v1 \t $inverterEnergyDayTotal\n";
Echo "v2 \t $inverterPowerLive\n";
Echo "\n";
Echo "Sending data to PVOutput.org \n";
Echo "$pvOutputURL \n";
Echo "\n";


// Push to log file
// log file Output ------   attempts to contact inverter, Date, Time, V1, V2   -------
// using the FILE_APPEND flag to append the content to the end of the file

$file='zever_'.date('d-m-Y').'.log';

$logData = $attempts
                . "," .  $date
                . "," .  $time
                . "," .  $inverterEnergyDayTotal
                . "," .  $inverterPowerLive;
//file_put_contents($file, ($logData . "\r\n"), FILE_APPEND);
file_put_contents("/home/pi/zeversolar/logs/$file", ($logData . "\r\n"), FILE_APPEND);


Echo "And output log to $file \n";
Echo "\n";



?>
