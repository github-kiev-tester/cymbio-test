<?php

class  Cymbio_Test_Model_Api_Request implements iRequest
{
    /**
     * @param string $url
     * @param array $config
     * @param $paramsToSend
     */
    public function requestApi($url, $config, $paramsToSend)
    {
        /** @var Zend_Http_Client $client */
        $client = new Zend_Http_Client($url, $config);
        $client->setParameterPost($paramsToSend);
        $client->setMethod('POST');

        $client->request();
    }
}