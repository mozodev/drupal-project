#!/usr/bin/env bash
set -ex

grep -v '^#' .env | \
tr '\\n' '\\0' | \
xargs -0 -L1 -I '$' echo 'SetEnv $' | \
sed 's/=/ /' | sudo tee /etc/apache2/conf-available/env-vars.conf

sudo a2enconf env-vars
sudo service apache2 reload
