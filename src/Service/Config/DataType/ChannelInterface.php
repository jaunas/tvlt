<?php

namespace App\Service\Config\DataType;

interface ChannelInterface
{
    public function getTvgName(): string;
    public function getTvgId(): string;
    public function getUrlSource(): UrlSourceInterface;
    public function setConfig(ConfigInterface $config): ChannelInterface;
}
