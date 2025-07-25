<?php

namespace Drupal\voteapp\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 *
 * @ContentEntityType(
 *   id = "vote",
 *   label = @Translation("Vote"),
 *   base_table = "vote",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\Core\Entity\ContentEntityForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder", *
 *   },
 *   admin_permission = "administer question entities",
 *   links = {
 *     "add-form" = "/admin/content/vote/add",
 *     "edit-form" = "/admin/content/vote/{vote}/edit",
 *     "delete-form" = "/admin/content/vote/{vote}/delete",
 *     "collection" = "/admin/content/vote"
 *   },
 *   field_ui_base_route = "entity.vote.settings",
 *   translatable = FALSE,
 *   show_revision_ui = FALSE
 * )
 */

class Vote extends ContentEntityBase
{

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Question reference
    $fields['question'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Question'))
      ->setDescription(t('The question this answer belongs to.'))
      ->setSetting('target_type', 'question')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    // answer reference
    $fields['answer'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Answer'))
      ->setDescription(t('The vote in Question.'))
      ->setSetting('target_type', 'answer')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // user reference
    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setDescription(t('The user vote.'))
      ->setSetting('target_type', 'user')
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDefaultValueCallback(static::class . '::getCurrentUserId')
      ->setRequired(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    return $fields;
  }

  public static function getCurrentUserId()
  {
    return [\Drupal::currentUser()->id()];
  }
}
