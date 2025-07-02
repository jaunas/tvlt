<?php

namespace App\Entity;

use App\Service\Config\DataType\UrlSourceInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
abstract class UrlSource implements UrlSourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
}
