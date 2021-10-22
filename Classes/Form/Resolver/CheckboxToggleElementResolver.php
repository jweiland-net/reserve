<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Form\Resolver;

use TYPO3\CMS\Backend\Form\Element\CheckboxElement;
use TYPO3\CMS\Backend\Form\Element\CheckboxToggleElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Form\NodeResolverInterface;

class CheckboxToggleElementResolver implements NodeResolverInterface
{
    protected $data;

    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        $this->data = $data;
    }

    public function resolve(): string
    {
        $parameterArray = $this->data['parameterArray'];
        if (class_exists(\TYPO3\CMS\Backend\Form\Element\CheckboxToggleElement::class)) {
            return CheckboxToggleElement::class;
        }
        return CheckboxElement::class;
    }
}
