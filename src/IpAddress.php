<?php
namespace Lamansky\Api;
use Darsyn\IP\Strategy\Compatible;
use Darsyn\IP\Strategy\EmbeddingStrategyInterface as Strategy;
use Darsyn\IP\Version\Multi;

class IpAddress extends Multi {
    public static function factory ($ip, Strategy $strategy = null) : self {
        return parent::factory($ip, new Compatible());
    }
}
