# convert-file-character-encoding
Convert a file's character-encoding.

```php
use Jstewmc\ConvertFileCharacterEncoding\Convert;

// set the filename
$filename = '/path/to/foo.txt';

// create an ASCII encoded string
$contents = mb_convert_encoding('foo', 'ASCII');

// put the contents into the file
file_put_contents($filename, $contents);

// is the file UTF-32 encoded?
mb_check_encoding(file_get_contents($filename), 'UTF-32');  // returns false

// create the service
$service = new Convert('UTF-32');

// convert the file to UTF-32
$service($filename);

// is the file UTF-32 encoded?
mb_check_encoding(file_get_contents($filename), 'UTF-32');  // returns true
```

## Dependencies

This library requires PHP's non-default [`mbstring`](http://php.net/manual/en/book.mbstring.php) extension. If the service is instantiated without the `mbstring` extension loaded, a `RuntimeException` will be thrown.

## Detect encoding

It's difficult to detect a string's character encoding, and PHP's [`mb_detect_encoding()`](http://php.net/manual/en/function.mb-detect-encoding.php) function is not perfect. For example, `mb_detect_encoding()` will almost never detect _Windows-1252_ encoding, even if the string actually is _Windows-1252_ encoded (see [Bug #64667](https://bugs.php.net/bug.php?id=64667) for details).

To prevent erroneously detecting the file's _from_ encoding, you MAY include it as the service's second constructor argument if it's known:

```php
use Jstewmc\ConvertFileCharacterEncoding\Convert;

// convert files to UTF-8 from Windows-1252
$service = new Convert('UTF-8', 'Windows-1252');

```

## Exceptions

This library will throw an exception in the following situations:

* If the `mbstring` extension is not loaded (`RuntimeException`); 
* If the _to_ encoding is not valid (`InvalidArgumentException`);
* If the _from_ encoding is not valid (`InvalidArgumentException`);
* If the _to_ and _from_ encoding are equal (`InvalidArgumentException`);
* If the file is not readable (`InvalidArgumentException`);
* If the file's character-encoding cannot be detected (`OutOfBoundsException`); or,
* If the file is not writeable (`InvalidArgumentException`).

## Author

[Jack Clayton](mailto:clayjs0@gmail.com)

## License

[MIT](https://github.com/jstewmc/convert-file-character-encoding/blob/master/LICENSE)

## Version

### 0.1.0, August 27, 2016

* Initial release
