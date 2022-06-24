<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\Controller\Setup;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class BaseDeleteItemSetup
{

    public function beforeFlush($entity, Request $request, AuthorizationCheckerInterface $authorizationChecker)
    {

    }

    public function afterFlush($entity, Request $request, AuthorizationCheckerInterface $authorizationChecker)
    {

    }

    public function rollbackBeforeFlush($entity, Request $request)
    {

    }

    public function rollbackAfterFlush($entity, Request $request)
    {

    }

    public function requiredRole($entity)
    {
        return 'ROLE_ADMIN';
    }


}