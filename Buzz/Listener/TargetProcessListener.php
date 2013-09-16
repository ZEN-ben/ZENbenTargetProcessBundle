<?php

namespace ZENben\Bundle\TargetProcessBundle\Buzz\Listener;

use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Listener\ListenerInterface;

class TargetProcessListener implements ListenerInterface {

    private $username;
    private $password;
    protected $apiBaseUrl;

    public function __construct($apiBaseUrl, $username, $password) {
        $this->apiBaseUrl = $apiBaseUrl;
        $this->username = $username;
        $this->password = $password;
    }

    public function preSend(RequestInterface $request) {
        $raw = $request->getContent();
        $request->setContent(json_encode($raw));
        $request->setResource($this->apiBaseUrl . $request->getResource());
        $request->addHeader('Authorization: Basic ' . base64_encode($this->username . ':' . $this->password));
        $request->addHeader('Accept: application/json');
        $request->addHeader('Content-Type: application/json');
    }

    public function postSend(RequestInterface $request, MessageInterface $response) {
        $raw = $response->getContent();
        $content = json_decode($raw,true);
        
        if ( ! ($response->isInformational() || $response->isSuccessful() ) ) {
            throw new \Exception(
                sprintf('%s %s: %s', 
                    $response->getStatusCode(), 
                    $content['Status'], 
                    $content['Message']
                )
            );
        }
        $response->setContent($content);
    }

}
