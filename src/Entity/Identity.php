<?php

namespace Ignis\Entity;

use DateTimeImmutable;

class Identity
{
    const TOKEN_SIZE = 16;

    private $accountId;
    private $payload;
    private $token;
    private $createdOn;
    private $activatedOn;

    public function __construct()
    {
        $this->createdOn = new DateTimeImmutable;
    }

    public function setAccountId(int $accountId)
    {
        $this->accountId = $accountId;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function setPayload(string $payload)
    {
        $this->payload = $payload;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function genrateToken()
    {
        $this->token = bin2hex(random_bytes(self::TOKEN_SIZE));
    }

    public function getKey(): string
    {
        return "{$this->accountId}-{$this->token}";
    }

    public function setCreationTime(DateTimeImmutable $createdOn)
    {
        $this->createdOn = $createdOn;
    }

    public function getCreationTime(): DateTimeImmutable
    {
        return $this->createdOn;
    }

    public function setActivationTime(DateTimeImmutable $activatedOn)
    {
        $this->activatedOn = $activatedOn;
    }

    public function getActivationTime()
    {
        return $this->activatedOn;
    }
}
