<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\Serializer;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;
use Zim\Bundle\SymfonyRestHelperBundle\Helper\StringHelper;

class ResourceDenormalizer implements DenormalizerInterface
{
    const TYPE_SINGLE_ID = 0;
    const TYPE_ARRAY_OF_IDS = 1;
    const TYPE_SINGLE_OBJECT = 2;
    const TYPE_ARRAY_OF_OBJECTS = 3;

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
        $dataType = $this->detectDataType($data, $type);

        if ($dataType === null) {
            throw new \Exception('Unsupported data type');
        }

        switch ($dataType) {
            case self::TYPE_SINGLE_ID:
                return $this->em->find($type, $data);
            case self::TYPE_ARRAY_OF_IDS:
                $fqcn = str_replace('[]', '', $type);
                $query = $this->em->createQuery('SELECT m FROM '.$fqcn.' m WHERE m.id IN(:ids)');
                $query->setParameter('ids', $data);
                return $query->getResult();
            case self::TYPE_SINGLE_OBJECT:
                return $this->em->find($type, $data['id']);
            default:
                throw new \Exception('Can not denormalize value. Unsupported value type');
        }
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        $dataType = $this->detectDataType($data, $type);

        return $dataType !== null;
    }

    protected function detectDataType($data, $format)
    {
        switch (true) {
            case $this->isSingleIDType($data, $format):
                return self::TYPE_SINGLE_ID;
            case $this->isArrayOfIDsType($data, $format):
                return self::TYPE_ARRAY_OF_IDS;
            case $this->isSingleObjectType($data, $format):
                return self::TYPE_SINGLE_OBJECT;
            case $this->isArrayOfObjectsType($data, $format):
                return self::TYPE_ARRAY_OF_OBJECTS;
        }

        return null;
    }

    protected function isSingleIDType($data, $type)
    {
        if (false === is_scalar($data)) {
            return false;
        }

        if (false === class_exists($type)) {
            return false;
        }

        $metadata = $this->em->getClassMetadata($type);

        if (!$metadata) {
            return false;
        }

        return true;
    }

    protected function isArrayOfIDsType($data, $type)
    {
        if (false === is_array($data)) {
            return false;
        }

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

        // check values are scalar values
        foreach ($data as $item) {
            if (!is_scalar($item)) {
                return false;
            }
        }

        // check metadata
        $fqcn     = str_replace('[]', '', $type);
        $metadata = $this->em->getClassMetadata($fqcn);

        if (!$metadata) {
            return false;
        }

        return true;
    }

    protected function isSingleObjectType($data, $type)
    {
        if (false === is_array($data)) {
            return false;
        }

        if (false === array_key_exists('id', $data)) {
            return false;
        }

        if (false === is_numeric($data['id'])) {
            return false;
        }

        $metadata = $this->em->getClassMetadata($type);

        if (!$metadata) {
            return false;
        }

        return true;
    }

    protected function isArrayOfObjectsType($data, $type)
    {
        return false;
    }
}