#gTranslator 

##Installation

Run the command below to install via Composer

```shell
composer require chaibi/gtranslator
```

Then add this line to your providers in config/app.php :

```shell
Chaibi\gTranslator\ServiceProvider::class,
```

And finally : 
```shell
php artisan translate
```

You will be prompted to choose the source language (Default: app.locale config value) then you will
be prompted again to enter the destination language (Default: app.fallback_locale config value), and that's it !

gTranslator will create a new folder with the destination language, and copy the source files to be translated 
there but with new translated values.

gTranslator detects :params and don't translate them. For example : "Welcome to :site !" with :site = home will give you in french "Bienvenue à home".

If you have extra directories where you have translations, different than resources/lang folder, please run
```shell
php artisan vendor:publish
```
and add your directories paths in the gTranslator newly created config file.

Please feel free to share with me your thoughts and suggestions.



