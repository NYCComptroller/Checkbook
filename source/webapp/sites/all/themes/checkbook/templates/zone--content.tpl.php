<?php if ($wrapper): ?><div<?php print $attributes; ?>><?php endif; ?>  
    <?php if ($breadcrumb && _checkbook_custom_breadcrumb_is_hierarchical()): ?>
      <div id="breadcrumb" class="breadcrumb grid-<?php print $columns; ?>"><?php print urldecode($breadcrumb); ?></div>
    <?php endif; ?>   
  <div<?php print $content_attributes; ?>>     
    <?php if ($messages): ?>
      <div id="messages" class="grid-<?php print $columns; ?>"><?php print $messages; ?></div>
    <?php endif; ?>
    <?php print $content; ?>
  </div>
<?php if ($wrapper): ?></div><?php endif; ?>


