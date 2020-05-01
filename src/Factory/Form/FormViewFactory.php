<?php

/*
 * This file is part of the Silverback API Components Bundle Project
 *
 * (c) Daniel West <daniel@silverback.is>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Silverback\ApiComponentsBundle\Factory\Form;

use Silverback\ApiComponentsBundle\Dto\FormView;
use Silverback\ApiComponentsBundle\Entity\Component\Form;

/**
 * @author Daniel West <daniel@silverback.is>
 */
class FormViewFactory
{
    private FormFactory $formFactory;

    public function __construct(
        FormFactory $formFactory
    ) {
        $this->formFactory = $formFactory;
    }

    public function create(Form $component): FormView
    {
        $form = $this->formFactory->create($component)->getForm();

        return new FormView($form, $form->createView());
    }
}
