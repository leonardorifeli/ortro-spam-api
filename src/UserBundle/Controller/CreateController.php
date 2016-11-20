<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Business\Service\UserService;

class CreateController extends Controller
{
    private function getUserService() : UserService
    {
        return $this->get('user.service');
    }

    public function buildAction(Request $request) : Response
    {
        try {
            $user = $this->getUserService()->createByRequest(json_decode($request->getContent()));
            return $this->getResponse(['user' => $user, 'isCreated' => true], 200);
        } catch (\Exception $e) {
            return $this->getResponse(['isCreated' => false, "message" => $e->getMessage()], $e->getCode());
        }
    }

    private function getResponse(array $message, int $code) : Response
    {
        return new Response(json_encode($message), $code);
    }

}
