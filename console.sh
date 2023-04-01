#!/bin/bash
current_dir=$(dirname "$(realpath $0)")
/opt/php81/bin/php $current_dir/bin/console $@
