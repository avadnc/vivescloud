# PHP Objects

Primitive types behaving like grown up objects. Compatible with PHP 5.3.3+

[![Build Status](https://travis-ci.org/mjacobus/php-objects.png?branch=master)](https://travis-ci.org/mjacobus/php-objects)
[![Coverage Status](https://coveralls.io/repos/mjacobus/php-objects/badge.png)](https://coveralls.io/r/mjacobus/php-objects)
[![Code Climate](https://codeclimate.com/github/mjacobus/php-objects.png)](https://codeclimate.com/github/mjacobus/php-objects)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mjacobus/php-objects/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mjacobus/php-objects/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/php-objects/php-objects/v/stable.svg)](https://packagist.org/packages/php-objects/php-objects)
[![Total Downloads](https://poser.pugx.org/php-objects/php-objects/downloads.svg)](https://packagist.org/packages/php-objects/php-objects)
[![Latest Unstable Version](https://poser.pugx.org/php-objects/php-objects/v/unstable.svg)](https://packagist.org/packages/php-objects/php-objects)
[![License](https://poser.pugx.org/php-objects/php-objects/license.svg)](https://packagist.org/packages/php-objects/php-objects)

Once you've written some ruby code and you HAVE to write php, you miss the
hell out off ruby objects.

Well, not anymore. Hopefully.

- [PO\Object](docs/Object.md)
- [PO\Hash](docs/Hash.md) - There are tons of contribuitions you can make here
- [PO\String](docs/String.md) - And here!

## Installing

### Installing via Composer
Append the lib to your requirements key in your composer.json.

```javascript
{
    // composer.json
    // [..]
    require: {
        // append this line to your requirements
        "php-objects/php-objects": "dev-master"
    }
}
```

### Alternative install
- Learn [composer](https://getcomposer.org). You should not be looking for an alternative install. It is worth the time. Trust me ;-)
- Follow [this set of instructions](#installing-via-composer)

## Issues/Features proposals

[Here](https://github.com/mjacobus/php-objects/issues) is the issue tracker.

## Contributing

Only TDD code will be accepted. Please follow the [PSR-2 code standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request

### How to run the tests:

```bash
phpunit --configuration tests/phpunit.xml
```

### To check the code standard run:

```bash
phpcs --standard=PSR2 lib
phpcs --standard=PSR2 tests

# alternatively

./bin/travis/run_phpcs.sh
```

## Lincense
[MIT](MIT-LICENSE)

## Authors

- [Marcelo Jacobus](https://github.com/mjacobus)
