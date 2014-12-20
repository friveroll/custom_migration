

<?php
/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\source\User.
 */
 
namespace Drupal\migrate_custom\Plugin\migrate\source;
 
use Drupal\migrate\Plugin\SourceEntityInterface;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;
 
/**
 * Extract users from Drupal 7 database.
 *
 * @MigrateSource(
 *   id = "migrate_custom"
 * )
 */
class User extends DrupalSqlBase implements SourceEntityInterface {
 
  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('users', 'u')
      ->fields('u', array_keys($this->baseFields()))
      ->condition('uid', 0, '>');
  }
 
  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = $this->baseFields();
    $fields['ecivil'] = $this->t('estado civil');
    $fields['ocupaci_n'] = $this->t('ocupación');
    $fields['cumpleanos'] = $this->t('Cumpleaños');
    $fields['religi_n'] = $this->t('religión');
    $fields['lugar_de_residencia'] = $this->t('Lugar de residencia')
    return $fields;
  }
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $uid = $row->getSourceProperty('uid');
 
    // first_name
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_ecivil_value
      FROM
        {field_data_field_ecivil} fld
      WHERE
        fld.entity_id = :uid
    ', array(':uid' => $uid));
    foreach ($result as $record) {
      $row->setSourceProperty('ecivil', $record->field_ecivil_value );
    }
 
    // last_name
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_ocupaci_n_value
      FROM
        {field_data_field_ocupaci_n} fld
      WHERE
        fld.entity_id = :uid
    ', array(':uid' => $uid));
    foreach ($result as $record) {
      $row->setSourceProperty('ocupaci_n', $record->field_ocupaci_n_value );
    }
 
    // biography
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_cumpleanos_value,
        fld.field_cumpleanos_format
      FROM
        {field_data_field_cumpleanos} fld
      WHERE
        fld.entity_id = :uid
    ', array(':uid' => $uid));
    foreach ($result as $record) {
      $row->setSourceProperty('cumpleanos_value', $record->field_cumpleanos_value );
    }

        // biography
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_religi_n_value,
        fld.field_religi_n_format
      FROM
        {field_data_field_religi_n} fld
      WHERE
        fld.entity_id = :uid
    ', array(':uid' => $uid));
    foreach ($result as $record) {
      $row->setSourceProperty('religi_n_value', $record->field_religi_n_value );
    }

    // lugar_de_residencia
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_lugar_de_residencia_value,
        fld.field_lugar_de_residencia_format
      FROM
        {field_data_field_lugar_de_residencia} fld
      WHERE
        fld.entity_id = :uid
    ', array(':uid' => $uid));
    foreach ($result as $record) {
      $row->setSourceProperty('lugar_de_residencia_value', $record->field_lugar_de_residencia_value );
    }
 
    return parent::prepareRow($row);
  }
 
  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'uid' => array(
        'type' => 'integer',
        'alias' => 'u',
      ),
    );
  }
 
  /**
   * Returns the user base fields to be migrated.
   *
   * @return array
   *   Associative array having field name as key and description as value.
   */
  protected function baseFields() {
    $fields = array(
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'pass' => $this->t('Password'),
      'mail' => $this->t('Email address'),
      'signature' => $this->t('Signature'),
      'signature_format' => $this->t('Signature format'),
      'created' => $this->t('Registered timestamp'),
      'access' => $this->t('Last access timestamp'),
      'login' => $this->t('Last login timestamp'),
      'status' => $this->t('Status'),
      'timezone' => $this->t('Timezone'),
      'language' => $this->t('Language'),
      'picture' => $this->t('Picture'),
      'init' => $this->t('Init'),
    );
    return $fields;
 
}
 
  /**
   * {@inheritdoc}
   */
  public function bundleMigrationRequired() {
    return FALSE;
  }
 
  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'user';
  }
 
}
?>

