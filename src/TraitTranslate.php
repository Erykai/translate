<?php

namespace Erykai\Translate;

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
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
    }

    /**
     * create dir defaults
     */
    protected function dir($module): void
    {
        $this->create($this->getPath());
        $this->create("{$this->getPath()}/{$this->getSource()}");
        if ($module) {
            $this->create("{$this->getPath()}/{$this->getSource()}/public");
        }

        $this->create("{$this->getPath()}/{$this->getTarget()}");
        if ($module) {
            $this->create("{$this->getPath()}/{$this->getTarget()}/public");
        }
    }

    /**
     * create files .translate
     */
    protected function file(string $module = null, ?string $keyArray = null): void
    {
        if ($module) {
            $modulePath = dirname(__DIR__, 4) . "/modules/{$module}/translate";
            $this->create("{$modulePath}/{$this->getSource()}");
            $this->create("{$modulePath}/{$this->getSource()}/public");
            $this->create("{$modulePath}/{$this->getTarget()}");
            $this->create("{$modulePath}/{$this->getTarget()}/public");
            $this->setSourceFile("{$modulePath}/{$this->getSource()}/{$this->getData()->file}." . TRANSLATE_EXT);
            $this->setTargetFile("{$modulePath}/{$this->getTarget()}/{$this->getData()->file}." . TRANSLATE_EXT);
        } else {
            $this->setSourceFile("{$this->getPath()}/{$this->getSource()}/{$this->getData()->file}." . TRANSLATE_EXT);
            $this->setTargetFile("{$this->getPath()}/{$this->getTarget()}/{$this->getData()->file}." . TRANSLATE_EXT);
        }
        if ($keyArray) {
            $this->array($keyArray);
        } else {
            $this->line();
        }

    }

    /**
     * @return void
     */
    protected function line(): void
    {
        if (!is_file($this->getSourceFile())) {
            file_put_contents($this->getSourceFile(), $this->getData()->text . PHP_EOL);
        } else if (!in_array($this->getData()->text . PHP_EOL, array_filter(file($this->getSourceFile())), true)) {
            file_put_contents($this->getSourceFile(), $this->getData()->text . PHP_EOL, FILE_APPEND);
        }

        if (!is_file($this->getTargetFile())) {
            file_put_contents($this->getTargetFile(), $this->translate(file_get_contents($this->getSourceFile())) . PHP_EOL);
        } else {
            $source = array_filter(file($this->getSourceFile()));
            $target = array_filter(file($this->getTargetFile()));
            $result = array_diff_key($source, $target);
            $implode = implode("", $result);
            if (count(array_filter(file($this->getSourceFile()))) > count(array_filter(file($this->getTargetFile())))) {
                file_put_contents($this->getTargetFile(), $this->translate($implode) . PHP_EOL, FILE_APPEND);
            }

        }
    }

    /**
     * @param string $keyArray
     * @return void
     */
    protected function array(string $keyArray): void
    {
        $text = $this->getData()->text;
        $sourceFile = $this->getSourceFile();

        $contentSource = [];
        if (is_file($sourceFile)) {
            $contentSource = include $sourceFile;
        }
        if (!preg_match("/'$keyArray'\s*=>/", var_export($contentSource, true))) {
            $this->insertNestedArrayValue($contentSource, $keyArray, $text);
            $sourceContent = "<?php\n\nreturn " . $this->formatArraySyntax(var_export($contentSource, true)) . ";\n";
            file_put_contents($sourceFile, $sourceContent);
        }

        $targetFile = $this->getTargetFile();
        $contentTarget = [];
        if (is_file($targetFile)) {
            $contentTarget = include $targetFile;
        }
        if (!preg_match("/'$keyArray'\s*=>/", var_export($contentTarget, true))) {
            $translatedText = $this->translate($text);
            $this->insertNestedArrayValue($contentTarget, $keyArray, $translatedText);
            $targetContent = "<?php\n\nreturn " . $this->formatArraySyntax(var_export($contentTarget, true)) . ";\n";
            file_put_contents($targetFile, $targetContent);
        }
    }

    /**
     * @param $array
     * @param $path
     * @param $value
     * @return void
     */
    private function insertNestedArrayValue(&$array, $path, $value): void
    {
        $keys = explode('.', $path);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
    }

    /**
     * @param $content
     * @return array|string
     */
    private function formatArraySyntax($content): array|string
    {
        return str_replace(['array (', ')'], ['[', ']'], $content);
    }

    private function replacePlaceholders($template, $values): string
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $template = preg_replace('/<#>/', $value, $template, 1);
        }
        return $template;
    }

    /**
     * @param string $text
     * @return mixed
     * send server translate
     */
    private function translate(string $text): mixed
    {
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
            return trim($this->replacePlaceholders($data->translate, $this->getDynamic()));
        }
        return trim($this->replacePlaceholders($text, $this->getDynamic()));
    }

}