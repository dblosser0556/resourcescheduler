<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://webmuehle.at
 * @since      1.0.3
 *
 * @package    resourcescheduler
 * @subpackage resourcescheduler/admin/partials
 */
?>

<?php
  if (!current_user_can('manage_options')) {
      wp_die();
  }


  global $wpdb;
  $table_name = $this->getTable('resources');
  $facilties = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");

  ?>

<div class="wrap">
  <h1 class="wp-heading-inline"><?= __('Manage Resources', 'resourcescheduler');?></h1>
  <a class="page-title-action" href="<?= admin_url("admin.php?page=resourcescheduler-resource") ?>"><?= __('Create', 'resourcescheduler');?></a>
  <hr class="wp-header-end">

  <table class="wp-list-table widefat fixed striped posts">
    <thead>
      <tr>
        <th class="manage-column column-title column-primary"><?= __('Name', 'resourcescheduler');?></th>
        <th class="manage-column column-title column-primary"><?= __('Opening Hours', 'resourcescheduler');?></th>
        <th class="manage-column column-title column-primary"><?= __('Max Future Reservation Days', 'resourcescheduler');?></th>
        <th class="manage-column column-title column-primary"><?= __('History to Keep', 'resourcescheduler');?></th>
        <th class="manage-column column-title column-primary"><?= __('Allowed Reservation Types', 'resourcescheduler');?></th>
        <th class="manage-column column-title column-primary"><?= __('Shortcode', 'resourcescheduler');?></th>
        <th class="manage-column column-title"><?= __('Action', 'resourcescheduler');?></th>
      </tr>
    </thead>
    <tbody>
      <?php for($i=0;$i<sizeof($facilties);$i++) { $item = $facilties[$i]; ?>
        <tr>
          <td><?= $item->name ?></td>
          <td><?= $item->open ?>-<?= $item->close ?> <?= __(' ', 'resourcescheduler');?></td>
          <td><?= $item->days ?></td>
          <td><?= $item->history ?></td>
          <td><?= $item->allowedtypes ?></td>
          <td><code><?= "[resourcescheduler-resource id=$item->id]" ?></code></td>
          <td><a class="page-action" href="<?= admin_url("admin.php?page=resourcescheduler-resource&resourceID={$item->id}") ?>"><?= __('Edit', 'resourcescheduler');?></a>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <p></p>
</div>
