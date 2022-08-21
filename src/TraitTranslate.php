<?php

namespace Erykai\Translate;
/**
 * translate return data in json, array and object
 */
trait TraitTranslate
{
    /**
     * @var array
     */
    private array $wordsDefaults;
    /**
     * @var array
     */
    private array $wordsTranslate;
    /**
     * @var string
     */
    private string $nameFile;

    /**
     * @param object $data
     * @return object
     */
    protected function translate(object $data): object
    {

        $this->setWords($data->nameDefault);
        $t = [];
        $i = 0;
        if (count($this->wordsDefaults) > count($this->wordsTranslate)) {
            foreach ($this->wordsDefaults as $key => $wordsDefault) {
                if(empty($this->wordsTranslate[$key])){
                    $file = $this->getPath() . "/" . $this->getLang() . "/" . $this->nameFile . ".translate";
                    file_put_contents($file, $wordsDefault, FILE_APPEND);
                }
            };
            $this->wordsTranslate = array_filter(file($this->fileTranslate()));

        }
        foreach ($this->wordsDefaults as $wordsDefault) {
            $wordsDefault = trim($wordsDefault);

            $this->wordsTranslate[$i] = trim($this->wordsTranslate[$i]);
            if (!empty($this->getDynamic())) {
                $key = trim(str_replace("<#>", $this->getDynamic(), $wordsDefault));
                if (empty($this->wordsTranslate[$i])) {
                    $value = $wordsDefault;
                } else {
                    $value = trim(str_replace("<#>", $this->getDynamic(), $this->wordsTranslate[$i]));
                }
            } else {
                $key = $wordsDefault;
                if (empty($this->wordsTranslate[$i])) {
                    $value = $wordsDefault;
                } else {
                    $value = trim($this->wordsTranslate[$i]);
                }
            }
            $t[$key] = $value;
            $i++;
        }
        if (!empty($this->getDynamic())) {
            $value = trim(str_replace($this->getDynamic(), "<#>", $data->translate));
            $translate = trim(str_replace("<#>", $this->getDynamic(), $data->translate));
            if (empty($t[$translate])) {
                $file = $this->getPath() . "/" . $this->getLang() . "/" . $this->nameFile . ".translate";
                file_put_contents($file, $value . PHP_EOL, FILE_APPEND);
                $file = $this->getPath() . "/_default/" . $this->nameFile . ".translate";
                file_put_contents($file, $value . PHP_EOL, FILE_APPEND);
                $data->translate = $value;
                return $data;
            }
        }
        if (empty($t[$data->translate])) {
            $file = $this->getPath() . "/" . $this->getLang() . "/" . $this->nameFile . ".translate";
            file_put_contents($file, $data->translate . PHP_EOL, FILE_APPEND);
            $file = $this->getPath() . "/_default/" . $this->nameFile . ".translate";
            file_put_contents($file, $data->translate . PHP_EOL, FILE_APPEND);
            return $data;
        }

        $data->translate = $t[$data->translate];

        return $data;
    }

    /**
     *
     */
    private function setWords(string $nameFile)
    {
        $this->createDir();
        $this->nameFile = $nameFile;
        $default = file($this->fileDefault());
        $translate = file($this->fileTranslate());
        $this->wordsDefaults = array_filter($default);
        $this->wordsTranslate = array_filter($translate);
    }

    /**
     *
     */
    private function createDir(): void
    {
        $path = $this->getPath();
        if (!is_dir($path) && !mkdir($path, 0755) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
        $path = $this->getPath() . "/" . $this->getLang();
        if (!is_dir($path) && !mkdir($path, 0755) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        $path = $this->getPath() . "/_default";
        if (!is_dir($path) && !mkdir($path, 0755) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }

    /**
     * @return string
     */
    private function fileDefault()
    {
        $fileDefault = $this->getPath() . "/_default/" . $this->nameFile . ".translate";
        if (!is_file($fileDefault)) {
            $dataDefault = '';
            file_put_contents($fileDefault, $dataDefault);
        }
        return $fileDefault;
    }

    /**
     * @return string
     */
    private function fileTranslate()
    {
        $fileDefault = $this->getPath() . "/_default/" . $this->nameFile . ".translate";
        $fileTranslate = $this->getPath() . "/" . $this->getLang() . "/" . $this->nameFile . ".translate";
        if (!is_file($fileTranslate)) {
            copy($fileDefault, $fileTranslate);
        }
        return $fileTranslate;
    }
}