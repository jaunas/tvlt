<?php

namespace App\Service\Config\DataType;

interface ConfigInterface
{
    /**
     * @return ChannelInterface[]
     */
    public function getChannels(): iterable;
    public function getProxyUrl(): ?string;
}
