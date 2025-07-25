<?php

namespace Drupal\voteapp\Form;

use Drupal\Core\Url;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class QuestionForm extends ContentEntityForm
{

  protected $answerListBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    $instance = parent::create($container);
    $instance->answerListBuilder = $container
      ->get('entity_type.manager')
      ->getListBuilder('answer');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildForm($form, $form_state);

    $question = $this->entity;

    if (!$question->isNew()) {

      $form['related_answers'] = [
        '#type' => 'details',
        '#title' => $this->t('Related Answers'),
        '#open' => TRUE,
        '#weight' => 100,
      ];

      $totalVotes = $question->getVotesCount();
      $form['related_answers']['total_votes'] = [
        '#type' => 'markup',
        '#markup' =>  '<h3>' . $totalVotes . ' vote(s)</h3>'
      ];


      $questionId = $question->id();
      $this->answerListBuilder->setQuestionId($questionId);
      $form['related_answers']['list'] = $this->answerListBuilder->render();
    }


    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => Url::fromRoute('entity.question.collection'),
      '#attributes' => [
        'class' => ['button'],
      ],
    ];

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state)
  {
    $entity = $this->getEntity();
    $status = parent::save($form, $form_state);

    if ($status === SAVED_NEW) {
      $form_state->setRedirect('entity.question.edit_form', ['question' => $entity->id()]);
    }

    //$form_state->setRedirectUrl(Url::fromRoute('entity.question.collection'));

    return $status;
  }
}
