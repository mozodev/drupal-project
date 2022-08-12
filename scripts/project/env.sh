#!/usr/bin/env bash
# https://stackoverflow.com/a/16619261

set -ex

if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
else
    echo 'usage: . ./scripts/env.sh'
    echo '.env not found'
fi
