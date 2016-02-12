FROM nginx
MAINTAINER Tomáš Kukrál <kukratom@fit.cvut.cz>

# environment
ENV DIR_CONF /etc/nginx/conf.d/
ENV DIR_APP /var/www/app/

# flush nginx config
RUN rm -v /etc/nginx/conf.d/*

# copy config filex
COPY vhost.conf $DIR_CONF

# edit vhost.conf 
RUN sed -i "s/vhostname/$VHOSTNAME/" $DIR_CONF/vhost.conf
