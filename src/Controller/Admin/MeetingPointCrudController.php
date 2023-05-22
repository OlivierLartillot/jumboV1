<?php

namespace App\Controller\Admin;

use App\Entity\MeetingPoint;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MeetingPointCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MeetingPoint::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
