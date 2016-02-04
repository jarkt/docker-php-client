FROM ubuntu:14.04

RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv E5267A6C
RUN echo 'deb http://ppa.launchpad.net/ondrej/php5-5.6/ubuntu trusty main\n\
deb-src http://ppa.launchpad.net/ondrej/php5-5.6/ubuntu trusty main' | tee /etc/apt/sources.list.d/ondrej-php5-5.6.list

RUN apt-get -yq update && apt-get install -yq \
  php5-common \
  php5-cli \
  php5-curl

WORKDIR /project

ENTRYPOINT ["/project/containers/php/entrypoint.sh"]
