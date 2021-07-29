<?php

/*
 * This file is part of the dorantor/mcounter package.
 */

namespace Dorantor;

abstract class AbstractCounter
{
    /**
     * @var \Memcached
     */
    private $client;

    /**
     * @var mixed
     */
    protected $item;

    /**
     * @var int
     */
    protected $expiry;

    /**
     * AbstractCounter constructor.
     *
     * @param mixed         $item
     * @param \Memcached    $client
     * @param int           $expiry
     */
    public function __construct($item, \Memcached $client, $expiry = 0)
    {
        $this->setClient($client);
        $this->item = $item;
        $this->expiry = $expiry;
    }

    /**
     * Set memcached client object
     *
     * @param \Memcached $client
     */
    public function setClient(\Memcached $client)
    {
        $this->client = $client;
    }

    /**
     * Get current counter value
     *
     * @return int
     */
    public function value()
    {
        $value = $this->client->get($this->getKey());
        if (false === $value) {
            $value = $this->getInitialValue();
        }

        return $value;
    }

    /**
     * Increase counter
     *
     * @param int $step
     *
     * @return int|bool value after increase or false on fail
     */
    public function inc($step = 1)
    {
        return $this->client->increment(
            $this->getKey(),
            $step,
            $this->getInitialValue() + 1,
            $this->getExpiry()
        );
    }

    /**
     * Update expriration time
     *
     * @link http://php.net/manual/en/memcached.touch.php
     * @return bool true on success
     */
    public function touch()
    {
        return $this->client->touch(
            $this->getKey(),
            $this->getExpiry()
        );
    }

    /**
     * Increase counter and update expiry at the same time.
     *
     * @param int $step
     * @see inc()
     *
     * @return bool|int new value or false if increase failed
     */
    public function incWithTouch($step = 1)
    {
        $new = $this->inc($step);
        if (false !== $new) {
            $this->touch();
        }

        return $new;
    }

    /**
     * Initial counter value
     * Could be redefined in descendants
     *
     * @return int
     */
    protected function getInitialValue()
    {
        return 0;
    }

    /**
     * Get expiry value
     *
     * @return int
     */
    protected function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Method for building cache key based on $item value
     *
     * @return string
     */
    abstract protected function getKey();
}
