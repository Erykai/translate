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
                if ($this->getLang() != "en") {
                    $file = $this->getPath() . "/" . $this->getLang() . "/" . $this->nameFile . ".translate";
                    file_put_contents($file, $this->translateErykia($value, $this->nameFile) . PHP_EOL, FILE_APPEND);
                }
                $file = $this->getPath() . "/en/" . $this->nameFile . ".translate";
                file_put_contents($file, $value . PHP_EOL, FILE_APPEND);
                $data->translate = $value;
                return $data;
            }
        }
        if (empty($t[$data->translate])) {
            if ($this->getLang() != "en") {
                $file = $this->getPath() . "/" . $this->getLang() . "/" . $this->nameFile . ".translate";
                file_put_contents($file, $this->translateErykia($data->translate, $this->nameFile) . PHP_EOL, FILE_APPEND);
            }
            $file = $this->getPath() . "/en/" . $this->nameFile . ".translate";
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
        if ($this->getLang() != "en") {
            $path = $this->getPath() . "/" . $this->getLang();
            if (!is_dir($path) && !mkdir($path, 0755) && !is_dir($path)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }

        $path = $this->getPath() . "/en";
        if (!is_dir($path) && !mkdir($path, 0755) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }

    /**
     * @return string
     */
    private function fileDefault(): string
    {
        $fileDefault = $this->getPath() . "/en/" . $this->nameFile . ".translate";
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
        $fileDefault = $this->getPath() . "/en/" . $this->nameFile . ".translate";
        if ($this->getLang() != "en") {
            $fileTranslate = $this->getPath() . "/" . $this->getLang() . "/" . $this->nameFile . ".translate";
        }
        if ($this->getLang() != "en") {
            if (!is_file($fileTranslate)) {
                file_put_contents($fileTranslate,$this->translateErykia(file_get_contents($fileDefault), $this->nameFile));
                //copy($fileDefault, $fileTranslate);
            }
            return $fileTranslate;
        }
        return $fileDefault;
    }

    /**
     * @param string $text
     * @param $route
     * @return mixed
     */
    private function translateErykia(string $text, $route): mixed
    {
        if (empty(RESPONSE_TRANSLATE_API_KEY)) {
            return $text;
        }
        $url = RESPONSE_TRANSLATE_API_URL;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => http_build_query([
                "key" => RESPONSE_TRANSLATE_API_KEY,
                "source" => "en",
                "target" => $this->getLang(),
                "text" => $text,
                "route" => $route ?? ""
            ])
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        if (!$response) {
            return $text;
        }
        $data = json_decode($response);
        if ($data->status === "success") {
            return $data->translate;
        }
        return $text;

    }
}