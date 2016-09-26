<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthController extends Controller
{

    private function getCredentialService()
    {
        return $this->get('core.credential.service');
    }

    public function authAction(Request $request)
    {
        $url = $this->getCredentialService()->getAuthUrl();

        if(!$this->getCredentialService()->credentialExist() || !$request->get('code')) {
            return $this->redirect($url);
        }

        $oauthCode = $request->get('code');

        $this->getCredentialService()->createCredential($oauthCode);
    }

}
