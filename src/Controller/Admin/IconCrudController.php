<?php

namespace App\Controller\Admin;

use App\Entity\Icon;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class IconCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Icon::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(100)
        ;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addHtmlContentToBody('
            <style>
                td span svg {
                    max-width: 32px;
                    fill: slategrey;
                }
            </style>
        ');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            SlugField::new('slug')
                ->setTargetFieldName('name'),
            CodeEditorField::new('svg'),
            TextField::new('svg')
                ->renderAsHtml()
                ->onlyOnIndex()
                ->setLabel('Preview'),
            DateTimeField::new('created_at')
                ->onlyOnIndex()
                ->setLabel('Added on'),
            AssociationField::new('categories')->setRequired(true),
            ArrayField::new('categories')->onlyOnIndex()
        ];
    }
}
