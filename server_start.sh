#!/bin/sh

slc_port=9888

#dd=$(cd `dirname $0`; pwd)
dd=$(pwd)

#did=$(docker run --name $(date +%Y%m%d%H%M%S) -p ${slc_port}:9501 -v $dd/app_root/:/app_root/ -w /root/ -d cmptech/cmp_app_server:latest sh start_cmp_server_docker_local.sh)

#echo docker run --name $(date +%Y%m%d%H%M%S) -p ${slc_port}:9501 -v $dd/webroot/:/app_root/webroot/ -w /root/ cmptech/cmp_app_server sh start_cmp_server_docker_local.sh

#echo docker pull cmptech/cmp_app_server

docker run --name $(date +%Y%m%d%H%M%S) -p ${slc_port}:9501 -v $dd/app_root:/app_root -w /root/ cmptech/cmp_app_server sh start_cmp_server_docker_local.sh

#echo $dd
#docker run --name $(date +%Y%m%d%H%M%S) -p ${slc_port}:9501 -w /root/ cmptech/cmp_app_server sh start_cmp_server_docker_local.sh
