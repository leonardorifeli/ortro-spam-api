<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CredentialController extends Controller
{

    private function getUserService()
    {
        return $this->get('user.service');
    }

    public function updateAction(Request $request) : Response
    {
        try {
            $accessToken = $request->header->get('access_token');
            $googleCode = $request->header->get('google_code');

            $user = $this->getUserService()->updateUserByCredential($accessToken, $googleCode);

            if(is_string($user))
                return $this->getResponse(['authorized' => false, 'url' => $user, "finded" => !!($user)], 200);

            return $this->getResponse(['authorized' => true, 'user' => $user, "finded" => !!($user)], 200);
        } catch (\Exception $e) {
            return $this->getResponse(['finded' => false, "message" => $e->getMessage()], ($e->getCode() != 0) ? $e->getCode() : 500);
        }
    }

    private function getResponse(array $message, int $code) : Response
    {
        return new Response(json_encode($message, false), $code);
    }

}
