<?php namespace Helstern\Nomsky\DependencyInjection;

use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * Overrides the normal PhpFileLoader which does not work well with files loaded from inside a phar archive
 */
class PhpConfigResourceLoader extends PhpFileLoader
{
    /**
     * @param $path
     *
     * @return \Symfony\Component\Config\Resource\ResourceInterface
     */
    protected function createResourceObject($path)
    {
        return new FileExistenceResource($path);
    }

    /**
     * @param string $resource
     * @param null $type
     *
     * @return array|string
     */
    protected function loadAndRegisterResource($resource, $type = null)
    {
        $path = $this->locator->locate($resource);
        $this->setCurrentDir(dirname($path));
        $this->container->addResource(new FileExistenceResource($path));

        return $path;
    }


    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        // the container and loader variables are exposed to the included file below
        $container = $this->container;
        $loader = $this;

        $path = $this->loadAndRegisterResource($resource, $type);

        include $path;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
