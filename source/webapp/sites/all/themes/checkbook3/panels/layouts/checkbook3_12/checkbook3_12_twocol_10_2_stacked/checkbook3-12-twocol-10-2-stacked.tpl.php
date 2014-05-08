<div class="panel-display omega-grid omega-12-twocol-10-2-stacked" <?php if (!empty($css_id)) { print "id=\"$css_id\""; } ?>>
  <div class="panel-panel grid-12">
    <div class="inside"><?php print $content['top']; ?></div>
  </div>
  <div class="panel-panel grid-10">
    <div class="inside"><?php print $content['left']; ?></div>
  </div>
  <div class="panel-panel grid-2">
    <div class="inside"><?php print $content['right']; ?></div>
  </div>
</div>
