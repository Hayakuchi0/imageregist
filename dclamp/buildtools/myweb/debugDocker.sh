#!/bin/sh
here=`dirname ${0}`/../../../
here=`cd ${here};pwd`
docker run -v ${here}:/var/www/html -i -d --name myserver_apache2 -p 8001:80 --net buildtools_my_network --privileged -t buildtools_webserver:latest /sbin/init
