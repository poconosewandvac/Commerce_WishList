<?php
namespace PoconoSewVac\Wishlist\Modules;
use modmore\Commerce\Modules\BaseModule;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class Wishlist extends BaseModule {

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_wishlist:default');
        return $this->adapter->lexicon('commerce_wishlist');
    }

    public function getAuthor()
    {
        return 'Tony Klapatch - Pocono Sew & Vac';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_wishlist.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_wishlist:default');

        // Add the xPDO package, so Commerce can detect the derivative classes
//        $root = dirname(dirname(__DIR__));
//        $path = $root . '/model/';
//        $this->adapter->loadPackage('commerce_wishlist', $path);

        // Add template path to twig
        /** @var ChainLoader $loader */
        $root = dirname(dirname(__DIR__));
        $loader = $this->commerce->twig->getLoader();
        $loader->addLoader(new FilesystemLoader($root . '/templates/'));
    }

    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];

//        $fields[] = new DescriptionField($this->commerce, [
//            'description' => $this->adapter->lexicon('commerce_wishlist.module_description'),
//        ]);

        return $fields;
    }
}
