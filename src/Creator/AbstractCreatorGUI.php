<?php

namespace srag\Plugins\SrProjectHelper\Creator;

use ILIAS\BackgroundTasks\Implementation\Bucket\BasicBucket;
use ILIAS\GlobalScreen\Identification\IdentificationProviderInterface;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\TopItem\TopParentItem;
use ilSrProjectHelperPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrProjectHelper\DICTrait;
use srag\Plugins\SrProjectHelper\Creator\Form\AbstractCreatorFormBuilder;
use srag\Plugins\SrProjectHelper\Creator\Task\DownloadOutputTask;
use srag\Plugins\SrProjectHelper\Utils\SrProjectHelperTrait;

/**
 * Class AbstractCreatorGUI
 *
 * @package srag\Plugins\SrProjectHelper\Creator
 */
abstract class AbstractCreatorGUI
{

    use DICTrait;
    use SrProjectHelperTrait;

    const CMD_CREATE = "create";
    const CMD_FORM = "form";
    /**
     * @var string
     *
     * @abstract
     */
    const LANG_MODULE = "";
    const PLUGIN_CLASS_NAME = ilSrProjectHelperPlugin::class;
    const START_CMD = self::CMD_FORM;


    /**
     * AbstractCreatorGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param IdentificationProviderInterface $if
     * @param TopParentItem                   $parent
     *
     * @return isItem
     */
    public static function getMenuItem(IdentificationProviderInterface $if, TopParentItem $parent) : isItem
    {
        return self::dic()->globalScreen()->mainBar()->link($if->identifier(ilSrProjectHelperPlugin::PLUGIN_ID . "_"
            . static::LANG_MODULE))
            ->withParent($parent->getProviderIdentification())->withTitle(self::plugin()
                ->translate("title", static::LANG_MODULE))->withAction(str_replace("\\", "%5C", self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                static::class
            ], static::START_CMD)));
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        if (!self::srProjectHelper()->currentUserHasRole()) {
            die();
        }

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CREATE:
                    case self::CMD_FORM:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @inheritDoc
     */
    protected function buildAndRunTask(array $data) : void
    {
        $bucket = new BasicBucket();

        $bucket->setUserId(self::srProjectHelper()->ilias()->users()->getUserId());

        $task = self::dic()->backgroundTasks()->taskFactory()->createTask($this->getTaskClass(), [json_encode($data)]);

        if ($this->shouldDownloadOutput()) {
            $task = self::dic()->backgroundTasks()->taskFactory()->createTask(DownloadOutputTask::class, [$task]);
        }

        $bucket->setTask($task);
        $bucket->setTitle(self::plugin()->translate("task_title", static::LANG_MODULE, [$data["name"]]));

        self::dic()->backgroundTasks()->taskManager()->run($bucket);
    }


    /**
     *
     */
    protected function create() : void
    {
        $form = $this->getCreatorFormBuilder();

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        $data = $form->getData2();

        $this->buildAndRunTask($data);

        ilUtil::sendSuccess(self::plugin()->translate("created", static::LANG_MODULE, [$data["name"]]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_FORM);
    }


    /**
     *
     */
    protected function form() : void
    {
        $form = $this->getCreatorFormBuilder();

        self::output()->output($form, true);
    }


    /**
     * @return AbstractCreatorFormBuilder
     */
    protected abstract function getCreatorFormBuilder() : AbstractCreatorFormBuilder;


    /**
     * @return string
     */
    protected abstract function getTaskClass() : string;


    /**
     *
     */
    protected function setTabs() : void
    {

    }


    /**
     * @return bool
     */
    protected abstract function shouldDownloadOutput() : bool;
}
