<?php

namespace App\DataFixtures;

use App\Story\CarStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CarStory::load();
    }
}
