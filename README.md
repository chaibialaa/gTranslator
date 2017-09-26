#gTranslator 

##Requirements##

 * [Composer](https://getcomposer.org) is required for installation
 * [cUrl](https://curl.com) is required for usage

##Installation##

Run the command below to install via Composer

```shell
composer require chaibi/gtranslator "dev-master"
```

Then add this line to your provider in config/app.php :

```shell
Chaibi\gTranslator\ServiceProvider::class,
```

Then another command :

```shell
composer dump-autoload
```

And finally : 
```shell
php artisan translate
```

That's it !


