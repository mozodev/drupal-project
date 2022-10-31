#!/usr/bin/env bash
set -ex

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
PROJECT_ROOT=$(dirname $(dirname "$SCRIPT_DIR"))
PROJECT_CODE=$(basename "$PROJECT_ROOT")

VHOST_CONFIG_FILE="$PROJECT_ROOT/config/site-dev/vhost.conf"
if [ -f $VHOST_CONFIG_FILE ]; then
  DOTENV="$PROJECT_ROOT/.env"
  if [ -f "$DOTENV" ]; then
    export $(grep -v '^#' $DOTENV | xargs)
  else
    echo "No environment variables file found for $DRUPAL_ENV"
    exit 1
  fi
  vhost=`envsubst < $VHOST_CONFIG_FILE`
  echo "$vhost" | sudo tee /etc/apache2/sites-available/$PROJECT_CODE.conf
  sudo a2ensite ${PROJECT_CODE}.conf
fi
