# phpoar
Imitate the option and result of rust.

## Usage
```php
function test1(\Phpoar\Option $opt) {}
function test2(\Phpoar\Result $result) {}

test1(\Phpoar\Some(1));
test1(\Phpoar\None());
test2(\Phpoar\Ok("ok"));
test2(\Phpoar\Err(new \Exception()));
```

## Available

#### Result
* unwarp
* is_ok
* is_err
* ok
* err
* map
* map_or
* map_or_else
* map_err
* and
* and_then
* or
* or_else
* unwrap_or

#### Option
* unwarp