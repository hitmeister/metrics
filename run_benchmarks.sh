#!/bin/sh
#sudo launchctl limit maxfiles 400000 unlimited
CURRENT_DIR=$( cd "$( dirname "$0" )" && pwd )
php ${CURRENT_DIR}/vendor/bin/athletic -p ${CURRENT_DIR}/src/Benchmarks/ -b ${CURRENT_DIR}/vendor/autoload.php
