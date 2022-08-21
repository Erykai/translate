<?php

namespace Erykai\Translate;

class Resource
{
    use TraitTranslate;
    /**
     * @var string
     */
    protected string $path;
    /**
     * @var object
     */
    protected object $response;
    /**
     * @var string
     */
    protected string $lang;
    /**
     * @var string|null
     */
    protected ?string $dynamic;

    /**
     *
     */
    public function __construct()
    {
        $this->setPath();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     */
    public function setPath(): void
    {
        $this->path = dirname(__DIR__, 4) . "/" . RESPONSE_TRANSLATE_PATH;
    }

    /**
     * @return object
     */
    public function getResponse(): object
    {
        return $this->response;
    }

    /**
     * @param object $response
     */
    public function setResponse(object $response): void
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param string|null $lang
     * @return bool
     */
    public function setLang(?string $lang): bool
    {
        if ($lang) {
            $this->lang = $lang;
            return true;
        }
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            [$lang] = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $this->lang = $lang;
            return true;
        }
        $this->lang = "en";
        return true;
    }

    /**
     * @return string|null
     */
    public function getDynamic(): ?string
    {
        return $this->dynamic;
    }

    /**
     * @param string|null $dynamic
     */
    public function setDynamic(?string $dynamic = null): void
    {
        $this->dynamic = $dynamic;
    }
}