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
                if (empty($this->wordsTranslate[$key])) {
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
                file_put_contents($file, $this->translateGoogle($value, $this->nameFile) . PHP_EOL, FILE_APPEND);
                $file = $this->getPath() . "/_default/" . $this->nameFile . ".translate";
                file_put_contents($file, $value . PHP_EOL, FILE_APPEND);
                $data->translate = $value;
                return $data;
            }
        }
        if (empty($t[$data->translate])) {
            $file = $this->getPath() . "/" . $this->getLang() . "/" . $this->nameFile . ".translate";
            file_put_contents($file, $this->translateGoogle($data->translate, $this->nameFile) . PHP_EOL, FILE_APPEND);
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
    private function setWords(string $nameFile): void
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
    private function fileDefault(): string
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

    /**
     * @param string $text
     * @param $route
     * @return mixed
     */
    private function translateGoogle(string $text, $route): mixed
    {
        if (empty(RESPONSE_TRANSLATE_API_KEY)) {
            return $text;
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://script.google.com/macros/s/' . RESPONSE_TRANSLATE_API_KEY . '/exec',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('source' => 'en', 'target' => $this->getLang(), 'text' => $text),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);
        if ($data["status"] === "success") {
            if ($route === "route") {
                return $this->route($data["translate"]);
            }
            return $data["translate"];
        }
        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    private function route(string $text): string
    {
        $characters = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', ' ' => ''
        );
        $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $text);
        return strtolower(strtr($stripped, $characters));
    }
}