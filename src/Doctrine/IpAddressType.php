<?php
namespace Lamansky\Api\Doctrine;
use Darsyn\IP\Doctrine\AbstractType;
use Lamansky\Api\IpAddress;

class IpAddressType extends AbstractType {
    protected function getIpClass () : string {
        return IpAddress::class;
    }

    protected function createIpObject ($ip) : IpAddress {
        return IpAddress::factory($ip);
    }
}
