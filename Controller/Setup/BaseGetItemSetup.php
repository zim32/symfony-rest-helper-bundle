<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\Controller\Setup;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class BaseGetItemSetup
{

    public function modifyQueryBuilder(QueryBuilder $qb, Request $request)
    {

    }

    public function requiredRole($entity)
    {
        return null;
    }

    public function overrideGroup()
    {
        return null;
    }
}