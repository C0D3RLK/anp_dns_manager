#!/bin/sh
#ANP_DNS_MANAGER Installer script
#v1.0.0

#Functions
deploy_app(){
  clear
  echo "Starting ANP DNS Manager installation"
  docker compose up -d --build
  docker ps
  echo "---------------------"
  echo "Script done running"
  echo ""
  echo "Browse your server ip with URL http://<server_ip>:8888"
  echo ""
}

set_files_permission(){
  chmod 777 entrypoint/
  chmod 777 html/
  chmod 777 microservices/
  chmod 777 nginx/
  cd html
  chmod 777 *
  chmod 777 app/*
  chmod 777 config/*
  chmod 777 css/*
  chmod 777 template/*
  cd ..
  chmod 777 entrypoint/*
  chmod 777 microservices/*
  chmod 777 nginx/*
  echo "File permission set complete"
  deploy_app
}

check_privileges(){
  if  [ "$(/usr/bin/id -u)" != "0" ];
  then
    echo "Must run as sudoer / root"
    exit 1

  else
    set_files_permission
  fi
}

#installer Be ning ging
clear
echo 'Starting installation'
echo "--------------------"
check_privileges
