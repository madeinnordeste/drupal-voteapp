<?php

namespace Drupal\voteapp;

use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;


class QuestionListBuilder extends EntityListBuilder
{

  /**
   * {@inheritdoc}
   */
  public function buildHeader()
  {
    $header['id'] = $this->t('ID');
    $header['title'] = $this->t('Title');
    $header['identifier'] = $this->t('Identifier');
    $header['answers'] = $this->t('Answers');
    $header['votes'] = $this->t('Votes');
    $header['link'] = $this->t('Public Link');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity)
  {

    $identifier = $entity->get('identifier')->value;

    $url = Url::fromRoute('vote.form', [
      'question' => $identifier,
    ])
      ->setOptions([
        'attributes' => [
          'target' => '_blank'
        ],
      ]);

    $link = Link::fromTextAndUrl($identifier, $url);

    $row['id'] = $entity->id();
    $row['title'] = $entity->label();
    $row['identifier'] = $identifier;
    $row['answers'] = $entity->getAnswersCount();
    $row['votes'] = $entity->getVotesCount();
    $row['link'] = $link;
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render()
  {
    $build = parent::render();

    $addURL = Url::fromRoute('entity.question.add_form');
    $build['add_button'] = [
      '#type' => 'link',
      '#title' => $this->t('Add Question'),
      '#url' => $addURL,
      '#attributes' => ['class' => ['button', 'button--primary', 'button-action']],
      '#weight' => -10,
    ];

    return $build;
  }
}
