<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait ORMSetterTrait
{
    /**
     * @param mixed $data Submitted data (Converted by serialized into doctrine entities)
     * @param string $propertyName Inverse property name
     * @param string $propertyName Owning property name
     * @param callable $comparator First argument is submitted item, second argument is existing item
     */
    protected function handleOneToMany($data, string $propertyName, string $owningPropertyName, callable $comparator)
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
                $owningSetter = 'set' . ucfirst($owningPropertyName);
                $existing->$owningSetter(null);
            }
        }

        // handle addition
        foreach ($data as $newItem) {
            if (false === $this->{$propertyName}->exists(function($key, $item) use ($newItem, $comparator) {
                    return $comparator($newItem, $item);
                })) {

                $this->{$propertyName}->add($newItem);
                $owningSetter = 'set' . ucfirst($owningPropertyName);
                $newItem->$owningSetter($this);
            }
        }
    }

    /**
     * @param mixed $data Submitted data (Converted by serialized into doctrine entities)
     * @param string $propertyName Inverse property name
     * @param string $owningPropertyName Owning property name 
     * @param callable $comparator First argument is submitted item, second argument is existing item
     */
    protected function handleManyToManyInverseSide($data, string $propertyName, string $owningPropertyName, callable $comparator)
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
                $owningGetter = 'get' . ucfirst($owningPropertyName);
                $existing->$owningGetter()->removeElement($this);
            }
        }

        // handle addition
        foreach ($data as $newItem) {
            if (false === $this->{$propertyName}->exists(function($key, $item) use ($newItem, $comparator) {
                    return $comparator($newItem, $item);
                })) {

                $this->{$propertyName}->add($newItem);
                $owningGetter = 'get' . ucfirst($owningPropertyName);
                $newItem->$owningGetter()->add($this);
            }
        }
    }
}