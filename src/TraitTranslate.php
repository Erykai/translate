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
     * @param object $data
     * @return object
     */
    protected function translate(object $data): object
    {

        $this->setWords($data->nameDefault);
        $t = [];
        $i = 0;
        foreach ($this->wordsDefaults as $wordsDefault) {
            if (!empty($this->getDynamic())) {
                $key = trim(str_replace("**********", $this->getDynamic(), $wordsDefault));
                if (empty($this->wordsTranslate[$i])) {
                    $value = "Insert text '" . trim($wordsDefault) . " <&>' in /" . RESPONSE_TRANSLATE_PATH . "/" . $this->getLang() . ".php";
                } else {
                    $value = trim(str_replace("**********", $this->getDynamic(), $this->wordsTranslate[$i]));
                }
            } else {
                $key = trim($wordsDefault);
                if (empty($this->wordsTranslate[$i])) {
                    $value = "Insert text '" . trim($wordsDefault) . " <&>' in /" . RESPONSE_TRANSLATE_PATH . "/" . $this->getLang() . ".php";
                } else {
                    $value = trim($this->wordsTranslate[$i]);
                }
            }
            $t[$key] = $value;
            $i++;
        }
        if (!empty($this->getDynamic())) {
            $value = trim(str_replace($this->getDynamic(), "**********", $data->translate));
            $translate = trim(str_replace("**********", $this->getDynamic(), $data->translate));
            if (empty($t[$translate])) {
                $data->translate = "Create in 'en' /" . RESPONSE_TRANSLATE_PATH . "/_default.php and in '" . $this->getLang() . "' /" . RESPONSE_TRANSLATE_PATH . "/" . $this->getLang() . ".php insert text -> '$value <&>'";
                return $data;
            }
        }
        if (empty($t[$data->translate])) {
            $data->translate = "Create in 'en' /" . RESPONSE_TRANSLATE_PATH . "/_default.php and in '" . $this->getLang() . "' /" . RESPONSE_TRANSLATE_PATH . "/" . $this->getLang() . ".php insert text -> '$data->translate <&>'";
            return $data;
        }

        $data->translate = $t[$data->translate];

        return $data;
    }

    /**
     *
     */
    private function setWords(string $nameDefault)
    {
        $this->createDir();
        $default = $nameDefault . "Default";
        $translate = $nameDefault . "Translate";
        require_once $this->fileDefault($nameDefault);
        require_once $this->fileTranslate($nameDefault);


        $this->wordsDefaults = array_filter(explode("<&>", trim($$default)));
        $this->wordsTranslate = array_filter(explode("<&>", trim($$translate)));
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
    }

    /**
     * @return string
     */
    private function fileDefault(string $nameDefault)
    {
        $fileDefault = $this->getPath() . "/_default.php";
        $default = $nameDefault . 'Default';

        if (!is_file($fileDefault)) {
            $dataDefault = '<?php
$' . $nameDefault . 'Default = "";';
            file_put_contents($fileDefault, $dataDefault);
        }else{
            if (!strpos(file_get_contents($fileDefault), $default)) {
                $dataDefault = '
$' . $nameDefault . 'Translate = "";';
                file_put_contents($fileDefault, $dataDefault, FILE_APPEND);
            }
        }

        return $fileDefault;
    }

    /**
     * @return string
     */
    private function fileTranslate(string $nameDefault)
    {
        $fileTranslate = $this->getPath() . "/" . $this->getLang() . ".php";
        $translate = $nameDefault . 'Translate';
        if (!is_file($fileTranslate)) {
            $dataTranslate = '<?php
$' . $nameDefault . 'Translate = "";';
            file_put_contents($fileTranslate, $dataTranslate);
        }else{
            if (!strpos(file_get_contents($fileTranslate), $translate)) {
                $dataTranslate = '
$' . $nameDefault . 'Translate = "";';
                file_put_contents($fileTranslate, $dataTranslate, FILE_APPEND);
            }
        }
        return $fileTranslate;
    }
}