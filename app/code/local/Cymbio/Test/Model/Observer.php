<?php

/**
 * Class Cymbio_Test_Model_Observer
 */
class Cymbio_Test_Model_Observer
{

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

    public function addProductToCart(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getEvent()->getRequest();
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();

        if ((int)$request->getParam('use_cymbio')) {
            $this->sendApiRequest($product);
        }
    }

    /**
     * @return false|\Cymbio_Test_Model_Cymbio_Events_ConfigProcessor
     */
    private function getConfigProcessor()
    {
        return Mage::getModel("cymbio_test/cymbio_events_configProcessor");
    }

    /**
     * @return false|\Cymbio_Test_Model_Cymbio_Events_Builder
     */
    private function getRequestBuilder()
    {
        return Mage::getModel("cymbio_test/cymbio_events_builder");
    }

    /**
     * @param \Mage_Catalog_Model_Product $product
     */
    private function sendApiRequest(Mage_Catalog_Model_Product $product)
    {
        $params = (array)Mage::app()->getConfig()->getNode("default/add_to_cart_cymbio_request");
        $configProcessor = $this->getConfigProcessor();
        $store = Mage::app()->getStore();
        //Registering entities
        $configProcessor->registerEntity('store', $store);
        $configProcessor->registerEntity('product', $product);
        //Set config path
        $configProcessor->setConfigPath('default/add_to_cart_cymbio_request/params');
        $params['params'] = $configProcessor->process();

        $builder = $this->getRequestBuilder();
        /** @var Zend_Http_Client $client */
        $client = $builder->build($params);

        /** @var Zend_Http_Response $response */
        $response = $client->request();
        $this->registerCymbioResponse($response, $params);
    }

    /**
     * @param \Zend_Http_Response $response
     * @param array $requestParams
     * @return void
     */
    private function registerCymbioResponse(Zend_Http_Response $response, array $requestParams)
    {
        Mage::log([
            "response" => $response->getMessage(),
            "client" => $requestParams
        ],
            null,
            'cymbio_request.log'
        );
    }
}
