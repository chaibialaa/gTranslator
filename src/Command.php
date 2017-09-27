<?php

namespace Chaibi\gTranslator;

use Illuminate\Console\Command as ConsoleCommand;
use Illuminate\Support\Facades\Lang;

class Command extends ConsoleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate Laravel Localisation Files using Google Translator';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $from = $this->ask('From which language ?', config('app.locale'));
        $to = $this->ask('To which language ?', 'fr');

        $result = $this->start($to,$from);

        $this->info($result);
    }

    public function start($to, $from){
        $translation = config('gTranslator.directories');

        if(empty($translation)){
            $translation =   [
                'default' => 'resources/lang',
            ];
            $this->info('If you have extra Language files, please consider running \'php artisan vendor:publish\' to edit configuration file');
        }

        $this->iterateTranslationFolders($translation,$from,$to);

        return 'Translation saved successfully';
    }

    public function iterateTranslationFolders($translation,$from,$to) {
        foreach($translation as $key=>$directory){
            $dir = base_path().'/'.$directory.'/'.$from.'/';
            $translationFiles = scandir($dir, 1);

            $this->iterateTranslationFiles($translationFiles,$to,$from,$directory);
        }
    }


    public function iterateTranslationFiles($translationFiles,$to,$from,$directory){
        foreach($translationFiles as $file){
            $fileInfo = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = pathinfo($file, PATHINFO_FILENAME);
            if($fileInfo === 'php'){
                $translated = $this->iterateTranslationLines($fileName,$to,$from);
                $translationPath = base_path().'/'.$directory.'/'.$to;
                if (!is_dir($translationPath)) { mkdir($translationPath, 0700); }

                file_put_contents($translationPath .'/'.$fileName.'.php',
                    "<?php ". "\r\n \r\n" ."/* Translation generated with Laravel gTranslator */" . "\r\n \r\n" ." return [ "  . " \r\n \r\n " . $translated . "\r\n \r\n" ."];"
                );
            }
        }
    }

    public function iterateTranslationLines($fileName,$to,$from){
        $translationQueue = Lang::get($fileName);
        $translated = "";
        $this->info('----- TRANSLATING ' .$fileName . ' -----');
        foreach ($translationQueue as $item=>$value){
            $this->info('Translating ' . '\'' . $item . '\'');
            if(is_array($value)){
                $this->error('Unable to translate arrays, passing to next');
            } else {
                if(strpos($value,':')){
                    $translatedWord = self::translateWithParameters($value,$to,$from);
                    $translated = $translated . '\'' . $item . '\'' . ' => ' .'\'' .$translatedWord .'\', ' . "\r\n";
                } else {
                    $translatedWord = str_replace('\'','\\\'',(string)self::translate($value,$to,$from));
                    $translated = $translated . '\'' . $item . '\'' . ' => ' .'\'' .$translatedWord .'\', ' . "\r\n";
                }
                sleep(1);
            }
        }
        return $translated;
    }
    public static function translateWithParameters($value, $to, $from){
        preg_match('/:\S+/', $value, $matches);
        $value = str_replace($matches[0],'$$$$$$$$$',$value);
        return str_replace('$$$$$$$$$', $matches[0], str_replace('\'','\\\'',(string)self::translate($value,$to,$from)));
    }

    public static function translate($word, $to, $from){
        $url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
        $fields = array(
            'sl' => urlencode($from),
            'tl' => urlencode($to),
            'q' => urlencode($word)
        );
        if(strlen($fields['q'])>=5000)
            return $word;

        // URL-ify the data for the POST
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');
        $result = curl_exec($ch);
        curl_close($ch);

        $sentencesArray = json_decode($result, true);
        $sentences = "";
        foreach ($sentencesArray["sentences"] as $s) {
            $sentences .= isset($s["trans"]) ? $s["trans"] : '';
        }
        return $sentences;

    }

}
