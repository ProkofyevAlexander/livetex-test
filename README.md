# livetex-test

## Start vagrant

```
cd ./vagrant
vagrant up
```

## Initial setup

```
vagrant ssh
mysql -udbuser -p123 < /var/www/livetex-test/install/db.sql
cd /var/www/livetex-test
composer install
bower install
```

## Url

http://192.168.56.103:4000/