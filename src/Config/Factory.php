<?php

namespace srag\Plugins\SrProjectHelper\Config;

use ilSrProjectHelperPlugin;
use srag\ActiveRecordConfig\SrProjectHelper\Config\AbstractFactory;
use srag\Plugins\SrProjectHelper\Config\Form\FormBuilder;
use srag\Plugins\SrProjectHelper\Utils\SrProjectHelperTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrProjectHelper\Config
 */
final class Factory extends AbstractFactory
{

    use SrProjectHelperTrait;

    const PLUGIN_CLASS_NAME = ilSrProjectHelperPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    protected function __construct()
    {
        parent::__construct();
    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param ConfigCtrl $parent
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(ConfigCtrl $parent) : FormBuilder
    {
        $form = new FormBuilder($parent);

        return $form;
    }
}
