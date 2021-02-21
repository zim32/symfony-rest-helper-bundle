<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\Controller\Setup;


use Symfony\Component\HttpFoundation\Request;

class BaseDeleteItemSetup
{

    public function beforeFlush($entity, Request $request)
    {

    }

    public function afterFlush($entity, Request $request)
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