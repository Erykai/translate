<?php

namespace Erykai\Translate;

/**
 * class get and set
 */
class Resource
{
    use TraitTranslate;

    /**
     * @var object
     */
    private object $data;
    /**
     * @var string
     */
    private string $dynamic;
    /**
     * @var string
     */
    private string $path;
    /**
     * @var string
     */
    private string $source;
    /**
     * @var string
     */
    private string $sourceFile;
    /**
     * @var string
     */
    private string $target;
    /**
     * @var string
     */
    private string $targetFile;
    /**
     * @var string
     */
    private string $response;

    /**
     *
     */
    public function __construct()
    {
        $this->setPath(dirname(__DIR__, 4)."/".TRANSLATE_PATH);
        $this->setSource('en');
    }

    /**
     * @return object
     */
    protected function getData(): object
    {
        return $this->data;
    }

    /**
     * @param object $data
     */
    protected function setData(object $data): void
    {
        $this->data = $data;
    }



    /**
     * @return string
     */
    protected function getDynamic(): string
    {
        return $this->dynamic;
    }

    /**
     * @param string $dynamic
     */
    protected function setDynamic(string $dynamic): void
    {
        $this->dynamic = $dynamic;
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    protected function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    protected function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    protected function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    protected function getSourceFile(): string
    {
        return $this->sourceFile;
    }

    /**
     * @param string $sourceFile
     */
    protected function setSourceFile(string $sourceFile): void
    {
        $this->sourceFile = $sourceFile;
    }

    /**
     * @return string
     */
    protected function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    protected function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    protected function getTargetFile(): string
    {
        return $this->targetFile;
    }

    /**
     * @param string $targetFile
     */
    protected function setTargetFile(string $targetFile): void
    {
        $this->targetFile = $targetFile;
    }

    /**
     * @return string
     */
    protected function getResponse(): string
    {
        return $this->response;
    }

    /**
     * set response translator
     */
    protected function setResponse(): void
    {
        $source =  file($this->getSourceFile());
        $target =  file($this->getTargetFile());
        $key = array_search($this->getData()->text . PHP_EOL, $source, true);
        $this->response = str_replace("<#>", $this->getDynamic(), $target[$key]);
    }


}