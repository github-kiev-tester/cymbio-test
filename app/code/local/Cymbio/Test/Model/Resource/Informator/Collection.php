<?php

/**
 * Class Cymbio_Test_Model_Resource_Informator_Collection
 */
class Cymbio_Test_Model_Resource_Informator_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('cymbio/informator');
    }
}