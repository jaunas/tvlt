<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:cache:response-url')]
class CacheResponseUrlCommand extends Command
{
    const RESPONSE_CACHE_DIR = __DIR__ . '/../../response_cache/';
    const PROXY_URL = 'socks4://78.61.27.207:5678';
    const LNK_TV_REQUEST_URL = 'https://lnk.lt/api/video/video-config/137535';
    const LNK_BTV_REQUEST_URL = 'https://lnk.lt/api/video/video-config/137534';
    const LNK_2TV_REQUEST_URL = 'https://lnk.lt/api/video/video-config/95343';
    const LNK_INFO_TV_REQUEST_URL = 'https://lnk.lt/api/video/video-config/137748';
    const LNK_TV1_REQUEST_URL = 'https://lnk.lt/api/video/video-config/106791';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cacheAll($output);

        return Command::SUCCESS;
    }

    private function cacheAll(OutputInterface $output): void
    {
        $this->cache($output, self::LNK_TV_REQUEST_URL, 'lnk.tv');
        $this->cache($output, self::LNK_BTV_REQUEST_URL, 'lnk.btv');
        $this->cache($output, self::LNK_2TV_REQUEST_URL, 'lnk.2tv');
        $this->cache($output, self::LNK_INFO_TV_REQUEST_URL, 'lnk.info.tv');
        $this->cache($output, self::LNK_TV1_REQUEST_URL, 'lnk.tv1');
    }

    private function cache(OutputInterface $output, string $url, string $channel): void
    {
        $output->write('Caching ' . $channel);

        $attempt = 1;
        while ($attempt <= 20) {
            $output->write('.');
            $response = $this->attempt($url);
            if ($response) {
                file_put_contents(self::RESPONSE_CACHE_DIR . $channel . '.json', $response);
                $output->writeln(' Done!');
                return;
            }
            $attempt++;
        }

        $output->writeln(' Failed!');
    }

    private function attempt(string $url): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_PROXY, self::PROXY_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);

        return curl_exec($curl);
    }
}