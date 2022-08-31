# Translate
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
const TRANSLATE_API_KEY = 'AKfycbz5DyirjUO6U_TQqkRSgLavLbThsOolNxz2bhj6_2c_RNHKkXLvGsxZMg0Bom_UzlI_';
//IMPORTANT KEY IS VALID IF ERROR:
/*
contact webav.com.br@gmail.com ou whatsapp https://wa.me/+5564992367973 and solicit new key free
*/
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