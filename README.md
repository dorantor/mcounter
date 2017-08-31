# mcounter
Dead simple memcached counter.

NB! There is only abstract classes, because it's meant to be redefined.

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

And usage:
```php
// $client creation is up to you.
// Most probably you already created one earlier, so just reuse it here.
$counter = new MyCounter($user, $client);
if ($counter->value() < 100) {
    $counter->inc();
}
```