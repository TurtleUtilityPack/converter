<?php

namespace TurtleClient;

use function fopen;
use function fwrite;
use function fclose;
use function is_writeable;
use function json_decode;
use function json_encode;


class Converter
{

    /**
     * @var string $from
     * Defines if converting from java or bedrock
     */
    public string $from;

    /**
     * @var string $to
     * Defines if converting to java or bedrock
     */
    public string $to;

    /**
     * @var string $zipPath
     * Where the texture-pack path is located
     */
    public string $zipPath;

    /**
     * @var string $javaVersion
     * Defines if the java version is 1.8 or higher for texture-pack conversion.
     */
    public string $javaVersion;


    /**
     * @var array|string[]
     * Colors that exist in Minecraft used for converting (Glass, etc.)
     */
    public array $colors = ["red", "blue"];

    /**
     * @var array
     * The textures in "textures" folder in bedrock that can be converted.
     */
    public array $textures_bedrock = [];

    /**
     * @var string
     */
    public string $items_path_bedrock = "textures/items";

    /**
     * @var string
     */
    public string $items_path_java = "assets/minecraft/textures/item";

    public function __construct()
    {

        if(is_readable("textures.txt"))
        {

            $contents = file_get_contents("textures.txt");
            $this->textures_bedrock = explode("\n", $contents);

        }


    }

    function config(): bool
    {

        if (is_writeable('config.json')) {

            echo 'Reading config...';

            $h = fopen("config.json", 'r');
            $config = json_decode(fread($h, 10000));

            $from = $config->from;
            $to = $config->to;
            $javaVersion = $config->javaVersion;
            $zipPath = $config->zipPath;

            $from = strtolower($from);
            $javaVersion = strtolower($javaVersion);


            switch ($from) {
                case 'bedrock':
                    $this->from = $from;
                    $this->to = 'java';
                    break;

                case 'java':
                    $this->from = $from;
                    $this->to = 'bedrock';
                    break;

                default:
                    echo "\ninvalid version {$from} provided. Please choose one of bedrock or java.";
                    break;
            }

            switch ($javaVersion) {
                case '1.8':
                    $this->javaVersion = $javaVersion;
                    break;
                case '1.8+':
                    $this->javaVersion = $javaVersion;
                    break;
                default:
                    echo "\ninvalid java version provided. Use one of 1.8 or 1.8+.";
            }

            if (file_exists($zipPath)) {
                $this->zipPath = $zipPath;
            } else {
                echo "\ninvalid zip path given. The file does not exist";
            }

        } else {

            echo "\nconfig.json not readable! Please check if you have the config.json file. We will build it for you.";
            $config = new Config('bedrock', 'java', 'pack.zip', '1.8');
            $file = fopen('config.json', 'w+');
            fwrite($file, json_encode($config, JSON_PRETTY_PRINT));
            fclose($file);

        }

        if(isset($this->from, $this->javaVersion, $this->zipPath)) {

            return true;

        } else {

            return false;

        }
    }

    function convert()
    {

        $config = $this->config();

        if($config){

            $converted_packs = "C:/Turtle/Converted_Packs";

            $zip = new \ZipArchive();
            $zip->open($this->zipPath);
            $zip->extractTo($converted_packs);
            $zip->close();

            $folders = scandir($converted_packs);

            $done = false;

            foreach($folders as $name){

                if(!$done) {
                    if (is_numeric($name)) {

                        $done = true;
                        $folder = $name;
                    }
                }
            }

            if(count($folders) > 1)
            {

                echo "\nMore than 1 folder detected! Choose one of these (Type their number value):\n";
                print_r($folders);

                $handle = fopen ("php://stdin","r");
                $line = fgets($handle);

                if(is_numeric($this->numericCheck(trim($line))))

                {
                    $line = trim($line);
                    echo "\nGreat! Chosen which folder to convert. ($line)\n";
                    $folder = $folders[$line];

                }

                fclose($handle);

            }

            $converter_folder_name = $converted_packs . DIRECTORY_SEPARATOR . $folder;
            $converter_texture_folder = $converter_folder_name . DIRECTORY_SEPARATOR . "textures";



        }
    }

    function numericCheck($number) {

        if(is_numeric(trim($number)))

            return $number;

        else {

            echo "Number not numeric. Try again.\n";

        }

        return false;

    }

}

class Config
{

    /**
     * @var string $from
     * Defines if converting from java or bedrock
     */
    public string $from;

    /**
     * @var string $to
     * Defines if converting to java or bedrock
     */
    public string $to;

    /**
     * @var string $zipPath
     * Where the texture-pack path is located
     */
    public string $zipPath;

    /**
     * @var string $javaVersion
     * Defines if the java version is 1.8 or higher for texture-pack conversion.
     */
    public string $javaVersion;


    public function __construct($from, $to, $zipPath, $javaVersion)
    {

        switch ($from) {
            case 'bedrock':
                $this->from = $from;
                $this->to = 'java';
                break;

            case 'java':
                $this->from = $from;
                $this->to = 'bedrock';
                break;

            default:
                echo "\ninvalid version {$from} provided. Please choose one of bedrock or java.";
                break;
        }

        switch ($javaVersion) {
            case '1.8':
                $this->javaVersion = $javaVersion;
                break;
            case '1.8+':
                $this->javaVersion = $javaVersion;
                break;
            default:
                echo "\ninvalid java version provided. Use one of 1.8 or 1.8+.";
        }

        if (file_exists($zipPath)) {
            $this->zipPath = $zipPath;
        } else {
            echo "\ninvalid zip path given. The file does not exist";
        }

    }
}

$converter = new Converter();
$converter->convert();




