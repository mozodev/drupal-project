#!/usr/bin/env bash

set -ex

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
PROJECT_ROOT=$(dirname $(dirname "$SCRIPT_DIR"))
PROJECT_CODE=$(basename "$PROJECT_ROOT")

DRUPAL_ENV=${1:-stage}
ENV_VARS="$PROJECT_ROOT/.env.$DRUPAL_ENV"
if [ -f "$ENV_VARS" ]; then
  export $(grep -v '^#' $ENV_VARS | xargs)
else
  echo "No environment variables file found for $DRUPAL_ENV"
  exit 1
fi

DEPLOY_HOST=${DEPLOY_HOST:-}
DEPLOY_USER=${DEPLOY_USER:-ubuntu}
DEPLOY_PROJECT_ROOT=${DEPLOY_PROJECT_ROOT:-/var/www/$PROJECT_CODE}
DEPLOY_SLACK_WEBHOOK_URL=${DEPLOY_SLACK_WEBHOOK_URL:-}
DEPLOY_SLACK_CHANNEL=${DEPLOY_SLACK_CHANNEL:-PROJECT_CODE}
DRUPAL_DB_DRIVER=${DRUPAL_DB_DRIVER:-127.0.0.1}
DRUPAL_DB_DATABASE=${DRUPAL_DB_DATABASE:-mysql}
DRUPAL_DB_HOST=${DRUPAL_DB_HOST:-}
DRUPAL_DB_USERNAME=${DRUPAL_DB_USERNAME:-}
DRUPAL_DB_PASSWORD=${DRUPAL_DB_PASSWORD:-}

if [ -v $DEPLOY_HOST ]; then
  echo "DEPLOY_HOST is not set"
  exit 1
fi

echo '[1/5] Copy required files and directories on remote target.'
ssh $DEPLOY_HOST bash <<EOF
sudo mkdir -p $DEPLOY_PROJECT_ROOT/web/sites/default/files/translations $DEPLOY_PROJECT_ROOT/web/profiles/custom
sudo chown -R $DEPLOY_USER:$DEPLOY_USER $DEPLOY_PROJECT_ROOT
chmod -R 777 $DEPLOY_PROJECT_ROOT/web/sites/default/files
EOF

REQUIRED_FILES="$PROJECT_ROOT/config $PROJECT_ROOT/scripts $PROJECT_ROOT/composer.json"
if [ -f "$ENV_VARS" ]; then
  REQUIRED_FILES="$REQUIRED_FILES $ENV_VARS"
fi
scp -r $REQUIRED_FILES $DEPLOY_HOST:$DEPLOY_PROJECT_ROOT/
PROFILES=$PROJECT_ROOT/web/profiles/custom
if [ -d "$PROFILES" ]; then
  scp -r $PROFILES $DEPLOY_HOST:$DEPLOY_PROJECT_ROOT/web/profiles/
fi

GIT_HOOK="$DEPLOY_PROJECT_ROOT/scripts/project/post-receive.sample"
DRUPAL_SETTINGS="$DEPLOY_PROJECT_ROOT/web/sites/default/default.settings.php"
DRUPAL_SETTINGS_LOCAL="$DEPLOY_PROJECT_ROOT/config/site-dev/settings.local.php"
DEPLOY_DOTENV="$DEPLOY_PROJECT_ROOT/.env.$DRUPAL_ENV"
TPL="$DEPLOY_PROJECT_ROOT/config/site-dev/vhost.conf"

ssh $DEPLOY_HOST bash <<EOF
echo '[2/5] Init bare repo and set config, post-receive hook.'
git init --bare $DEPLOY_PROJECT_ROOT/repo
cd $DEPLOY_PROJECT_ROOT/repo
git config hooks.slack.webhook-url "$DEPLOY_SLACK_WEBHOOK_URL"
git config hooks.slack.username "$PROJECT_CODE Deploy Bot"
git config hooks.slack.channel "$DEPLOY_SLACK_CHANNEL"
if [ -f "$GIT_HOOK" ]; then
  cp $GIT_HOOK $DEPLOY_PROJECT_ROOT/repo/hooks/post-receive
  chmod +x $DEPLOY_PROJECT_ROOT/repo/hooks/post-receive
fi

echo '[3/5] Set environment variables and install drupal dependencies and settings.'
sudo apt-get install -y direnv
cd $DEPLOY_PROJECT_ROOT && echo 'dotenv' >> .envrc && direnv allow
composer install -o
cp $DRUPAL_SETTINGS $DEPLOY_PROJECT_ROOT/web/sites/default/settings.php && composer add-settings-local

echo '[4/5] Create database and give access.'
sudo $DRUPAL_DB_DRIVER -e "DROP DATABASE IF EXISTS $DRUPAL_DB_DATABASE; CREATE DATABASE $DRUPAL_DB_DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
sudo $DRUPAL_DB_DRIVER -e "GRANT ALL ON '$DRUPAL_DB_DATABASE'.* TO '$DRUPAL_DB_USERNAME'@'%' IDENTIFIED BY '$DRUPAL_DB_PASSWORD'; FLUSH PRIVILEGES;"

echo '[5/5] Set apache2 vhost'
echo $DEPLOY_DOTENV
if [ -f "$DEPLOY_DOTENV" ]; then
  mv $DEPLOY_DOTENV ./.env && composer set-env:apache2
else
  echo "No environment variables file found for $DRUPAL_ENV"
fi
echo $TPL
if [ -f "$TPL" ]; then
  $DEPLOY_PROJECT_ROOT/scripts/project/set-vhost.sh
  echo "Apache2 vhost has been enabled."
fi
EOF

echo 'Deploy init completed.'
