<?php

/**
 * Class Cymbio_Test_Model_Observer
 */
class Cymbio_Test_Model_Observer
{
    /** @var array */
    private $apiParams = [];

    /** @var array */
    private $actionMap = array(
        'addToCart' => 'ADD_TO_CART'
    );

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    private function getExtraAddToCartButtonHtml(Mage_Catalog_Model_Product $product)
    {
        /** @var  $block */
        $block = Mage::app()->getLayout()
            ->createBlock('core/template')
            ->setProduct($product)
            ->setTemplate('cymbio/catalog/product/view/addtocart.phtml');

        return $block instanceof Mage_Core_Block_Template ? $block->toHtml() : "";
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addExtraButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getData("transport");

        if ($block->getNameInLayout() == "product.info.addtocart") {
            $product = $block->getProduct();

            if ($product instanceof Mage_Catalog_Model_Product && $product->isSalable()) {
                $transport->setHtml(
                    $transport->getHtml() . $this->getExtraAddToCartButtonHtml($product)
                );
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addProductToCart(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getEvent()->getRequest();
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();

        if ((int)$request->getParam('use_cymbio')) {
            try {
                $this->sendApiRequest($product, $this->actionMap['addToCart']);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'cymbio-api-calls.log', true);
            }
        }
    }

    public function logCymbioEvent(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();
        /** @var \Cymbio_Test_Model_Informator $model */
        $model = Mage::getModel('cymbio/informator');

        try {
            $model->setData(
                [
                    'event_type' => "ADD_TO_CART",
                    'product_id' => $product->getId(),
                    'product_price' => $product->getPrice(),
                    'product_description' => $product->getShortDescription()
                ]
            )
                ->save();
        } catch (Exception $e) {

        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     */
    private function sendApiRequest(Mage_Catalog_Model_Product $product, $actionType)
    {
        /** @var Cymbio_Test_Model_Api_Request $apiRequestResult */
        $apiRequestModel = Mage::getModel('cymbio/api_request');
        $this->prepareApiParamsBeforeRequest($product, $actionType);

        $apiRequestModel->requestApi(
            $this->apiParams['api_url'],
            $this->apiParams['config'],
            $this->apiParams['params_to_send']
        );
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     */
    private function prepareApiParamsBeforeRequest(Mage_Catalog_Model_Product $product, $actionType)
    {
        $url = Mage::getConfig()->getNode('default/cymbio_api_url/general/url');
        $paramsToSend = [
            'action' => $actionType,
            'query' => '',
            'store_id' => Mage::app()->getStore()->getId(),
            'product_id' => $product->getId(),

        ];
        $config = array(
            'adapter' => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_REFERER => Mage::getBaseUrl(),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                )
            ),
        );
        $this->apiParams['api_url'] = (string)$url;
        $this->apiParams['params_to_send'] = (string)$paramsToSend;
        $this->apiParams['config'] = (string)$config;
    }
}