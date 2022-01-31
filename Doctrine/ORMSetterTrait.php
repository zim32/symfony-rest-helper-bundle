<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait ORMSetterTrait
{
    /**
     * Do not forget to set cascade persist and orphanRemoval=true in OneToMany side
     *
     * @param $data
     * @param string $propertyName Name of property that holds collection
     * @param callable $comparator First argument is submitted item, second argument is existing item
     * @param callable $factory First argument is submitted item
     * @param callable|null $removeFromOwning First argument is existing item to be deleted (owning item)
     */
    protected function handleOneToMany($data, string $propertyName, callable $comparator, callable $factory, ?callable $removeFromOwning = null)
    {
        if (!($data instanceof Collection)) {
            $data = new ArrayCollection($data);
        }

        // handle removing
        foreach ($this->{$propertyName} as $existing) {
            if (false === $data->exists(function($key, $val) use ($existing, $comparator) {
                    return $comparator($val, $existing);
                })) {

                $this->{$propertyName}->removeElement($existing);

                if ($removeFromOwning) {
                    $removeFromOwning($existing);
                }
            }
        }

        // handle addition
        foreach ($data as $newItem) {
            if (false === $this->{$propertyName}->exists(function($key, $item) use ($newItem, $comparator, $factory) {
                    return $comparator($newItem, $item);
                })) {

                $entity = $factory($newItem);

                $this->{$propertyName}->add($entity);
            }
        }
    }

    /**
     * @param mixed $data Submitted data (Converted by serialized into doctrine entities)
     * @param string $propertyName Name of property that holds collection (inverse side)
     * @param callable $comparator First argument is submitted item, second argument is existing item
     * @param callable $factory First argument is submitted
     * @param callable $removeFromOwning First argument is existing item to be deleted (owning item)
     * @param callable $addToOwning First argument is new item (owning item)
     */
    protected function handleManyToManyInverseSide($data, string $propertyName, callable $comparator, callable $factory, callable $removeFromOwning, callable $addToOwning)
    {
        if (!($data instanceof Collection)) {
            $data = new ArrayCollection($data);
        }

        // handle removing
        foreach ($this->{$propertyName} as $existing) {
            if (false === $data->exists(function($key, $val) use ($existing, $comparator) {
                    return $comparator($val, $existing);
                })) {
                $this->{$propertyName}->removeElement($existing);
                $removeFromOwning($existing);
            }
        }

        // handle addition
        foreach ($data as $newItem) {
            if (false === $this->{$propertyName}->exists(function($key, $item) use ($newItem, $comparator, $factory) {
                    return $comparator($newItem, $item);
                })) {

                $entity = $factory($newItem);

                $this->{$propertyName}->add($entity);
                $addToOwning($newItem);
            }
        }
    }
}