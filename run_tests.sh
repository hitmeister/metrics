#!/bin/sh
CURRENT_DIR=$( cd "$( dirname "$0" )" && pwd )
phpunit --colors -c ${CURRENT_DIR}/phpunit.xml --verbose
