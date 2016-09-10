<?php

class Cymbio_Test_Model_Cymbio_Events_ConfigProcessor
{
    /**
     * @var string
     */
    private $configPath;

    /**
     * @var Mage_Core_Model_Abstract[]
     */
    private $entities;

    /**
     * @var string
     */
    private $specialSymbol = "%";

    /**
     * @param string $path
     */
    public function setConfigPath($path)
    {
        $this->configPath = $path;
    }

    /**
     * Register entities
     * @param string $alias
     * @param \Mage_Core_Model_Abstract $entity
     */
    public function registerEntity($alias, Mage_Core_Model_Abstract $entity)
    {
        $this->entities[$alias] = $entity;
    }

    /**
     * Process special field such as store.name
     * @return array
     */
    public function process()
    {
        $config = (array) Mage::app()->getConfig()->getNode($this->configPath);

        foreach ($config as $key => &$value) {
            if (preg_match(
                "/" . $this->specialSymbol . "(.*)"  . $this->specialSymbol. "/",
                $value,
                $matches
            )) {
                /** @var string $entityAlias */
                list($entityAlias, $field) = explode(".", $matches[1]);

                if (isset($entityAlias) && isset($field) && isset($this->entities[$entityAlias])) {
                    $value = $this->entities[$entityAlias]->getData($field);
                }
            }
        }

        return $config;
    }
}
<?php

class Cymbio_Test_Model_Cymbio_Events_ConfigProcessor
{
    /**
     * @var string
     */
    private $configPath;

    /**
     * @var Mage_Core_Model_Abstract[]
     */
    private $entities;

    /**
     * @var string
     */
    private $specialSymbol = "%";

    /**
     * @param string $path
     */
    public function setConfigPath($path)
    {
        $this->configPath = $path;
    }

    /**
     * Register entities
     * @param string $alias
     * @param \Mage_Core_Model_Abstract $entity
     */
    public function registerEntity($alias, Mage_Core_Model_Abstract $entity)
    {
        $this->entities[$alias] = $entity;
    }

    /**
     * Process special field such as store.name
     * @return array
     */
    public function process()
    {
        $config = (array) Mage::app()->getConfig()->getNode($this->configPath);

        foreach ($config as $key => &$value) {
            if (preg_match(
                "/" . $this->specialSymbol . "(.*)"  . $this->specialSymbol. "/",
                $value,
                $matches
            )) {
                /** @var string $entityAlias */
                list($entityAlias, $field) = explode(".", $matches[1]);

                if (isset($entityAlias) && isset($field) && isset($this->entities[$entityAlias])) {
                    $value = $this->entities[$entityAlias]->getData($field);
                }
            }
        }

        return $config;
    }
}
