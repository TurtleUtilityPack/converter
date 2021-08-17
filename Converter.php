<?php

namespace TurtleClient;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
     * A list of variables that define, for example, {color}
     */
    public array $variables = ["color" => ["red", "blue", "white", "magenta", "light_blue", "yellow", "lime", "pink", "gray", "silver", "cyan", "purple", "blue", "brown", "green", "red", "black"]];

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

    /**
     * @var array|string[][]
     */
    public array $registered_paths = ["item" => ["bedrock" => "textures/items", "java" => "assets/minecraft/textures/item"], "block" => ["bedrock" => "textures/blocks", "java" => "assets/minecraft/textures/block"]];

    public function __construct()
    {

        if(is_readable("textures.txt"))
        {

            $contents = file_get_contents("textures.txt");
            $this->textures_bedrock = explode("\n", $contents);

            $count = 0;

            foreach($this->textures_bedrock as $shit)
            {
                $this->textures_bedrock[$count] = trim($shit);

                $count++;
            }

        }


    }

    public function config(): bool
    {

        if (is_writeable('config.json')) {

            echo 'Reading config...';

            $h = fopen("config.json", 'r');
            $config = json_decode(fread($h, 10000));

            $from = $config->from;
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

            echo "\n\nExtracting files to convert....\n\n";

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

                echo "\nMore than 1 folder detected! Choose one of these (Type their number value):\n\n";
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
            $folder_converted = $converter_folder_name. "-Converted";

            @mkdir($folder_converted);

            if($this->from == 'bedrock')
            {

                copy($converter_folder_name . DIRECTORY_SEPARATOR . "pack_icon.png", $folder_converted . DIRECTORY_SEPARATOR . "pack.png");

                $manifest = json_decode(file_get_contents($converter_folder_name . DIRECTORY_SEPARATOR . "manifest.json"));

                $mcmeta = new \stdClass();
                $mcmeta->pack = ['pack_format' => $manifest->format_version, 'description' => $manifest->header->description];

                $mcmetaHandle = fopen($folder_converted . DIRECTORY_SEPARATOR . "pack.mcmeta", "w+");
                fwrite($mcmetaHandle, json_encode($mcmeta, JSON_PRETTY_PRINT));
                fclose($mcmetaHandle);


                @mkdir($folder_converted . DIRECTORY_SEPARATOR . "assets");
                @mkdir($folder_converted . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "minecraft");
                @mkdir($folder_converted . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "minecraft" . DIRECTORY_SEPARATOR . "textures");
                @mkdir($folder_converted . DIRECTORY_SEPARATOR . $this->items_path_java);
                @mkdir($folder_converted . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "minecraft" . DIRECTORY_SEPARATOR . "textures" . DIRECTORY_SEPARATOR . "block");


                $this->recurse_copy($converter_folder_name . DIRECTORY_SEPARATOR . $this->registered_paths["item"]["bedrock"], $folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths["item"]["java"]);
                $this->recurse_copy($converter_folder_name . DIRECTORY_SEPARATOR . $this->registered_paths["block"]["bedrock"], $folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths["block"]["java"]);

                $textures = $this->textures_bedrock;


                foreach($textures as $texture)
                {

                    $languagizedTexture = $this->languagize($texture);

                    if($languagizedTexture->type === "custom") {

                        $custom = $this->register_path($languagizedTexture->full);

                        if(file_exists($folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$custom]["java"] . DIRECTORY_SEPARATOR . $languagizedTexture->bedrock . ".png")) {

                            rename($folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$custom]["java"] . DIRECTORY_SEPARATOR . $languagizedTexture->bedrock . ".png", $folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$custom]["java"] . DIRECTORY_SEPARATOR . $languagizedTexture->java . ".png");
                            echo "\nConverting " . $languagizedTexture->bedrock . ".png\n";


                        }
                        else
                            echo "\nFailed to convert file $languagizedTexture->bedrock. It doesn't exist. Full path used: " . $folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$custom]["java"] . DIRECTORY_SEPARATOR . $languagizedTexture->bedrock . ".png\n\n";

                    } elseif ($languagizedTexture->type === "custom-var") {

                        $this->register_var($languagizedTexture->full);

                    } else {


                        if (strpos($languagizedTexture->bedrock, "{") && strpos($languagizedTexture->bedrock, "}")) {

                            if(array_key_exists($type = $this->getStringBetween($languagizedTexture->bedrock, "{", "}"), $this->variables)) {

                                foreach ($this->variables[$type] as $color) {

                                    $bedrock = str_replace("{". $type . "}", $color, $languagizedTexture->bedrock);
                                    $java = str_replace("{". $type . "}", $color, $languagizedTexture->java);

                                    if (file_exists($folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$languagizedTexture->type]["java"] . DIRECTORY_SEPARATOR . $bedrock . ".png")) {

                                        rename($folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$languagizedTexture->type]["java"] . DIRECTORY_SEPARATOR . $bedrock . ".png", $folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$languagizedTexture->type]["java"] . DIRECTORY_SEPARATOR . $java . ".png");
                                        echo "\nConverting " . $bedrock . ".png\n";

                                    } else
                                        echo "\nFailed to convert file $bedrock. It doesn't exist. Full path used: " . $folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$languagizedTexture->type]["java"] . DIRECTORY_SEPARATOR . $bedrock . ".png\n\n";

                                }
                            } else {

                                echo "\nUndefined type $type. Skipping\n";

                            }

                        } else {

                            if(file_exists($folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$languagizedTexture->type]["java"] . DIRECTORY_SEPARATOR . $languagizedTexture->bedrock . ".png")) {

                                rename($folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$languagizedTexture->type]["java"] . DIRECTORY_SEPARATOR . $languagizedTexture->bedrock . ".png", $folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$languagizedTexture->type]["java"] . DIRECTORY_SEPARATOR . $languagizedTexture->java . ".png");
                                echo "\nConverting " . $languagizedTexture->bedrock . ".png\n";

                            }
                            else
                                echo "\nFailed to convert file $languagizedTexture->bedrock. It doesn't exist. Full path used: " . $folder_converted . DIRECTORY_SEPARATOR . $this->registered_paths[$languagizedTexture->type]["java"] . DIRECTORY_SEPARATOR . $languagizedTexture->bedrock . ".png\n\n";

                        }
                    }
                }

                $this->rmdir_recursive($converter_folder_name);

            } elseif ($this->from == 'java')
            {
                echo "\nWe don't support conversion from java -> bedrock yet. Check our discord constantly for updates.\n\n";
            }



        }



    }

    function rmdir_recursive($dir) {
        $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($it as $file) {
            if ($file->isDir()) rmdir($file->getPathname());
            else unlink($file->getPathname());
        }
        rmdir($dir);
    }

    function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    function languagize($texture): \stdClass {

        $daArray = new \stdClass();

        $daArray->type = $this->getStringBetween($texture, '[', ']');
        $daArray->full = str_replace("[$daArray->type]", "", $texture);
        $daArray->non_equals = explode(" = ", $daArray->full);
        $daArray->bedrock = $daArray->non_equals[0];
        $daArray->java = $daArray->non_equals[1];



        return $daArray;

    }

    /**
     * @param $str
     * @param $from
     * @param $to
     * @return string
     * Gets string between $from and $to
     */
    function getStringBetween($str, $from, $to): string
    {
        $sub = substr($str, strpos ($str,$from) + strlen($from) , strlen ($str));
        return substr($sub,0, strpos ($sub, $to));
    }

    /**
     * @param $number
     * @return false|mixed
     */
    function numericCheck($number) {

        if(is_numeric(trim($number)))

            return $number;

        else {

            echo "Number not numeric. Try again.\n";

        }

        return false;

    }

    /**
     * @param string $full
     */
    public function register_path(string $full): string {

        $explode = explode(" = ", $full);

        $this->registered_paths[$explode[0]] = ["bedrock" => $explode[1], "java" => $explode[2]];

        echo "\nSuccessfully registered $explode[0] file path.\n";

        return $explode[0];

    }

    /**
     * @param string $full
     */
    public function register_var(string $full): string {

        $explode = explode(" = ", $full);

        $name = $explode[0];
        $array = $explode[1];

        $this->registered_paths[$name] = explode(", ", $array);

        echo "\nSuccessfully registered $explode[0] file path.\n";

        return $explode[0];

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
            case '1.8+':
            case '1.8':
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




