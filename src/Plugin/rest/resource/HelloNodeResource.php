<?php

namespace Drupal\hello\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Drupal\node\Entity\Node;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get node data in JSON.
 *
 * @RestResource(
 *   id = "hello_node_resource",
 *   label = @Translation("Hello Node Resource"),
 *   uri_paths = {
 *     "canonical" = "/v2/node/{apikey}/{nid}"
 *   }
 * )
 */
class HelloNodeResource extends ResourceBase {
  /**
   * A curent user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('current_user')
    );
  }

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * Responds to GET requests.
   *
   * Returns the node data if api key matches.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing node data.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function get($apikey = NULL, $nid = NULL) {
    // Uses current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    // Fetching the site api key.
    $required_api_key = \Drupal::config('system.site')->get('siteapikey');
    // Checking if nid in url is valid and of type page.
    $values = \Drupal::entityQuery('node')
      ->condition('nid', $nid)
      ->condition('type', 'page')
      ->execute();
    $node_exists = !empty($values);
    $node = '';
    // Checking if api key present in url matches the one saved in siteapikey
    // variable and whether the nid in url is valid or not.
    if (!strcmp($required_api_key, $apikey) && $node_exists) {
      $node_storage = \Drupal::entityTypeManager()->getStorage('node');
      $node = $node_storage->load($nid);
      $response = new ResourceResponse($node);
    }
    else {
      throw new AccessDeniedHttpException('access denied');
    }
    // cacheability metadata in render array
    $response->addCacheableDependency($node);

    return $response;
  }

}
