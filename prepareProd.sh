app/console cache:clear --env=prod
app/console assets:install --env=prod --symlink web
app/console assetic:dump --env=prod
app/console fos:js-routing:dump
chmod 777 app/cache* app/logs* -R
