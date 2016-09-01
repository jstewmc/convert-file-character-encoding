# encode-file
Encode a file.

```php
use Jstewmc\EncodeFile\Encode;
use Jstewmc\ReadFile\Read;
use Jstewmc\WriteFile\Write;

// set the filename
$filename = '/path/to/foo.txt';

// create an ASCII encoded string
$contents = mb_convert_encoding('foo', 'ASCII');

// put the contents into the file
file_put_contents($filename, $contents);

// is the file UTF-32 encoded?
mb_check_encoding(file_get_contents($filename), 'UTF-32');  // returns false

// create the service
$service = new Encode(new Read(), new Write());

// convert the file to UTF-32
$service($filename, 'UTF-32');

// is the file UTF-32 encoded?
mb_check_encoding(file_get_contents($filename), 'UTF-32');  // returns true
```

## Dependencies

This library requires PHP's non-default [`mbstring`](http://php.net/manual/en/book.mbstring.php) extension. If the service is instantiated without the `mbstring` extension loaded, a `RuntimeException` will be thrown.

## From encoding

It's difficult to detect a string's character encoding. Even PHP's [`mb_detect_encoding()`](http://php.net/manual/en/function.mb-detect-encoding.php) function is not perfect. For example, `mb_detect_encoding()` will almost never detect _Windows-1252_ encoding, even if the string actually is _Windows-1252_ encoded (see [Bug #64667](https://bugs.php.net/bug.php?id=64667) for details).

To prevent erroneously detecting the file's _from_ encoding, you MAY include it as the service's third argument:

```php
use Jstewmc\EncodeFile\Encode;
use Jstewmc\ReadFile\Read;
use Jstewmc\WriteFile\Write;

$service = new Encode(new Read(), new Write());

// encode file as UTF-8 from Windows-1252
$service('/path/to/file.txt', 'UTF-8', 'Windows-1252');
```

## Author

[Jack Clayton](mailto:clayjs0@gmail.com)

## License

[MIT](https://github.com/jstewmc/encode-file/blob/master/LICENSE)

## Version

### 0.2.0, August 31, 2016

* Rename repository to `encode-file`
* Refactor library to use [jstewmc/read-file](https://github.com/jstewmc/read-file) and [jstewmc/write-file](https://github.com/jstewmc/write-file)

### 0.1.0, August 27, 2016

* Initial release
