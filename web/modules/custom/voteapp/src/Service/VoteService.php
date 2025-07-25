<?php

namespace Drupal\voteapp\Service;


use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VoteService
{

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager)
  {
    $this->entityTypeManager = $entityTypeManager;
  }


  public function createVote(int $questionId, int $answerId, int $userId = 0)
  {

    $userId = $userId ?? \Drupal::currentUser()->id();

    $this->checkUserHasVote($questionId, $userId);

    $this->checkAnswerExists($questionId, $answerId);

    try {
      $storage = $this->entityTypeManager->getStorage('vote');
      $vote = $storage->create([
        'question' => (int)$questionId,
        'answer' => (int)$answerId,
        'user' => (int)$userId
      ]);
      $vote->save();
      return $vote;
    } catch (EntityStorageException $e) {
      $this->logger->error('VoteService->createVote(): @message', ['@message' => $e->getMessage()]);
      return null;
    }
  }

  public function checkUserHasVote(int $questionId, int $userId)
  {

    $storage = $this->entityTypeManager->getStorage('vote');

    $votes = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('user', $userId)
      ->condition('question', $questionId)
      ->count()
      ->execute();

    if ($votes) {
      throw new AccessDeniedHttpException('Only one vote per User.');
    }
  }


  public function checkAnswerExists(int $questionId, int $answerId)
  {
    $storage = $this->entityTypeManager->getStorage('answer');

    $answers = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('id', $answerId)
      ->condition('question', $questionId)
      ->count()
      ->execute();

    if (!$answers) {
      throw new BadRequestHttpException('Answer not found.');
    }
  }
}
