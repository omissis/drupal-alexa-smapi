<?php declare(strict_types=1);

namespace Drupal\alexa_smapi;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ImmutableConfig;
use Omissis\AlexaSdk\Serializer\Deserializer;
use Omissis\AlexaSdk\Serializer\Serializer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class SdkFactory implements ContainerAwareInterface
{
  use ContainerAwareTrait;

  /**
   * @var ClientInterface
   */
  private $client;

  /**
   * @var RequestFactoryInterface
   */
  private $request_factory;

  /**
   * @var Serializer
   */
  private $serializer;

  /**
   * @var Deserializer
   */
  private $deserializer;

  /**
   * @var ImmutableConfig
   */
  private $config;

  public function __construct(
    ClientInterface $client,
    RequestFactoryInterface $request_factory,
    Serializer $serializer,
    Deserializer $deserializer,
    ConfigFactory $config_factory
  ) {
    $this->client = $client;
    $this->request_factory = $request_factory;
    $this->serializer = $serializer;
    $this->deserializer = $deserializer;
    $this->config = $config_factory->get('alexa_smapi.settings');
  }

  public function get(): Sdk
  {
    return new Sdk(
      $this->client,
      $this->request_factory,
      $this->serializer,
      $this->deserializer,
      $this->config->get('api_base_url'),
      $this->config->get('oauth_token')
    );
  }
}
