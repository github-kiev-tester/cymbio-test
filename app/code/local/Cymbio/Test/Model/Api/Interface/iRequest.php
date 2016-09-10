<?php

interface iRequest
{
    /**
     * @param $url string
     * @param $config array
     *
     */
    public function requestApi($url, $config, $paramsToSend);
}