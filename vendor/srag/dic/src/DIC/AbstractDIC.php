<?php

namespace srag\DIC\SrProjectHelper\DIC;

use ILIAS\DI\Container;
use srag\DIC\SrProjectHelper\Database\DatabaseDetector;
use srag\DIC\SrProjectHelper\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\SrProjectHelper\DIC
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
