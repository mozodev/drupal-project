#!/usr/bin/env bash
set -ex

grep -v '^#' .env | \
tr '\n' '\0' | \
xargs -0 -L1 -I '$' echo 'SetEnv $' | \
sed 's/=/ /' | sudo tee .env.apache

PHP_VERSION=$(php -v | tac | tail -n 1 | cut -d " " -f 2 | cut -c 1-3)
SVC_FPM="php$PHP_VERSION-fpm"
SVC_HTTPD="apache2"
service_exists() {
  local n=$1
  if [[ $(systemctl list-units --all -t service --full --no-legend "$n.service" | sed 's/^\s*//g' | cut -f1 -d' ') == $n.service ]]; then
    return 0
  else
    return 1
  fi
}
[[ $(service_exists "$SVC_FPM") -eq 0 ]] && sudo service $SVC_FPM reload
[[ $(service_exists "$SVC_HTTPD") -eq 0 ]] && sudo service $SVC_HTTPD reload
