FROM centos
ADD ./init.sh /tmp/init.sh
ADD ./sqlinit.sh /tmp/sqlinit.sh
ADD ./init.sql /tmp/init.sql
ADD ./install.php /tmp/install.php
ADD ./imageregist /tmp/imageregist
ADD ./imgr_config.php /tmp/imageregist/imageregist/imgr_config.php
RUN /tmp/init.sh
VOLUME ["/var/www/html/"]
EXPOSE 8001
