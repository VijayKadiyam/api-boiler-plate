#!/bin/bash
cd ~/public_html/rms-api/

chmod -R 777 storage public vendor bootstrap database .git

cd ~/public_html/

chmod -R 755 rms-api