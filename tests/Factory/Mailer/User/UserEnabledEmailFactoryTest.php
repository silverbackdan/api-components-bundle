<?php

/*
 * This file is part of the Silverback API Component Bundle Project
 *
 * (c) Daniel West <daniel@silverback.is>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Silverback\ApiComponentBundle\Tests\Factory\Mailer\User;

use Silverback\ApiComponentBundle\Entity\User\AbstractUser;
use Silverback\ApiComponentBundle\Factory\Mailer\User\UserEnabledEmailFactory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class UserEnabledEmailFactoryTest extends AbstractFinalEmailFactoryTest
{
    public function test_skip_user_validation_if_disabled(): void
    {
        $factory = new UserEnabledEmailFactory(
            $this->containerInterfaceMock,
            $this->eventDispatcherMock,
            'subject',
            false
        );
        $this->assertNull($factory->create(new class() extends AbstractUser {
        }));
    }

    public function test_redirect_url_context_added_and_html_template_passed(): void
    {
        $user = new class() extends AbstractUser {
        };
        $user
            ->setUsername('username')
            ->setEmailAddress('email@address.com');

        $factory = new UserEnabledEmailFactory(
            $this->containerInterfaceMock,
            $this->eventDispatcherMock,
            'subject',
            true,
            '/default-path'
        );

        $this->assertCommonMockMethodsCalled();

        $email = (new TemplatedEmail())
            ->to(Address::fromString('email@address.com'))
            ->subject('subject')
            ->htmlTemplate('@SilverbackApiComponent/emails/user_enabled.html.twig')
            ->context([
                'website_name' => 'my website',
                'user' => $user,
            ]);

        $this->assertEquals($email, $factory->create($user, ['website_name' => 'my website']));
    }
}