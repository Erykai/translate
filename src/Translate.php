<?php

namespace Erykai\Translate;

class Translate extends Resource
{
    public function data(object $data): static
    {
        if(empty($data->nameDefault))
        {
            die('to use the Translate component send an object that contains the example attribute $data->nameDefault = "route";');
        }
        if(empty($data->translate))
        {
            die('to use the Translate component send an object that contains the example attribute $data->translate = "Hello Word";');
        }
        $this->setDynamic();
        if(!empty($data->dynamic)){
            $this->setDynamic($data->dynamic);
            unset($data->dynamic);
        }
        $this->setResponse($data);
        return $this;
    }

    /**
     * @param string|null $lang
     * @return $this
     */
    public function lang(?string $lang = null): static
    {
        $this->setLang($lang);
        $this->setResponse($this->translate($this->getResponse()));
        return $this;
    }

    public function response()
    {
        return $this->getResponse();
    }
}