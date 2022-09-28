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

use ReduxWeb\AdvancedEmailGuard\Forms\Form;
use ReduxWeb\AdvancedEmailGuard\Forms\MessageForm;

class WriteReview extends Form implements MessageForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        return $this->context->controller instanceof \ProductCommentsPostCommentModuleFrontController
            || ($this->context->controller instanceof \ProductCommentsDefaultModuleFrontController
                && $this->getInput('action') === 'add_comment');
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        if (\Tools::version_compare(_PS_VERSION_, '1.7.6', '>=')) {
            return $this->getInput('comment_content');
        }
        return $this->getInput('content');
    }

    /**
     * {@inheritDoc}
     */
    public function sendResponseWithError($type)
    {
        $this->module->sendJson(array(
            'success' => false,
            'errors' => array($this->module->getValidationError($type)),
        ), \Tools::version_compare(_PS_VERSION_, '1.7.6', '>='));
    }
}
