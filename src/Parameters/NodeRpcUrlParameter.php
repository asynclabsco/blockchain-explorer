<?php

namespace App\Parameters;

use DomainException;

class NodeRpcUrlParameter
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        if(is_null($path) || !is_string($path)){
            throw new DomainException('JSON RPC Url must be specified');
        }

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
