<?php

namespace Rbs\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Rbs\Bundle\CoreBundle\Entity\Bundle;

class LoadBundleData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $modules = array(1 => 'Sales Module', 2 => 'Purchase Module');
        foreach ($modules as $id => $module) {
            $bundle = new Bundle();
            $bundle->setId($id);
            $bundle->setName($module);
            $manager->persist($bundle);
        }

        $manager->flush();
    }
}