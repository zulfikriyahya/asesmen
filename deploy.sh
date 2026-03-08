sudo chown $USER:$USER -R /var/www/asesmen
git stash
git pull origin v2.0
sudo chown www-data:www-data -R /var/www/asesmen
