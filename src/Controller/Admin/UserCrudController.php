<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {

        yield IdField::new('id')->hideOnForm();
        yield TextField::new('username');
 
        yield TextField::new('password')            ->hideOnIndex()
        ->hideOnDetail();
        
        $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_REP', 'ROLE_AEROPORT', 'ROLE_TRANSFER'];
        yield ChoiceField::new('roles')
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderAsBadges()
            /* ->renderExpanded() */;
    
        yield AssociationField::new('area');
        yield Field::new('phoneNumber');

    }
   
}
