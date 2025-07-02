<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Entity\StaticUrlSource;
use App\Repository\CacheRepository;
use App\Service\Config\DataType\ConfigInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StreamController extends AbstractController
{
    #[Route('/master.m3u8')]
    public function list(ConfigInterface $config): Response
    {
        return $this->render('stream/list.m3u8.twig', [
            'channels' => $config->getChannels(),
            'hostUrl' => $this->getParameter('host_url'),
        ]);
    }

    #[Route('/channel/{tvgId:channel}.{_format}', requirements: [
        'tvgId' => '.+',
        '_format' => 'm3u8',
    ])]
    public function channel(CacheRepository $cacheRepository, Channel $channel): Response
    {
        if (($staticSource = $channel->getUrlSource()) instanceof StaticUrlSource) {
            return $this->redirect($staticSource->getStreamUrl());
        }

        $cache = $cacheRepository->findByChannel($channel);
        return $this->redirect($cache->getStreamUrl());
    }
}