<?php

namespace Drupal\voteapp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\voteapp\Entity\Question;
use Drupal\voteapp\Service\QuestionService;
use Drupal\voteapp\Service\VoteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class QuestionController extends ApiBaseController
{

  protected $questionService;
  protected $voteService;

  public function __construct(QuestionService $questionService, VoteService $voteService)
  {
    $this->questionService = $questionService;
    $this->voteService = $voteService;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('voteapp.question_service'),
      $container->get('voteapp.vote_service')
    );
  }

  public function list(Request $request)
  {
    $user = $this->getUserRequest($request);

    $page = max(0, (int) $request->query->get('page', 0));
    $limit = max(5, (int) $request->query->get('limit', 0));

    $questionsList = $this->questionService->list($page, $limit);

    return $this->response($questionsList);
  }

  public function show(Request $request, Question $question)
  {
    $user = $this->getUserRequest($request);

    $this->checkIsPublished($question);

    $questionData = $this->questionService->buildData($question, true);

    return $this->response($questionData);
  }

  public function vote(Request $request, Question $question)
  {
    $user = $this->getUserRequest($request);

    $this->checkIsPublished($question);

    $data = json_decode($request->getContent(), TRUE);
    $questionId = $question->id();
    $answerId = $data['answer'] ?? null;
    $userId = $user->id();
    $showResults = (int)$question->get('show_results')->value;

    $vote = $this->voteService->createVote($questionId, $answerId, $userId);

    $questionData = $this->questionService->buildData($question);

    if ($showResults) {
      $questionData['statistics'] = $question->getStatistics();
    }

    return $this->response($questionData);
  }


  public function checkIsPublished(Question $question)
  {
    $isPublished = (int) $question->get('status')->value;
    if (!$isPublished) {
      throw new NotFoundHttpException('Not found.');
    }
  }
}
