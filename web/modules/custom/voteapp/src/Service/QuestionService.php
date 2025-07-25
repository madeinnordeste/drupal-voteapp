<?php

namespace Drupal\voteapp\Service;


use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuestionService
{

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager)
  {
    $this->entityTypeManager = $entityTypeManager;
  }

  public function list($page = 1, $limit = 5)
  {

    $page = max(1, (int) $page);
    $limit = max(0, (int) $limit);

    $offset = ($page - 1) * $limit;

    $storage = $this->entityTypeManager->getStorage('question');

    $total_query = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', 1);

    $total = $total_query->count()->execute();

    $total_pages = (int) ceil($total / $limit);

    if ($page > $total_pages) {
      throw new NotFoundHttpException('Page not found');
    }

    $query = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', 1)
      ->range($offset, $limit);

    $ids = $query->execute();

    $questions = $storage->loadMultiple($ids);

    $data = [];
    foreach ($questions as $question) {
      $data[] = $this->buildData($question);
    }

    $list = [
      'page' => $page,
      'per_page' => $limit,
      'total' => $total,
      'total_pages' => $total_pages,
      'data' => $data,
    ];

    return $list;
  }

  public function buildData(EntityInterface $question, bool $includeAnswers = false)
  {
    $data = [
      //'id' => $question->id(),
      'identifier' => $question->get('identifier')->value,
      'title' => $question->label(),
    ];

    if ($includeAnswers) {

      $data['answers'] = [];

      foreach ($question->getAnswers() as $answer) {
        $id = $answer->id();
        $title = $answer->get('title')->value;
        $description = $answer->get('description')->value;
        $imageURL = $answer->getImageURL();

        $data['answers'][] = compact('id', 'title', 'description', 'imageURL');
      }
    }

    return $data;
  }
}
