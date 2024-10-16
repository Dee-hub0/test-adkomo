<?php

namespace App\DataFixtures;

use App\Story\UserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserStory::load();
    }
}
