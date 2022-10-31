#!/usr/bin/env bash

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
PROJECT_ROOT=$(dirname $(dirname "$SCRIPT_DIR"))

MODULE_PATH="web/modules/custom/$1"
KEYWORD="$2"
OPTIONAL_PATH="$PROJECT_ROOT/$MODULE_PATH/config/optional"

if [ -v $1 ]; then
  echo "Please provide module machine name to copy config yaml files to."
  exit 1
fi

if [ -v $2 ]; then
  echo "Please provide keyword to search filename in config/sync."
  exit 1
fi

[ ! -d "$OPTIONAL_PATH" ] && mkdir -p "$OPTIONAL_PATH"
if [ "$2" = "git" ]; then
  git ls-files --others --exclude-standard config/sync > /tmp/untracked.txt
else
  find config/sync -maxdepth 1 -name "*$2*" -print > /tmp/untracked.txt
fi
cat /tmp/untracked.txt | xargs -I % cp % $OPTIONAL_PATH
