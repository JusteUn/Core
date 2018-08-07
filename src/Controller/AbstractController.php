<?php
/**
 * This file is part of Berlioz framework.
 *
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2018 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

declare(strict_types=1);

namespace Berlioz\Core\Controller;

use Berlioz\Core\App\AbstractApp;
use Berlioz\Core\App\AppAwareTrait;
use Berlioz\Core\Exception\BerliozException;
use Berlioz\Core\Exception\ContainerException;
use Berlioz\Core\Package\TemplateEngine;
use Psr\Container\ContainerExceptionInterface;

abstract class AbstractController implements ControllerInterface
{
    use AppAwareTrait;

    /**
     * AbstractController constructor.
     *
     * @param \Berlioz\Core\App\AbstractApp $app
     */
    public function __construct(AbstractApp $app)
    {
        $this->setApp($app);
    }

    /**
     * __sleep() magic method.
     *
     * @throws \RuntimeException because unable to serialize a Controller object
     */
    public function __sleep(): array
    {
        throw new \RuntimeException('Unable to serialize a Controller object');
    }

    /**
     * Get service.
     *
     * @param string $id
     *
     * @return mixed
     * @throws \RuntimeException if a error occurred in Berlioz Framework
     * @throws \Berlioz\Core\Exception\BerliozException
     */
    protected function getService(string $id)
    {
        try {
            if (is_null($serviceContainer = $this->getApp()->getServiceContainer())) {
                throw new BerliozException('No service container defined in application');
            }
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerException('Service container error', 0, $e);
        }

        return $serviceContainer->get($id);
    }

    /**
     * Do render of templates.
     *
     * @param string  $name      Filename of template
     * @param mixed[] $variables Variables for template
     *
     * @return string Output content
     * @throws \Berlioz\Core\Exception\BerliozException
     */
    protected function render(string $name, array $variables = []): string
    {
        $templateEngine = $this->getService('templating');

        if (!($templateEngine instanceof TemplateEngine)) {
            throw new ContainerException(sprintf('Service "templating" must be implements %s interface', TemplateEngine::class));
        }

        return $templateEngine->render($name, $variables);
    }
}