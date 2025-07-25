<?php

namespace Drupal\voteapp\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\voteapp\Entity\Question;
use Drupal\voteapp\Service\VoteService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VoteForm extends FormBase
{

  protected $question;
  protected $voteService;

  public function __construct(VoteService $voteService)
  {
    $this->voteService = $voteService;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('voteapp.vote_service')
    );
  }

  public function getFormId()
  {
    return 'vote_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, Question $question = null)
  {
    $this->question = $question;

    $answers = $this->question->getAnswers();
    $isPublished = (int) $this->question->get('status')->value;
    $showResults = (int) $this->question->get('show_results')->value;
    $isSubmitted = $form_state->get('submitted');

    if (!$isPublished) {
      throw new NotFoundHttpException();
    }

    if ($isSubmitted) {

      $form['message'] = [
        '#markup' => '<h2>'
          . $this->t('<h2>Thanks for you vote.</h2>')
          . '</h2>',
      ];

      if ($showResults) {
        $statisticsTable = $this->buildStatisticsTable();
        $form = array_merge($form, $statisticsTable);
      }

      return $form;
    }

    $form['title'] = [
      '#markup' => '<h2>'
        . $this->question->get('title')->value
        . '</h2>',
    ];

    $form['identifier'] = [
      '#markup' => '<h4>'
        . $this->question->get('identifier')->value
        . '</h4>',
    ];

    $token = \Drupal::service('csrf_token')->get($this->getFormId());

    $form['csrf_token'] = [
      '#type' => 'hidden',
      '#value' => $token,
    ];


    $answerOptions = $this->buildAnswersOptions($answers);
    $form = array_merge($form, $answerOptions);

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enviar resposta'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {


    $token = $form_state->getValue('csrf_token');
    if (!$this->checkToken($token)) {
      throw new AccessDeniedHttpException('Invalid CSRF Token.');
    }

    if (!$this->checkHost()) {
      throw new AccessDeniedHttpException('Not allowed Host.');
    }

    $questionId = (int)$this->question->id();
    $answerId = (int)$form_state->getValue('answer');
    $userId = \Drupal::currentUser()->id();

    $vote = $this->voteService->createVote($questionId, $answerId, $userId);

    $form_state->set('submitted', TRUE);
    $form_state->setRebuild(TRUE);
  }


  public function buildAnswersOptions(array $answers)
  {

    $form['answers'] = [
      '#type' => 'container',
    ];

    foreach ($answers as  $answer) {

      $answerId = $answer->id();
      $title = $answer->get('title')->value;
      $description = $answer->get('description')->value;

      $imageURL = $answer->getImageURL();
      $image = $imageURL ? '<img src="' . $imageURL . '">' : null;

      $form['answers'][$answerId] = [
        '#type' => 'container',
        'radio' => [
          '#type' => 'radio',
          '#title' => $title,
          '#return_value' => $answerId,
          '#parents' => ['answer'],
        ],
        'title' => [
          '#markup' => '<strong>' . $title . '</strong>'
        ],
        'image' => [
          '#markup' => $image,
          '#allowed_tags' => ['img'],
        ],
        'description' => [
          '#markup' => '<p>' . $description . '</p>'
        ],
        'line' => [
          '#markup' => '<hr>'
        ]
      ];
    }

    return $form;
  }

  public function buildStatisticsTable()
  {

    $form['votes'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Answer'),
        $this->t('Votes'),
        $this->t('Percent'),
      ],
      '#empty' => $this->t('No votes found.'),
    ];

    $statistics = $this->question->getStatistics();

    foreach ($statistics['votes'] as $answerId => $voteData) {

      $title = $voteData['title'];
      $votes = $voteData['votes'];
      $percent = $voteData['percent'];

      $form['votes'][$answerId]['answer'] = [
        '#markup' => $title,
      ];

      $form['votes'][$answerId]['votes'] = [
        '#markup' => $votes,
      ];

      $form['votes'][$answerId]['percent'] = [
        '#markup' => $percent . '%',
      ];
    }

    $form['cache_date'] = [
      '#markup' => 'proccessed date: ' . $statistics['date'],
    ];

    return $form;
  }

  public function checkToken(string $token)
  {
    $validToken = \Drupal::csrfToken()->validate($token, 'vote_form');
    return $validToken;
  }

  public function checkHost()
  {
    $referer = \Drupal::request()->headers->get('referer');
    $host = parse_url($referer, PHP_URL_HOST);
    $allowedHost = \Drupal::request()->getHost();
    $valid = ($host == $allowedHost);
    return $valid;
  }
}
