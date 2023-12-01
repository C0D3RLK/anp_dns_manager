#!/bin/sh
#ANP_DNS_MICROSRV Initializer

# start cron
/usr/sbin/crond
#rc-update add crond default
php-fpm
