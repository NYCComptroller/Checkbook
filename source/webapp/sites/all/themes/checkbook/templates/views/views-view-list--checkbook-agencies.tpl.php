<?php
/**
 * @file views-view-list.tpl.php
 * Default simple view template to display a list of rows.
 *
 * - $title : The title of this group of rows.  May be empty.
 * - $options['type'] will either be ul or ol.
 * @ingroup views_templates
 */
?>

<?php print $wrapper_prefix; ?>
  <?php if (!empty($title)) : ?>
    <h3><?php print $title; ?></h3>
  <?php endif; ?>
  <div id="agency-list">
    <div class="agency-list-open"><a href="#">Select Agency</a></div>
		<div class="agency-list-content">
	    <?php print $list_type_prefix; ?>
	      <?php foreach ($rows as $id => $row): ?>
	        <li class="<?php print $classes_array[$id]; ?>"><?php print $row; ?></li>
	      <?php endforeach; ?>
	    <?php print $list_type_suffix; ?>
	    <div class="agency-list-nav">
	      <a href="#" id="prev">Prev</a>
	      <a href="#" id="next">Next</a>
	    </div>
			<div class="agency-list-close"><a href="#">Close</a></div>
		</div>
  </div>
<?php print $wrapper_suffix; ?>