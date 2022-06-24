<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\Controller\Setup;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class BaseGetItemSetup
{
    public function modifyQueryBuilder(QueryBuilder $qb, Request $request, AuthorizationCheckerInterface $authorizationChecker)
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