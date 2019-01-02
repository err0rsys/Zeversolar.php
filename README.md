# Zeversolar.php
PVOutput PHP uploader script for Zever Solar Inverters to send data every 5 minutes to PVOutput

Its work in progress but having a few missed uploads every so often

i run as a Cron task in 5 min intervals on my Pi as below.

crontab -e

*/5 * * * * /usr/bin/php /home/pi/froniusUltimate.php
