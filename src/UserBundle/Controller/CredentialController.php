<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Business\Service\UserCredentialService;

class CredentialController extends Controller
{

    private function getUserCredentialService() : UserCredentialService
    {
        return $this->get('user.credential.service');
    }

    public function updateAction(Request $request) : Response
    {
        try {
            $user = $this->getUserCredentialService()->updateUserByCredential($request->headers->get('access-token'), $request->headers->get('google-code'));

            return $this->getResponse(['authorized' => true, 'user' => $user], 200);
        } catch (\Exception $e) {
            return $this->getResponse(['finded' => false, "message" => $e->getMessage()], ($e->getCode() != 0) ? $e->getCode() : 500);
        }
    }

    private function getResponse(array $message, int $code) : Response
    {
        return new Response(json_encode($message, false), $code);
    }

}
