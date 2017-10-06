<?php

namespace api\models;

/**
 * Для передачи в response токена для REST
 * @package api\models
 */
class Token
{
    /**
     * Token constructor.
     * @param string $token
     */
    public function __construct($token = null)
    {
        $this->token = $token;
    }

    /**
     * @var string Значение токена для REST
     */
    public $token;
}