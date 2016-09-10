<?php

/**
 * Class Cymbio_Test_Model_Resource_Informator
 */
class Cymbio_Test_Model_Resource_Informator extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('cymbio/informator', 'entity_id');
    }
}