<?php


namespace App\Tests\User;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserEntity(): void
    {
        $user = new User();

        // Test username
        $user->setUsername('JohnDoe');
        $this->assertEquals('JohnDoe', $user->getUsername());

        // Test email
        $user->setEmail('john@example.com');
        $this->assertEquals('john@example.com', $user->getEmail());

        // Test password
        $user->setPassword('Password123!');
        $this->assertEquals('Password123!', $user->getPassword());

        // Test avatar
        $user->setAvatar('avatar.jpg');
        $this->assertEquals('avatar.jpg', $user->getAvatar());

        // Test website
        $user->setWebsite('https://example.com');
        $this->assertEquals('https://example.com', $user->getWebsite());

        // Test description
        $user->setDescription('A short bio');
        $this->assertEquals('A short bio', $user->getDescription());

        // Test role
        $user->setRole('ROLE_USER');
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        // Test confirmed
        $user->setConfirmed(true);
        $this->assertTrue($user->getConfirmed());

        // Test confirmation token
        $user->setConfirmationToken('token123');
        $this->assertEquals('token123', $user->getConfirmationToken());

        // Test createdAt
        $now = new \DateTime();
        $user->setCreatedAt($now);
        $this->assertEquals($now, $user->getCreatedAt());
    }
}
