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

    function config()
    {
        if (is_writeable('config.json')) {

            echo 'reading configs...';
            $config = json_decode(fopen("config.json", 'w+'));

            $from = $config->from;
            $to = $config->to;
            $javaVersion = $config->javaVersion;
            $zipPath = $config->zipPath;


            switch($from){
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

            switch($javaVersion){
                case '1.8':
                    $this->javaVersion = $javaVersion;
                    break;
                case '1.8+':
                    $this->javaVersion = $javaVersion;
                    break;
                default:
                    echo "\ninvalid java version provided. Use one of 1.8 or 1.8+.";
            }

            if(file_exists($zipPath)){
                $this->zipPath = $zipPath;
            }else{
                echo "\ninvalid zip path given. The file does not exist";
            }

        } else {

            echo "\nconfig not readable! Please check if you have the config.json file. We will build it for you.";
            $config = new Config('bedrock', 'java', 'pack.zip', '1.8');
            $file = fopen('config.json', 'w+');
            fwrite($file, json_encode($config));
            fclose($file);

        }
    }

    function convert()
    {

        $this->config();
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





