{**
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
 *}
<!DOCTYPE html>
<html lang="{$lang.iso_code|escape:'html':'UTF-8'}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex,nofollow">
    <title>{$appName|escape:'html':'UTF-8'}</title>
    <link rel="icon" type="image/png" href="{$basePath|escape:'html':'UTF-8'}logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="{$cssPath|escape:'html':'UTF-8'}bootstrap.min.css">
    <link rel="stylesheet" href="{$cssPath|escape:'html':'UTF-8'}bootstrap-switch.min.css">
    <link rel="stylesheet" href="{$cssPath|escape:'html':'UTF-8'}select2.min.css">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="{$cssPath|escape:'html':'UTF-8'}admin.css">
    <script>var app = {$jsVars}; {* This is JSON content *}</script>
    <script src="{$jsPath|escape:'html':'UTF-8'}jquery.min.js"></script>
    <script src="{$jsPath|escape:'html':'UTF-8'}bootstrap.bundle.min.js"></script>
    <script src="{$jsPath|escape:'html':'UTF-8'}bootstrap-switch.min.js"></script>
    <script src="{$jsPath|escape:'html':'UTF-8'}select2.min.js"></script>
    <script src="{$jsPath|escape:'html':'UTF-8'}jquery.mark.min.js"></script>
    <script src="{$jsPath|escape:'html':'UTF-8'}admin.js"></script>
    {block name='head'}{/block}
</head>
<body>
    {include file="$tplPath/admin/_partials/header.tpl"}
    {block name='content'}{/block}
</body>
</html>