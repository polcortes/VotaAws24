#!/bin/bash

# directori del script run.sh
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# entrem a la carpeta del codi font
cd $SCRIPT_DIR

# engeguem el PHP server
php -S 0.0.0.0:8080