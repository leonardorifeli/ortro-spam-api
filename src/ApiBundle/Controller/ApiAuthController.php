<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiAuthController extends Controller
{

    public function authAction(Request $request)
    {
        return $this->render('ApiBundle:Default:index.html.twig');
    }

}
