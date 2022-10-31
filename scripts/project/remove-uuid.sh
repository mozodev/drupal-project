#!/usr/bin/env bash

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
PROJECT_ROOT=$(dirname $(dirname "$SCRIPT_DIR"))

MODULE_PATH="web/modules/custom/$1"
INSTALL_PATH="$PROJECT_ROOT/$MODULE_PATH/config/install"
OPTIONAL_PATH="$PROJECT_ROOT/$MODULE_PATH/config/optional"

if [ -v $1 ]; then
  echo "Please provide module machine name to remove uuid from config yaml files."
  exit 1
fi

if [ -d "$INSTALL_PATH" ]; then
  find $INSTALL_PATH -type f -exec sed -i -e '/^uuid: /d' {} \;
  find $INSTALL_PATH -type f -exec sed -i -e '/_core:/,+1d' {} \;
fi

if [ -d "$OPTIONAL_PATH" ]; then
  find $OPTIONAL_PATH -type f -exec sed -i -e '/^uuid: /d' {} \;
  find $OPTIONAL_PATH -type f -exec sed -i -e '/_core:/,+1d' {} \;
fi
