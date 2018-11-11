#!/bin/sh
./downDocker.sh
mv ../../install.php myweb
mv ../../imageregist myweb
docker-compose up -d --build
mv myweb/install.php ../../
mv myweb/imageregist ../../
./myweb/debugDocker.sh
docker exec -it myserver_apache2 /bin/bash /tmp/sqlinit.sh
