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

namespace ReduxWeb\AdvancedEmailGuard\Forms\Types;

use ReduxWeb\AdvancedEmailGuard\Forms\EmailForm;
use ReduxWeb\AdvancedEmailGuard\Forms\Form;

class SendToFriend extends Form implements EmailForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        return $this->getInput('action') === 'sendToMyFriend';
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->getInput('email');
    }

    /**
     * {@inheritDoc}
     */
    public function sendResponseWithError($type)
    {
        unset($type);
        die('0');
    }
}
