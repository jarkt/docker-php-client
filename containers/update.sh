#!/bin/sh

docker build -t dockerphpclient ./php/
docker run --rm -v $(cd "$(dirname "$0")/.." || exit; pwd):/project/ dockerphpclient update
