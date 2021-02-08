# String

This is a (work in progress) port of Ruby String to PHP. Not all methods will be ported.
The examplified methods are already ported. The non documented methods must be implemented.

```php
use PO\Hash;
```

The methods that have description are the ones that were impplemented. Fill free to write your implementation.

- ```ascii_only?```
- ```at``` - returns the string whitin a given range. Works like substr
```php
$string = new String('abcde');
$string->at(0); // a
$string->at(1); // b
$string->at(1, 3); // bcd

```
- ```between?```
- ```bytes```
- ```bytesize```
- ```byteslice```
- ```capitalize```
- ```capitalize!```
- ```casecmp```
- ```center```
- ```chars```
- ```chomp```
- ```chomp!```
- ```chop```
- ```chop!```
- ```chr```
- ```clear```
- ```codepoints```
- ```concat```
- ```count``` - Get the number of chars
- ```crypt```
- ```delete```
- ```delete!```
- ```downcase``` - use ```toLowerCase```
- ```downcase!```
- ```dump```
- ```each_byte```
- ```each_char```
- ```each_codepoint```
- ```each_line```
- ```empty?```
- ```encode```
- ```encode!```
- ```encoding```
- ```end_with?```
- ```force_encoding```
- ```getbyte```
- ```gsub``` - Replace string
- ```gsub!```
- ```hex```
- ```include?```
- ```index```
- ```insert```
- ```intern```
- ```isRegexp``` - does not exist in ruby. Ass a regexp is not an object in PHP, this method tells if the object is a Regular Expression.
- ```length``` - alias to count
- ```lines```
- ```ljust```
- ```lstrip```
- ```lstrip!```
- ```match```
- ```next```
- ```next!```
- ```oct```
- ```ord```
- ```partition```
- ```parameterize``` - string to friendly url
- ```prepend```
- ```replace```
- ```reverse```
- ```reverse!```
- ```rindex```
- ```rjust```
- ```rpartition```
- ```rstrip```
- ```rstrip!```
- ```scan```
- ```setbyte```
- ```size```
- ```slice```
- ```slice!```
- ```split``` - Splits the string into a Hash of Strings, based on the given separator.
- ```squeeze```
- ```squeeze!```
- ```start_with?```
- ```strip```
- ```strip!```
- ```sub```
- ```sub!```
- ```succ```
- ```succ!```
- ```sum```
- ```swapcase```
- ```swapcase!```
- ```toLowerCase``` - self descriptive.
- ```toUpperCase``` - self descriptive.
- ```to_c```
- ```to_f```
- ```to_i```
- ```to_r```
- ```to_str```
- ```to_sym```
- ```tr```
- ```tr!```
- ```tr_s```
- ```tr_s!```
- ```unpack```
- ```upcase```
- ```upcase!```
- ```upto```
- ```valid_encoding?```
