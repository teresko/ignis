<?php

namespace Ignis\Service;

use Ignis\Entity;
use Ignis\Mapper;
use Memcached;

class Authentication
{
    private $storage;
    private $lifetime;
    private $burndown;

    public function __construct(Memcached $storage, int $lifetime, int $burndown)
    {
        $this->storage = $storage;
        $this->lifetime = $lifetime;
        $this->burndown = $burndown;
    }

    public function createIdentity(int $accountId, string $payload): Entity\Identity
    {
        $identity = new Entity\Identity;
        $identity->genrateToken();

        $identity->setAccountId($accountId);
        $identity->setPayload($payload);

        $mapper = new Mapper\Identity($this->storage, $this->lifetime, $this->burndown);
        $mapper->store($identity);

        return $identity;
    }

    public function retrieveIdentity(string $token): Entity\Identity
    {
        $identity = new Entity\Identity;
        $identity->setToken($token);

        try {
            $mapper = new Mapper\Identity($this->storage, $this->lifetime, $this->burndown);
            $mapper->fetch($identity);
        } catch (Exception\EntityNotFound $exception) {
            throw new Exception\IdentityNotFound;
        }

        if (null === $identity->getActivationTime()) {
            $identity->setActivationTime(new DateTimeImmutable);
            $mapper->store($identity);
        }

        return $identity;
    }
}
