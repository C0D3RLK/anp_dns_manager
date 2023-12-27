FROM php:7.4-fpm-alpine
WORKDIR /var/www/html

RUN apk update && apk add \
    build-base \
    nano
RUN apk add --no-cache tzdata
ENV TZ=Asia/Kuala_Lumpur
#RUN apk add php7 php7-fpm php7-mysqli php7-opcache php7-gd php7-mysqli php7-zlib php7-curl p>
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apk add --update busybox-suid
RUN docker-php-ext-install pdo_mysql pdo
RUN apk add busybox-suid
RUN apk upgrade --available

RUN apk add busybox-initscripts openrc --no-cache


RUN echo '*/30 * * * *  /usr/local/bin/php /home/www/microservices/ms1.php' >>  /etc/crontabs/root
RUN echo '*/1 * * * *  /usr/local/bin/php /home/www/microservices/ms2.php' >>  /etc/crontabs/root

RUN rc-update add crond default

RUN addgroup -g  2001 -S  www && \
  adduser -u  1001 -S www -G www
#USER www

#RUN touch /home/www/hi
#RUN chmod +x /home/www/hi

COPY  /html /var/www/html
COPY  /microservices /home/www/microservices
COPY  /entrypoint /home/www/entrypoint

RUN chmod +x /home/www/microservices
RUN chmod +x /home/www/entrypoint

RUN chmod +x /home/www/microservices/*
RUN chmod +x /home/www/entrypoint/*
RUN chmod +x /var/www/html/app/*
RUN chmod +x /var/www/html/config/*
RUN chmod +x /var/www/html/css/*
RUN chmod +x /var/www/html/template/*

RUN chmod a+x /home/www/entrypoint/set_services.sh
ENTRYPOINT ["/home/www/entrypoint/set_services.sh"]
