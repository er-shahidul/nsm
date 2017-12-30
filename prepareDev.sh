app/console cache:clear
app/console assets:install --symlink web
app/console fos:js-routing:dump
chmod 777 app/cache* app/logs* -R
