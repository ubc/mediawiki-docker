#!/bin/bash
echo "runJobs Cron Started."
while true; do
	php /var/www/html/maintenance/runJobs.php
	echo "Waiting for 15 seconds..."
	sleep 15
done