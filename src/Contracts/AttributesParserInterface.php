<?php

namespace Jsl\Router\Contracts;

use JsonSerializable;

interface AttributesParserInterface extends JsonSerializable
{
    /**
     * Get all routes organized in groups
     *
     * @return array
     */
    public function getResult(): array;
}
