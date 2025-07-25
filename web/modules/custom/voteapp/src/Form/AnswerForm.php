<?php

namespace Drupal\voteapp\Form;

use Drupal\Core\Url;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

class AnswerForm extends ContentEntityForm
{

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildForm($form, $form_state);

    $request = \Drupal::request();
    $questionId = $request->query->get('question');

    if ($questionId) {

      $form['question']['widget']['#default_value'] = $questionId;

      $form['actions']['cancel'] = [
        '#type' => 'link',
        '#title' => $this->t('Cancel'),
        '#url' => Url::fromRoute('entity.question.edit_form', ['question' => $questionId]),
        '#attributes' => [
          'class' => ['button'],
        ],
      ];
    }

    return $form;
  }
}
