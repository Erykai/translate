<?php

namespace Erykai\Translate;

use stdClass;

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
    public function target(?string $lang = null, string $module = null, ?string $keyArray = null): static
    {
        $this->lang($lang);
        $this->dir($module);
        $this->file($module, $keyArray);
        $this->setResponse();
        return $this;
    }

    /**
     * @param string|null $lang
     * @return string
     */
    public function lang(?string $lang = null): string
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
        return $this->getTarget();
    }

    /**
     * @param $messages
     * @param $translate
     * @param $filenameWithoutExtension
     * @param string $parentKey
     * @param string $target
     * @return void
     */
    public function processMessages($messages, $translate, $filenameWithoutExtension, string $parentKey = '', string $target = TRANSLATE_DEFAULT): void
    {
        foreach ($messages as $key => $message) {
            $fullKey = $parentKey ? $parentKey . '.' . $key : $key;
            if (is_array($message)) {
                $this->processMessages($message, $translate, $filenameWithoutExtension, $fullKey, $target);
            } else {
                $data = new stdClass();
                $data->file = $filenameWithoutExtension;
                $data->text = $message;

                preg_match_all('/:(\w+)/', $message, $matches);
                if (!empty($matches[0])) {
                    $data->dynamic = $matches[0];
                }

                $translate->data($data)->target($target, keyArray: $fullKey)->response();
            }
        }
    }


    /**
     * @return object
     */
    public function response(): object
    {
        return $this->getResponse();
    }

}