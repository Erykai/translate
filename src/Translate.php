<?php

namespace Erykai\Translate;

/**
 * Class translate remote all languages
 */
class Translate extends Resource
{
    /**
     * @param object $data
     * @return $this
     */
    public function data(object $data): static
    {
        if(!isset($data->dynamic)){
            $data->dynamic = "";
        }
        $this->setDynamic($data->dynamic);
        $data->text = str_replace($this->getDynamic(),"<#>", $data->text);

        $this->setData($data);
        return $this;
    }

    /**
     * @param ?string $lang
     * @return $this
     * detect language if not declare in target or const TRANSLATE_DEFAULT
     */
    public function target(?string $lang = null): static
    {
        $this->setTarget('en');
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            [$l] = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $this->setTarget($l);
        }
        if(TRANSLATE_DEFAULT){
            $this->setTarget(TRANSLATE_DEFAULT);
        }
        if ($lang) {
            $this->setTarget($lang);
        }
        $this->dir();
        $this->file();
        $this->setResponse();
        return $this;
    }

    /**
     * @return object
     */
    public function response(): object
    {
        return $this->getResponse();
    }

}