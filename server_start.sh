#!/bin/sh

slc_port=9888

#dd=$(cd `dirname $0`; pwd) && docker run --name $(date +%Y%m%d%H%M%S) -p ${slc_port}:9501 -v $dd/app_root/:/app_root/ -w /root/ -ti cmptech/cmp_app_server:latest sh start_cmp_server_docker_local.sh

dd=$(cd `dirname $0`; pwd)

#did=$(docker run --name $(date +%Y%m%d%H%M%S) -p ${slc_port}:9501 -v $dd/app_root/:/app_root/ -w /root/ -d cmptech/cmp_app_server:latest sh start_cmp_server_docker_local.sh)
docker run --name $(date +%Y%m%d%H%M%S) -p ${slc_port}:9501 -v $dd/webroot/:/app_root/webroot/ -w /root/ cmptech/cmp_app_server:latest sh start_cmp_server_docker_local.sh
