<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://webmuehle.at
 * @since      1.0.3
 *
 * @package    resourceres
 * @subpackage resourceres/admin/partials
 */
?>

<?php

  if (!current_user_can('manage_options')) {
      wp_die();
  }

  global $wpdb;
  $table_name = $this->getTable('resources');

  if(isset($_GET['resourceID'])) {
    $resourceID = (int)$_GET['resourceID'];
  }

  if(isset($_POST['delete']) && isset($_POST['id']) && (int)$_POST['id']>0) { // delete
    $wpdb->delete($table_name, array( 'id' => (int)$_POST['id']));
  }

  if(isset($_POST['submit'])) {
    
    PC::debug($_POST);
    if (!isset($_POST['format12'])) {
      $format12 = 0;
    } else {
      $format12 = 1;
    }

    if(isset($_POST['id']) && (int)$_POST['id']>0) { // edit
      $wpdb->update($table_name,
        array(
     		'name' => $_POST['name'],
     		'open' => $_POST['open'],
        'close' => $_POST['close'],
        'days' => $_POST['days'],
        'history' => $_POST['history'],
        'allowedtypes' => $_POST['allowedtypes'],
        'time_split' => $_POST['time_split'],
        'max_reservation_minutes' => $_POST['max_reseravtion_minutes'],
        'format12' => $format12 
        ),
        array( 'id' => (int)$_POST['id'] ),
       	array(
       		'%s',
       		'%s',
           '%s',
           '%d',
           '%d',
           '%s',
           '%d',
           '%d',
           '%d'
       	));
      $resourceID = (int)$_POST['id'];
      $message = __('Successfully changed!','resourcescheduler');
    } else { // create
      $wpdb->insert($table_name,
	     array(
    		'name' => $_POST['name'],
    		'open' => $_POST['open'],
        'close' => $_POST['close'],
        'days' => $_POST['days'],
        'history' => $_POST['history'],
        'allowedtypes' => $_POST['allowedtypes'],
        'time_split' => $_POST['time_split'],
        'max_reservation_minutes' => $_POST['max_reseravtion_minutes'],
        'format12' => $format12 
    	),
    	array(
    		'%s',
    		'%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%d',
        '%d',
        '%d'
    	));
      $message = __('Successfully created!','resourcescheduler');
      $resourceID = $wpdb->insert_id;
    }
  }


  if(isset($resourceID) && $resourceID>0) {
    $resource = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $resourceID" );
  }

  if(!isset($resource)) {
    $resource = new stdClass();
    $resource->id = 0;
    $resource->name = '';
    $resource->open = 8;
    $resource->close = 22;
    $resource->days = 60;
    $resource->history = 30;
    $resource->max_reservation_minutes = 120;
    $resource->time_split = 30;
    $resource->format12 = 0;
    $resource->allowedtypes = "League[event-important]|Member[event-info]|Lesson[event-special]";
  }
?>

<div class="wrap">
  <a class="page-title-action" href="<?= admin_url("admin.php?page=resourcescheduler-resources") ?>"><?= __('Back','resourcescheduler');?></a>
  <h1 class="wp-heading-inline"><?= (isset($resource) && $resource->id>0) ? $resource->name.__(' edit','resourcescheduler') : __('Create Resource','resourcescheduler') ?></h1>
  <hr class="wp-header-end">
  <?php if (isset($message)) { ?>
    <div id="message" class="updated notice is-dismissible">
        <p><?= $message ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
      <?php 
    } ?>
  <form method="post">
    <input type="hidden" name="id" value="<?= $resource->id ?>" />
    <table>
      <tr>
        <td><?= __('Name','resourcescheduler');?></td>
        <td><input type="text" name="name" maxlength="255" 
          placeholder="resource name" value="<?= $resource->name ?>" required /></td>
      </tr>
      <tr>
        <td><?= __('Opens (hour)','resourcescheduler');?></td>
        <td><input type="time" name="open" placeholder="Start Time" 
          value="<?= $resource->open ?>" required /></td>
      </tr>
      <tr>
        <td><?= __('Closes (hour)','resourcescheduler');?></td>
        <td><input type="time" name="close" placeholder="Closing Time" value="<?= $resource->close ?>" required /></td>
      </tr>
      <tr>
        <td><?= __('Days','resourcescheduler');?></td>
        <td><input type="number" name="days" min="0" max="365" maxlength="3" value="<?= $resource->days ?>" required /></td>
      </tr>
      <tr>
        <td><?= __('Days History','resourcescheduler');?></td>
        <td><input type="number" name="history" min="0" max="365" maxlength="3" value="<?= $resource->history ?>" required /></td>
      </tr>
      <tr>
        <td><?= __('Allowed Types','resourcescheduler');?></td>
        <td><textarea name="allowedtypes" rows="5" cols="30" wrap="soft" placeholder="input event types seperated with |"><?= $resource->allowedtypes ?></textarea></td>
      </tr>
      <tr>
        <td><?= __('Time Split','resourcescheduler');?></td>
        <td>
          <select name="time_split">
              <option value="30" selected="30==<%=resource->time_split%>"><?= __('30 minutes','resourcescheduler');?></option>
              <option value="60" selected="60==<%=resource->time_split%>"><?= __('60 minutes','resourcescheduler');?></option>
        </select>
      </td>
      </tr>
      <tr>
        <td><?= __('Max Reservation Length (Min)','resourcescheduler');?></td>
        <td><input type="number" name="max_reseravtion_minutes" value="<?= $resource->max_reservation_minutes ?>" /></td>
      </tr>
      <tr>
        <td><?= __('Use 12 Hour (AM/PM) Format)','resourcescheduler');?></td>
        <td><input type="checkbox" name="format12" value="1" 
            <?php if ($resource->format12 == 1) {?> checked <?php } ?> /></td>
      </tr>


      <tr>
        <td></td>
        <td><input class="button" type="submit" name="submit" /></td>
      </tr>
      <?php if(isset($resource) && $resource->id>0) { ?>
        <tr>
          <td colspan="2"><hr/></td>
        </tr>
        <tr>
          <td><?= __('Delete Resource','resourcescheduler');?></td>
          <td><input class="button" type="submit" name="delete" value=<?= __('Delete','resourcescheduler');?> /></td>
        </tr>
      <?php } ?>
  </form>
</div>
</div>
</div>
