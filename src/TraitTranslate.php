<?php

namespace Erykai\Translate;

use JsonSchema\Exception\RuntimeException;

/**
 *
 */
trait TraitTranslate
{
    /**
     * @param string $dir
     * create dir
     */
    private function create(string $dir): void
    {
        if (!is_dir($dir) && !mkdir($dir, 0755) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
    }

    /**
     * create dir defaults
     */
    protected function dir(): void
    {
        $this->create($this->getPath());
        $this->create("{$this->getPath()}/{$this->getSource()}");
        $this->create("{$this->getPath()}/{$this->getTarget()}");
    }

    /**
     * create files .translate
     */
    protected function file(): void
    {
        $this->setSourceFile("{$this->getPath()}/{$this->getSource()}/{$this->getData()->file}.translate");
        $this->setTargetFile("{$this->getPath()}/{$this->getTarget()}/{$this->getData()->file}.translate");

        if (!is_file($this->getSourceFile())) {
            file_put_contents($this->getSourceFile(), $this->getData()->text . PHP_EOL);
        } else if(!in_array($this->getData()->text . PHP_EOL, array_filter(file($this->getSourceFile())), true)){
            file_put_contents($this->getSourceFile(), $this->getData()->text . PHP_EOL, FILE_APPEND);
        }

        if (!is_file($this->getTargetFile())) {
            file_put_contents($this->getTargetFile(), $this->translate(file_get_contents($this->getSourceFile())). PHP_EOL);
        } else {
            $source = array_filter(file($this->getSourceFile()));
            $target = array_filter(file($this->getTargetFile()));
            $result = array_diff_key($source, $target);
            $implode = implode("", $result);
            if(count(array_filter(file($this->getSourceFile()))) > count(array_filter(file($this->getTargetFile())))){
                file_put_contents($this->getTargetFile(), $this->translate($implode) . PHP_EOL, FILE_APPEND);
            }

        }

    }

    /**
     * @param string $text
     * @return mixed
     * send server translate
     */
    private function translate(string $text): mixed
    {
        if (empty(TRANSLATE_API_KEY)) {
            return $text;
        }
        $url = TRANSLATE_API_URL;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => http_build_query([
                "key" => TRANSLATE_API_KEY,
                "source" => "en",
                "target" => $this->getTarget(),
                "text" => $text,
                "route" => $this->getData()->file
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