#!/bin/bash
current_dir=$(dirname "$(realpath $0)")
/opt/php73/bin/php $current_dir/bin/console cache:clear --no-warmup
rm $current_dir/var/cache/prod/* -rf
rm $current_dir/var/cache/dev/* -rf
