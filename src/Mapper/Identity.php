<?php

namespace Ignis\Mapper;

use Memcached;
use Ignis\Entity;
use Ignis\Exception;

class Mapper
{
    private $storage;
    private $lifetime;
    private $burndown;

    public function __construct(Memecached $storage, int $lifetime, int $burndown)
    {
        $this->storage = $storage;
        $this->lifetime = $lifetime;
        $this->burndown = $burndown;
    }

    public function store(Entity\Identity $identity)
    {
        $data = [
            'payload' => $identity->getPayload(),
            'account' => $identity->getAccountId(),
            'token' => $identity->getToken(),
            'createdOn' => null,
            'activatedOn' => null,
        ];

        if ($identity->getCreationTime()) {
            $data['createdOn'] = $identity->getCreationTime()->getTimestamp();
            $expires = $identity->getCreationTime()->getTimestamp() + $this->lifetime;
        }

        if ($identity->getActivationTime()) {
            $data['activatedOn'] = $identity->getActivationTime()->getTimestamp();
            $expires = $identity->getActivationTime()->getTimestamp() + $this->burndown;
        }

        $this->storage->set($identity->getKey(), $data, $expires);
    }

    public function fetch(Entity\Identity $identity)
    {
        $data = $this->storage->get($identity->getKey());

        if (false === $data) {
            throw new Exception\EntityNotFound;
        }

        $identity->setPayload($data['payload']);
        $identity->setAccountId($data['account']);
        $identity->setToken($data['token']);
        $identity->setCreationTime((new DateTimeImmutable)->setTimestamp($data['createdOn']));
        
        if ($data['activatedOn']) {
            $identity->setActivationTime((new DateTimeImmutable)->setTimestamp($data['activatedOn']));
        }
    }
}
