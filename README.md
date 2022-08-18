# Translate
Component for all language translation, system messages and routes

## Installation

Composer:

```bash
"erykai/translate": "1.0.*"
```

Terminal

```bash
composer require erykai/translate
```

Create config.php

```php
const RESPONSE_TRANSLATE_PATH = 'translate';
```

Translate define language

```php
use Erykai\Translate\Translate;

require_once "test/config.php";
require_once "vendor/autoload.php";

$translate = new Translate();
$data = new stdClass();
$data->nameDefault = "route";
$data->translate = "/users";
echo $translate->data($data)->lang("en")->getResponse()->translate;
```

Translate language browser

```php
use Erykai\Translate\Translate;

require_once "test/config.php";
require_once "vendor/autoload.php";

$translate = new Translate();
$data = new stdClass();
$data->nameDefault = "message";
$data->translate = "Hello Word";
echo $translate->data($data)->lang()->getResponse()->translate;
```

Translate define language and dynamic text

```php
use Erykai\Translate\Translate;

require_once "test/config.php";
require_once "vendor/autoload.php";

$translate = new Translate();
$data = new stdClass();
$data->nameDefault = "route";
$data->translate = "/users/{id}/{slug}";
$data->dynamic = "/{id}/{slug}";
echo $translate->data($data)->lang()->getResponse()->translate;
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