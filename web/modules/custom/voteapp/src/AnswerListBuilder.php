<?php

namespace Drupal\voteapp;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;


class AnswerListBuilder extends EntityListBuilder
{

  protected $questionId;

  public function setQuestionId($question_id)
  {
    $this->questionId = $question_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityIds()
  {

    $query = $this->storage->getQuery()
      ->accessCheck(FALSE);

    if ($this->questionId) {
      $query->condition('question', $this->questionId);
    }

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity)
  {

    $operations = parent::getOperations($entity);

    if ($this->questionId) {

      $queryParams = $this->buildQuesryParams();

      if (isset($operations['edit'])) {
        $operations['edit']['url'] = $operations['edit']['url']->setOption('query', $queryParams);
      }

      if (isset($operations['delete'])) {
        $operations['delete']['url'] = $operations['delete']['url']->setOption('query', $queryParams);
      }
    }

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array
  {
    $header['ID'] = $this->t('ID');
    $header['title'] = $this->t('Title');
    $header['question_label'] = $this->t('Question');
    $header['votes'] = $this->t('Votes');
    $header['percent'] = $this->t('Percent');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array
  {

    $question = $entity->get('question')->entity;
    $label = $question ? $question->label() : '';

    $statistics = $question->getAnswerStatistics($entity->id());
    $votes = $statistics['votes'];
    $percent = $statistics['percent'];

    $row['id'] = $entity->id();
    $row['title'] = $entity->get('title')->value;
    $row['question_label'] = $label;
    $row['votes'] = $votes;
    $row['percent'] = $percent . "%";
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render()
  {
    $build = parent::render();

    $queryParams = $this->buildQuesryParams();

    $addURL = Url::fromRoute('entity.answer.add_form', $queryParams);

    $build['add_button'] = [
      '#type' => 'link',
      '#title' => $this->t('Add Answer'),
      '#url' => $addURL,
      '#attributes' => ['class' => ['button', 'button--primary', 'button-action']],
      '#weight' => -10,
    ];

    return $build;
  }


  public function buildQuesryParams()
  {

    $queryParams = \Drupal::request()->query->all();

    if ($this->questionId) {

      $queryParams['question'] = $this->questionId;

      $url = Url::fromRoute('entity.question.edit_form', [
        'question' => $this->questionId
      ])->toString();

      $queryParams['destination'] = $url;
    }

    return $queryParams;
  }
}
