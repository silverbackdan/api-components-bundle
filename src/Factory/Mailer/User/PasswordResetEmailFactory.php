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

namespace Silverback\ApiComponentsBundle\Factory\Mailer\User;

use Silverback\ApiComponentsBundle\Entity\User\AbstractUser;
use Silverback\ApiComponentsBundle\Exception\InvalidArgumentException;
use Symfony\Component\Mime\RawMessage;

/**
 * @author Daniel West <daniel@silverback.is>
 */
final class PasswordResetEmailFactory extends AbstractUserEmailFactory
{
    public function create(AbstractUser $user, array $context = []): ?RawMessage
    {
        if (!$this->enabled) {
            return null;
        }

        $this->initUser($user);

        $token = $user->getNewPasswordConfirmationToken();
        if (!$token) {
            throw new InvalidArgumentException('A new password confirmation token must be set to send the `password reset` email');
        }

        $context['redirect_url'] = $this->getTokenUrl($token, $user->getUsername());

        return $this->createEmailMessage($context);
    }

    protected static function getContextKeys(): ?array
    {
        return array_merge(parent::getContextKeys(), [
            'redirect_url',
        ]);
    }

    protected function getTemplate(): string
    {
        return 'user_password_reset.html.twig';
    }
}
