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

If you need use self expiring counter(flag?), you can set third
parameter in the constructor:
```php
$counter = new MyCounter($user, $client, 3600); // hour, in this case
```
By default it's set to never expire.


*Important note.* You will have to use binary protocol in memcached. 
For example, it could be enabled this way:
```php
$client->setOption(\Memcached::OPT_SERIALIZER, \Memcached::SERIALIZER_IGBINARY);
$client->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
```
But you will also need a binary serializer installed, as you can see. Igbinary in my example.