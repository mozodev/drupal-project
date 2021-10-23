#!/usr/bin/env bash

curl -s https://api.github.com/repos/gohugoio/hugo/releases/latest \
| grep "browser_download_url.*hugo_extended.*_Linux-64bit\.tar\.gz" \
| cut -d ":" -f 2,3 \
| tr -d \" | xargs curl -sSL | tar xz

chmod +x hugo && sudo mv hugo /usr/local/bin/
echo "Hugo binary location: $(which hugo)"
echo "Hugo binary version: $(hugo version)"
