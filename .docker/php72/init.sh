composer install -d /app

cp /app/.docker/php72/conf.d/xdebug.ini /usr/local/etc/php/conf.d

mysqladmin -hmysql -u$MYSQL_USER -p$MYSQL_PASSWORD status
while [ $? -ne "0" ]
do
    echo "Waiting for mysql"
    mysqladmin -hmysql -u$MYSQL_USER -p$MYSQL_PASSWORD status
    sleep 3
done

cd /app && ./bin/console doctrine:migrations:migrate