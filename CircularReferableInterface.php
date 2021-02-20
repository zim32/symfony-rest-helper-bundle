<?php

namespace Zim\Bundle\SymfonyRestHelperBundle;


interface CircularReferableInterface
{

    public function representCircularDependency($format, $context);

}