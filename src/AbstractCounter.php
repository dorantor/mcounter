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
     * @return int value after increase
     */
    public function inc($step = 1)
    {
        return $this->client->increment(
            $this->getKey(),
            $step,
            $this->getInitialValue(),
            $this->expiry
        );
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
     * Method for building cache key based on $item value
     *
     * @return string
     */
    abstract protected function getKey();
}