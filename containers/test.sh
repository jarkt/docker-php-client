#!/bin/sh

docker network create dockerphpclient

docker run --rm --net dockerphpclient -v /var/run/docker.sock:/var/run/docker.sock         --name dockerremoteapi -p 2375:2375 -d jarkt/docker-remote-api
docker run --rm --net dockerphpclient -v $(cd "$(dirname "$0")/.." || exit; pwd):/project/ --name dockerphpclient                 dockerphpclient
docker kill dockerremoteapi

docker network rm dockerphpclient
