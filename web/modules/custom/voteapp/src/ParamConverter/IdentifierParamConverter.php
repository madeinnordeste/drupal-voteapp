<?php

namespace Drupal\voteapp\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Drupal\voteapp\Entity\Question;

class IdentifierParamConverter implements ParamConverterInterface
{

  public function applies($definition, $name, Route $route)
  {
    return isset($definition['type']) && $definition['type'] === 'question_by_identifier';
  }

  public function convert($value, $definition, $name, array $defaults)
  {
    $storage = \Drupal::entityTypeManager()->getStorage('question');
    $results = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('identifier', $value)
      ->execute();

    if (!empty($results)) {
      $id = reset($results);
      return $storage->load($id);
    }

    return NULL;
  }
}
