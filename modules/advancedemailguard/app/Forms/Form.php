<?php
/**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2022 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace ReduxWeb\AdvancedEmailGuard\Forms;

abstract class Form
{
    /**
     * Form name.
     *
     * @var string
     */
    protected $name;

    /**
     * Shop context instance.
     *
     * @var \Context
     */
    protected $context;

    /**
     * Module instance.
     *
     * @var \Advancedemailguard
     */
    protected $module;

    /**
     * Create new form instance.
     *
     * @param string $name
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->context = \Context::getContext();
        $this->module = \Module::getInstanceByName('advancedemailguard');
    }

    /**
     * Get the form name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Check if the form has been submitted.
     *
     * @return bool
     */
    abstract public function isSubmitted();

    /**
     * Send response with the specified error type.
     *
     * @param string $type
     * @return void
     */
    public function sendResponseWithError($type)
    {
        $this->module->setValidationError($type);
        $this->module->redirectBack();
    }

    /**
     * Get trimmed input value by name.
     *
     * @param string $name
     * @return string
     */
    public function getInput($name)
    {
        return $this->inputExists($name) ? trim(\Tools::getValue($name)) : '';
    }

    /**
     * Check if input value exists.
     *
     * @param string $name
     * @return bool
     */
    public function inputExists($name)
    {
        return \Tools::getValue($name) !== false;
    }

    /**
     * Check if submit input exists.
     *
     * @param string $name
     * @return bool
     */
    public function submitExists($name)
    {
        return \Tools::isSubmit($name);
    }
}
