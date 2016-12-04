<?php

namespace UserBundle\Business\Adapter;

use UserBundle\Entity\UserMessage;
use UserBundle\Entity\User;

abstract class UserMessageAdapter
{

    public static final function buildHeader(UserMessage $entity, User $user, string $messageId, $headers) : UserMessage
    {
        if(is_null($entity->getProviderId())) 
            $entity->setProviderId($messageId);

        $entity->setUser($user);
        if(is_null($entity->getIsDeleted())) 
            $entity->setIsDeleted(false);
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        $headers = self::getValidHeader($entity, $headers);
        $entity->setHeaderInformation(json_encode($headers));

        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $date = (array_key_exists('Date', $headers)) ? new \DateTime($headers['Date'], $timezone) : new \DateTime();
        $date->setTimeZone($timezone);

        $entity->setDate($date);

        $to = (array_key_exists('To', $headers)) ? self::validateFromAndTo($headers['To']) : "";
        $entity->setTo($to);

        $from = (array_key_exists('From', $headers)) ? self::validateFromAndTo($headers['From']) : "";
        $entity->setFrom($from);

        return $entity;
    }

    private static final function validateFromAndTo(string $message) : string
    {
        $result = substr($message, strpos($message, "<"), strlen($message));
        return str_replace("<", "", str_replace(">", "", $result));
    }

    private static final function getValidHeader(UserMessage $entity, $headers) : array
    {
        if(count($headers) < 1) return;

        $newHeaders = [];
        foreach($headers as $header) {
            if(!in_array($header->name, self::getValidHeaders())) 
                continue;
            $newHeaders[$header->name] = $header->value;
        }

        return $newHeaders;
    }

    private static final function getValidHeaders() : array
    {
        return [
            "Received",
            "From",
            "Date",
            "Subject",
            "To",
            "Return-Path",
            "Message-ID",
        ];
    }

}
