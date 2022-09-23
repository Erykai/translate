# Translate
[![Maintainer](http://img.shields.io/badge/maintainer-@alexdeovidal-blue.svg?style=flat-square)](https://instagram.com/alexdeovidal)
[![Source Code](http://img.shields.io/badge/source-erykai/translate-blue.svg?style=flat-square)](https://github.com/erykai/translate)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/erykai/translate.svg?style=flat-square)](https://packagist.org/packages/erykai/translate)
[![Latest Version](https://img.shields.io/github/release/erykai/translate.svg?style=flat-square)](https://github.com/erykai/translate/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Quality Score](https://img.shields.io/scrutinizer/g/erykai/translate.svg?style=flat-square)](https://scrutinizer-ci.com/g/erykai/translate)
[![Total Downloads](https://img.shields.io/packagist/dt/erykai/translate.svg?style=flat-square)](https://packagist.org/packages/erykai/translate)

Component for all language translation, system messages and routes

## Installation

Composer:

```bash
"erykai/translate": "2.0.*"
```

Terminal

```bash
composer require erykai/translate
```

Create config.php

```php
const TRANSLATE_PATH = 'translate';
const TRANSLATE_DEFAULT = 'en';
const TRANSLATE_API_URL = 'https://translate.erykia.com';

```

Translate define language ->target("es") ou ->target() default "en"


```php
use Erykai\Translate\Translate;

require_once "test/config.php";
require_once "vendor/autoload.php";

$translate = new Translate();
$data = new stdClass();
$data->file = "route";
$data->text = "/send/{id}/{slug}";
$data->dynamic = "/{id}/{slug}";
// send object
// require file $data->file = "message" save file message.translate
// require text language "en"
// optional dynamic data not translate exemple:
// email webav.com.br@gmail invalid
// $data->dynamic = "webav.com.br@gmail"
// translate pt-BR email webav.com.br@gmail invalido
echo $translate->data($data)->target("es")->response()->translate;
```

## Contribution

All contributions will be analyzed, if you make more than one change, make the commit one by one.

## Support


If you find faults send an email reporting to webav.com.br@gmail.com.

## Credits

- [Alex de O. Vidal](https://github.com/alexdeovidal) (Developer)
- [All contributions](https://github.com/erykai/translate/contributors) (Contributors)

## License

The MIT License (MIT). Please see [License](https://github.com/erykai/translate/LICENSE) for more information.