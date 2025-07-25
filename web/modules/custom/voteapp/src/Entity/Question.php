<?php

namespace Drupal\voteapp\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Database\Database;
use Symfony\Component\Validator\Constraints\Regex;

/**
 *
 * @ContentEntityType(
 *   id = "question",
 *   label = @Translation("Question"),
 *   label_collection = @Translation("Questions"),
 *   label_singular = @Translation("question"),
 *   label_plural = @Translation("questions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count question",
 *     plural = "@count questions"
 *   ),
 *   base_table = "question",
 *   admin_permission = "administer question entities",
 *   handlers = {
 *     "list_builder" = "Drupal\voteapp\QuestionListBuilder",
 *     "form" = {
 *       "default" = "Drupal\voteapp\Form\QuestionForm",
 *       "add" = "Drupal\voteapp\Form\QuestionForm",
 *       "edit" = "Drupal\voteapp\Form\QuestionForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     },
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *     "status" = "status"
 *   },
 *   links = {
 *     "collection" = "/admin/content/question",
 *     "add-form" = "/admin/content/question/add",
 *     "edit-form" = "/admin/content/question/{question}/edit",
 *     "delete-form" = "/admin/content/question/{question}/delete",
 *     "entity.question.settings" = "/admin/structure/question/settings",
 *     "canonical" = "/admin/content/question/{question}",
 *   },
 *   field_ui_base_route = "entity.question.settings"
 * )
 */
class Question extends ContentEntityBase
{



  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setSettings(['max_length' => 255])
      ->setDisplayOptions('form', ['type' => 'string_textfield', 'weight' => 0])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['identifier'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Identifier'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 100,
        'is_ascii' => TRUE,
        'unique' => TRUE,
      ])
      ->addPropertyConstraints('value', [
        new Regex([
          'pattern' => '/^[A-Za-z0-9\-]+$/',
          'message' => 'The identifier can only contain letters, numbers, and hyphens.',
        ]),
      ])
      ->addConstraint('UniqueField')
      ->setDisplayOptions('form', ['type' => 'string_textfield', 'weight' => 1])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['show_results'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Show results'))
      ->setDescription(t('Show results after voting.'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Published'))
      ->setDescription(t('is published.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }


  private function getStorage(string $storage)
  {

    $storage = \Drupal::entityTypeManager()
      ->getStorage($storage);

    return $storage;
  }

  private function getStorageQuery(string $storage)
  {

    $query = $this->getStorage($storage)->getQuery();

    return $query;
  }


  public function getAnswersCount()
  {

    $query = $this->getStorageQuery('answer');

    $count = $query
      ->accessCheck(FALSE)
      ->condition('question', $this->id())
      ->count()
      ->execute();

    return $count;
  }


  public function getAnswersIds()
  {
    $query = $this->getStorageQuery('answer');

    $ids = $query
      ->accessCheck(FALSE)
      ->condition('question', $this->id())
      ->execute();

    return $ids;
  }

  public function getAnswers()
  {
    $ids = $this->getAnswersIds();
    $storage = $this->getStorage('answer');
    $answers = $storage->loadMultiple($ids);

    return $answers;
  }

  public function getStatistics()
  {

    $questionId = $this->id();

    $cache = \Drupal::cache();
    $cid = 'q:' . $questionId . ':statistics';

    $cachedData =  $cache->get($cid);

    if (!$cachedData) {

      $connection = Database::getConnection();

      $query = $connection->select('vote', 'v');
      $query->addField('v', 'answer');
      $query->addExpression('COUNT(v.id)', 'count');
      $query->condition('v.question', $questionId);
      $query->groupBy('v.answer');

      $results = $query->execute()->fetchAllKeyed();

      $total = (int) array_sum($results);

      $votes = array_map(function ($a) use ($total) {
        $votes = (int)$a;
        $percent =  ($votes * 100) / $total;
        $percent = (float)number_format($percent, 2, '.');
        return compact('votes', 'percent');
      }, $results);

      //
      foreach ($this->getAnswers() as $answer) {
        $answerId = $answer->id();
        $title = $answer->get('title')->value;
        if (isset($votes[$answerId])) {
          $votes[$answerId]['title'] = $title;
        }
      }

      $date = date("Y-m-d H:i:s");
      $data = compact('date', 'total', 'votes');

      $expire =  time() + 86400; //24h
      $cache->set($cid, $data, $expire);
    } else {
      $data = $cachedData->data;
    }

    return $data;
  }

  public function clearStatistics()
  {
    $questionId = $this->id();
    $cache = \Drupal::cache();
    $cid = 'q:' . $questionId . ':statistics';
    $cache->delete($cid);
  }


  public function getAnswerStatistics(int $answerId)
  {
    $answerId = (int)$answerId;
    $statistics = $this->getStatistics();
    $defaultStats = ['votes' => 0, 'percent' => 0];

    $stats = $statistics['votes'][$answerId] ?? $defaultStats;

    return $stats;
  }


  public function getVotesCount()
  {
    $statistics = $this->getStatistics();
    $total = $statistics['total'] ?? 0;

    return (int)$total;
  }
}
