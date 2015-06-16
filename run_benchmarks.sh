#!/bin/sh

CURRENT_DIR=$( cd "$( dirname "$0" )" && pwd )
php ${CURRENT_DIR}/vendor/bin/athletic --path ${CURRENT_DIR}/benchmarks/Metrics --bootstrap ${CURRENT_DIR}/benchmarks/bootstrap.php
