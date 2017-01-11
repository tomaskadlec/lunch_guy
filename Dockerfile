FROM php:7-fpm
MAINTAINER Tomáš Kukrál <kukratom@fit.cvut.cz>

ENV destdir /var/www/html/

RUN apt-get -y update && \
  apt-get -y install git && \
  apt-get -y clean


COPY . $destdir
WORKDIR $destdir

RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar --no-interaction install

RUN chown -Rv www-data ${destdir}/var/

VOLUME $destdir
