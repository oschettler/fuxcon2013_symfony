#!/bin/bash

cat <<EOS
Welcome to the installation of the Symfony version of the project site for the FUxCon 2013 workshop.

This software and documentation is released under the GNU General Public License 
(version 3). Please review the license in LICENSE.txt.

This script will download and setup some dependencies and the database. 
It assumes that you have created a MySQL database "fuxcon2013_symfony" 
and a database user "fuxcon" with password "fuxcon" for it. 

If you still need to do this, please press ^C now 
and restart the installation once you are ready.

EOS

read -p "Ready to proceed? [y]/n " reply
if [ "x$reply" != "x" -a "x$reply" != "xy" ]
then
  echo "Please type \"y\" or simply press ENTER to proceed with the installation"
  exit
fi

echo "Loading external dependencies ..."
curl -s http://getcomposer.org/installer | php
php composer.phar install

echo "Loading database ..."
mysql -ufuxcon -pfuxcon fuxcon2013_cakephp < fuxcon2013_symfony.sql

echo "Setting permissions (requires root access) ..."
sys=`uname -s`
if [ $sys = Darvin ]
then
  rm -rf app/cache/*
  rm -rf app/logs/*

  sudo chmod +a "www allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs
  sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs
else
  sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
  sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs
fi
echo "Done."
