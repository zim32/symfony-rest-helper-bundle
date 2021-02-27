<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\Serializer;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Zim\Bundle\SymfonyRestHelperBundle\Helper\StringHelper;

class ResourceDenormalizer implements DenormalizerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (is_scalar($data)) {
            return $this->em->find($type, $data);
        } else if (is_array($data)) {
            $fqcn = str_replace('[]', '', $type);
            $query = $this->em->createQuery('SELECT m FROM '.$fqcn.' m WHERE m.id IN(:ids)');
            $query->setParameter('ids', $data);
            return $query->getResult();
        } else {
            throw new \Exception('Can not denormalize value. Unsupported value type');
        }
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        if (is_scalar($data)) {
            if (false === class_exists($type)) {
                return false;
            }

            $metadata = $this->em->getClassMetadata($type);

            if (!$metadata) {
                return false;
            }
        }

        if (is_array($data)) {
            if (count($data) === 0) {
                return false;
            }

            if (false === StringHelper::endsWith($type, '[]')) {
                return false;
            }

            // check array is real array (not associative)
            $keys = array_keys($data);

            if ($keys !== range(0, count($data) - 1)) {
                return false;
            }

            $fqcn     = str_replace('[]', '', $type);
            $metadata = $this->em->getClassMetadata($fqcn);

            if (!$metadata) {
                return false;
            }
        }

        return is_scalar($data) || is_array($data);
    }
}