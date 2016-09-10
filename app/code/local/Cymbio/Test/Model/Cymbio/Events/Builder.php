<?php

class Cymbio_Test_Model_Cymbio_Events_Builder
{
    /** @var \Zend_Http_Client_Adapter_Curl  */
    private $curlAdapter;

    /**
     * @return \Zend_Http_Client_Adapter_Curl
     */
    private function getZendHttpAdapter()
    {
        if (!$this->curlAdapter) {
            $this->curlAdapter = new Zend_Http_Client_Adapter_Curl();
        }

        return $this->curlAdapter;
    }

    /**
     * Create Zend Http Client
     * @return \Zend_Http_Client
     */
    private function createZendHttpClient()
    {
        /** @TODO: move this to factory */
        $client = new Zend_Http_Client();
        $client->setAdapter($this->getZendHttpAdapter());
        return $client;
    }

    /**
     * @param array $params
     * @throws \Exception
     */
    protected function validateParams(array $params)
    {
        if (!isset($params['uri'])) {
            throw new Exception("Cannot find request uri");
        }

        if (!isset($params['method'])) {
            throw new Exception("Cannot determine http method");
        }
    }

    /**
     * @param array $params
     * @return \Zend_Http_Client
     */
    public function build(array $params)
    {
        $this->validateParams($params);
        $client = $this->createZendHttpClient();
        $client->setUri($params['uri']);
        $client->setMethod($params['method']);

        if (isset($params['params'])) {
            $client->setParameterPost($params['params']);
        }

        return $client;
    }
}<?php

class Cymbio_Test_Model_Cymbio_Events_Builder
{
    /** @var \Zend_Http_Client_Adapter_Curl  */
    private $curlAdapter;

    /**
     * @return \Zend_Http_Client_Adapter_Curl
     */
    private function getZendHttpAdapter()
    {
        if (!$this->curlAdapter) {
            $this->curlAdapter = new Zend_Http_Client_Adapter_Curl();
        }

        return $this->curlAdapter;
    }

    /**
     * Create Zend Http Client
     * @return \Zend_Http_Client
     */
    private function createZendHttpClient()
    {
        /** @TODO: move this to factory */
        $client = new Zend_Http_Client();
        $client->setAdapter($this->getZendHttpAdapter());
        return $client;
    }

    /**
     * @param array $params
     * @throws \Exception
     */
    protected function validateParams(array $params)
    {
        if (!isset($params['uri'])) {
            throw new Exception("Cannot find request uri");
        }

        if (!isset($params['method'])) {
            throw new Exception("Cannot determine http method");
        }
    }

    /**
     * @param array $params
     * @return \Zend_Http_Client
     */
    public function build(array $params)
    {
        $this->validateParams($params);
        $client = $this->createZendHttpClient();
        $client->setUri($params['uri']);
        $client->setMethod($params['method']);

        if (isset($params['params'])) {
            $client->setParameterPost($params['params']);
        }

        return $client;
    }
}