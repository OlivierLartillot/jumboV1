<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {


        $package = new Package(new EmptyVersionStrategy());

        // gestion local en env dev ou ligne prod

        if ($_ENV['APP_ENV']  == 'dev' ){
            $uploadPath = $package->getUrl('public\images\qrcode\\');  
        } else{
            $uploadPath = $package->getUrl('public/images/qrcode/');  
        }

        $path = $package->getUrl('/images/qrcode/');




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

        yield ImageField::new('qrCode')->setUploadDir($uploadPath)->setBasePath($path);

       
    }
   
}
