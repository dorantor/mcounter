# mcounter
Dead simple memcached counter.

## Installation

```sh
composer require dorantor/mcounter
```

## Usage

NB! There are only abstract classes, because it's meant to be extended.


```php
class MyCounter extends \Dorantor\AbstractCounter
{
    /**
     * Method for building cache key based on $item value
     *
     * @return string
     */
    protected function getKey()
    {
        return 'myCounter' . (int) $this->item->id;
    }
}
```

And in your code:
```php
$client = new Memcached();
// .. client setup
// 
// basically, $client creation is up to you.
// Most probably you already created one earlier, so just reuse it here.
$counter = new MyCounter($user, $client);
if ($counter->value() < 100) {
    $counter->inc();
}
```

By default it's set to never expire. But if you need to use self
expiring counter(flag?), you can set third parameter in the
constructor:
```php
$counter = new MyCounter($user, $client, 3600); // hour, in this case
```
or you can define expiry logic/value inside counter by overriding
`getExpiry()` method, p.ex.:
```php
protected function getExpiry()
{
    return 3600; // also hour, but this way it's defined inside counter
    // or it could be some logic based on value(s) in $this->item
}
```
*NB!* Expiry is not updated on inc() call. It's default Memcached
behaviour. If you need to update expiry use `touch()`, p.ex.:
```php
$counter->inc();
$counter->touch();
// or
$counter->incWithTouch();
```
Second option is more convenient but you loose control over `touch()`
success/fail.



*Important note.* You will have to use binary protocol in memcached. 
For example, it could be enabled this way:
```php
$client->setOption(\Memcached::OPT_SERIALIZER, \Memcached::SERIALIZER_IGBINARY);
$client->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
```
But you will also need a binary serializer installed, as you can see. Igbinary in my example.