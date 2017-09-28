<?php
/**
 * @file
 * checkbook_menus.features.menu_links.inc
 */

/**
 * Implements hook_menu_default_menu_links().
 */
function checkbook_menus_menu_default_menu_links() {
  $menu_links = array();

  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/',
    'router_path' => '',
    'link_title' => 'Bureaus',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-50',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/acc/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/acc/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/acc/',
    'router_path' => '',
    'link_title' => 'Accountancy',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-49',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/adm/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/adm/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/adm/',
    'router_path' => '',
    'link_title' => 'Administration',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-48',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/audit/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/audit/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/audit/',
    'router_path' => '',
    'link_title' => 'Audit',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-46',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/bam/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/bam/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/bam/',
    'router_path' => '',
    'link_title' => 'Asset Management',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-47',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/bis/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/bis/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/bis/',
    'router_path' => '',
    'link_title' => 'Information Systems',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-39',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/bla/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/bla/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/bla/',
    'router_path' => '',
    'link_title' => 'Law & Adjustment',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-36',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/bll/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/bll/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/bll/',
    'router_path' => '',
    'link_title' => 'Labor Law',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-37',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/bud/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/bud/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/bud/',
    'router_path' => '',
    'link_title' => 'Budget',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-45',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/cac/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/cac/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/cac/',
    'router_path' => '',
    'link_title' => 'Community Action Center',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-43',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/cb/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/cb/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/cb/',
    'router_path' => '',
    'link_title' => 'Commercial Banking',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-44',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/eng/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/eng/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/eng/',
    'router_path' => '',
    'link_title' => 'Engineering',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-40',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/igr/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/igr/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/igr/',
    'router_path' => '',
    'link_title' => 'Intergovernmental Relations',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-38',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/oca/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/oca/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/oca/',
    'router_path' => '',
    'link_title' => 'Contract Administration',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-41',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/ocr/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/ocr/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/ocr/',
    'router_path' => '',
    'link_title' => 'Community Relations',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-42',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/opm/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/opm/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/opm/',
    'router_path' => '',
    'link_title' => 'Policy Management',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-34',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/pf/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/pf/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/pf/',
    'router_path' => '',
    'link_title' => 'Public Finance',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-32',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/bureaus/pla/events.shtm
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/bureaus/pla/events.shtm'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/pla/events.shtm',
    'router_path' => '',
    'link_title' => 'Planning',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-35',
  );
  // Exported menu link: menu-bureaus:https://comptroller.nyc.gov/press/
  $menu_links['menu-bureaus:https://comptroller.nyc.gov/press/'] = array(
    'menu_name' => 'menu-bureaus',
    'link_path' => 'https://comptroller.nyc.gov/press/',
    'router_path' => '',
    'link_title' => 'Press Office',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-33',
  );
  // Exported menu link: menu-employment:https://comptroller.nyc.gov/bureaus/adm/jobs.shtm
  $menu_links['menu-employment:https://comptroller.nyc.gov/bureaus/adm/jobs.shtm'] = array(
    'menu_name' => 'menu-employment',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/adm/jobs.shtm',
    'router_path' => '',
    'link_title' => 'Employment',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '0',
  );
  // Exported menu link: menu-news-room:https://comptroller.nyc.gov/press/
  $menu_links['menu-news-room:https://comptroller.nyc.gov/press/'] = array(
    'menu_name' => 'menu-news-room',
    'link_path' => 'https://comptroller.nyc.gov/press/',
    'router_path' => '',
    'link_title' => 'News Room',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-50',
  );
  // Exported menu link: menu-news-room:https://comptroller.nyc.gov/press/event_photos.shtm
  $menu_links['menu-news-room:https://comptroller.nyc.gov/press/event_photos.shtm'] = array(
    'menu_name' => 'menu-news-room',
    'link_path' => 'https://comptroller.nyc.gov/press/event_photos.shtm',
    'router_path' => '',
    'link_title' => 'Photos',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-49',
  );
  // Exported menu link: menu-news-room:https://comptroller.nyc.gov/press/pr_current.shtm
  $menu_links['menu-news-room:https://comptroller.nyc.gov/press/pr_current.shtm'] = array(
    'menu_name' => 'menu-news-room',
    'link_path' => 'https://comptroller.nyc.gov/press/pr_current.shtm',
    'router_path' => '',
    'link_title' => 'Press Releases',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-48',
  );
  // Exported menu link: menu-reports:https://comptroller.nyc.gov/bureaus/acc/CAFR2009_Ins.shtm
  $menu_links['menu-reports:https://comptroller.nyc.gov/bureaus/acc/CAFR2009_Ins.shtm'] = array(
    'menu_name' => 'menu-reports',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/acc/CAFR2009_Ins.shtm',
    'router_path' => '',
    'link_title' => 'Accountancy',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-49',
  );
  // Exported menu link: menu-reports:https://comptroller.nyc.gov/bureaus/audit/yearlyview.asp
  $menu_links['menu-reports:https://comptroller.nyc.gov/bureaus/audit/yearlyview.asp'] = array(
    'menu_name' => 'menu-reports',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/audit/yearlyview.asp',
    'router_path' => '',
    'link_title' => 'Audits',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-48',
  );
  // Exported menu link: menu-reports:https://comptroller.nyc.gov/bureaus/bla/annual_report.shtm
  $menu_links['menu-reports:https://comptroller.nyc.gov/bureaus/bla/annual_report.shtm'] = array(
    'menu_name' => 'menu-reports',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/bla/annual_report.shtm',
    'router_path' => '',
    'link_title' => 'Law and Adjustment',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-44',
  );
  // Exported menu link: menu-reports:https://comptroller.nyc.gov/bureaus/bud/all_budget_reports.shtm
  $menu_links['menu-reports:https://comptroller.nyc.gov/bureaus/bud/all_budget_reports.shtm'] = array(
    'menu_name' => 'menu-reports',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/bud/all_budget_reports.shtm',
    'router_path' => '',
    'link_title' => 'Budget',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-47',
  );
  // Exported menu link: menu-reports:https://comptroller.nyc.gov/bureaus/eng/reports.shtm
  $menu_links['menu-reports:https://comptroller.nyc.gov/bureaus/eng/reports.shtm'] = array(
    'menu_name' => 'menu-reports',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/eng/reports.shtm',
    'router_path' => '',
    'link_title' => 'Engineering',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-45',
  );
  // Exported menu link: menu-reports:https://comptroller.nyc.gov/bureaus/oca/reports.shtm
  $menu_links['menu-reports:https://comptroller.nyc.gov/bureaus/oca/reports.shtm'] = array(
    'menu_name' => 'menu-reports',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/oca/reports.shtm',
    'router_path' => '',
    'link_title' => 'Contract Administration',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-46',
  );
  // Exported menu link: menu-reports:https://comptroller.nyc.gov/bureaus/opm/reports.shtm
  $menu_links['menu-reports:https://comptroller.nyc.gov/bureaus/opm/reports.shtm'] = array(
    'menu_name' => 'menu-reports',
    'link_path' => 'https://comptroller.nyc.gov/bureaus/opm/reports.shtm',
    'router_path' => '',
    'link_title' => 'Policy',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-43',
  );
  // Exported menu link: menu-reports:https://comptroller.nyc.gov/reports.shtm
  $menu_links['menu-reports:https://comptroller.nyc.gov/reports.shtm'] = array(
    'menu_name' => 'menu-reports',
    'link_path' => 'https://comptroller.nyc.gov/reports.shtm',
    'router_path' => '',
    'link_title' => 'Reports',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-50',
  );
  // Exported menu link: menu-the-administration:http://comptroller.nyc.gov/administration.asp#Ari-Hoffnung
  $menu_links['menu-the-administration:http://comptroller.nyc.gov/administration.asp#Ari-Hoffnung'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'http://comptroller.nyc.gov/administration.asp#Ari-Hoffnung',
    'router_path' => '',
    'link_title' => 'Ari Hoffnung',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-46',
  );
  // Exported menu link: menu-the-administration:http://comptroller.nyc.gov/administration.asp#Valerie-Budzik
  $menu_links['menu-the-administration:http://comptroller.nyc.gov/administration.asp#Valerie-Budzik'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'http://comptroller.nyc.gov/administration.asp#Valerie-Budzik',
    'router_path' => '',
    'link_title' => 'Valerie Budzik',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-48',
  );
  // Exported menu link: menu-the-administration:https://comptroller.nyc.gov/administration.asp
  $menu_links['menu-the-administration:https://comptroller.nyc.gov/administration.asp'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'https://comptroller.nyc.gov/administration.asp',
    'router_path' => '',
    'link_title' => 'The Administration',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-50',
  );
  // Exported menu link: menu-the-administration:https://comptroller.nyc.gov/administration.asp#Carol-Kostik
  $menu_links['menu-the-administration:https://comptroller.nyc.gov/administration.asp#Carol-Kostik'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'https://comptroller.nyc.gov/administration.asp#Carol-Kostik',
    'router_path' => '',
    'link_title' => 'Carol S. Kostik',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-44',
  );
  // Exported menu link: menu-the-administration:https://comptroller.nyc.gov/administration.asp#Geneith-Turnbull
  $menu_links['menu-the-administration:https://comptroller.nyc.gov/administration.asp#Geneith-Turnbull'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'https://comptroller.nyc.gov/administration.asp#Geneith-Turnbull',
    'router_path' => '',
    'link_title' => 'Geneith Turnbull',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-42',
  );
  // Exported menu link: menu-the-administration:https://comptroller.nyc.gov/administration.asp#Lawrence-Schloss
  $menu_links['menu-the-administration:https://comptroller.nyc.gov/administration.asp#Lawrence-Schloss'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'https://comptroller.nyc.gov/administration.asp#Lawrence-Schloss',
    'router_path' => '',
    'link_title' => 'Lawrence M. Schloss',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-43',
  );
  // Exported menu link: menu-the-administration:https://comptroller.nyc.gov/administration.asp#Ricardo-Morales
  $menu_links['menu-the-administration:https://comptroller.nyc.gov/administration.asp#Ricardo-Morales'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'https://comptroller.nyc.gov/administration.asp#Ricardo-Morales',
    'router_path' => '',
    'link_title' => 'Ricardo E. Morales',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-49',
  );
  // Exported menu link: menu-the-administration:https://comptroller.nyc.gov/administration.asp#Simcha-Felder
  $menu_links['menu-the-administration:https://comptroller.nyc.gov/administration.asp#Simcha-Felder'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'https://comptroller.nyc.gov/administration.asp#Simcha-Felder',
    'router_path' => '',
    'link_title' => 'Simcha Felder',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-47',
  );
  // Exported menu link: menu-the-administration:https://comptroller.nyc.gov/administration.asp#Tina-Kim
  $menu_links['menu-the-administration:https://comptroller.nyc.gov/administration.asp#Tina-Kim'] = array(
    'menu_name' => 'menu-the-administration',
    'link_path' => 'https://comptroller.nyc.gov/administration.asp#Tina-Kim',
    'router_path' => '',
    'link_title' => 'H. Tina Kim',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-45',
  );
  // Exported menu link: menu-the-comptroller:https://comptroller.nyc.gov/comptroller/
  $menu_links['menu-the-comptroller:https://comptroller.nyc.gov/comptroller/'] = array(
    'menu_name' => 'menu-the-comptroller',
    'link_path' => 'https://comptroller.nyc.gov/comptroller/',
    'router_path' => '',
    'link_title' => 'The Comptroller',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-50',
  );
  // Exported menu link: menu-the-comptroller:https://comptroller.nyc.gov/comptroller/bio.shtm
  $menu_links['menu-the-comptroller:https://comptroller.nyc.gov/comptroller/bio.shtm'] = array(
    'menu_name' => 'menu-the-comptroller',
    'link_path' => 'https://comptroller.nyc.gov/comptroller/bio.shtm',
    'router_path' => '',
    'link_title' => 'About John C. Liu',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-48',
  );
  // Exported menu link: menu-the-comptroller:https://comptroller.nyc.gov/comptroller/inauguration.shtm
  $menu_links['menu-the-comptroller:https://comptroller.nyc.gov/comptroller/inauguration.shtm'] = array(
    'menu_name' => 'menu-the-comptroller',
    'link_path' => 'https://comptroller.nyc.gov/comptroller/inauguration.shtm',
    'router_path' => '',
    'link_title' => 'Inaugural Address',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '1',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-47',
  );
  // Translatables
  // Included for use with string extractors like potx.
  t('About John C. Liu');
  t('Accountancy');
  t('Administration');
  t('Ari Hoffnung');
  t('Asset Management');
  t('Audit');
  t('Audits');
  t('Budget');
  t('Bureaus');
  t('Carol S. Kostik');
  t('Commercial Banking');
  t('Community Action Center');
  t('Community Relations');
  t('Contract Administration');
  t('Employment');
  t('Engineering');
  t('Geneith Turnbull');
  t('H. Tina Kim');
  t('Inaugural Address');
  t('Information Systems');
  t('Intergovernmental Relations');
  t('Labor Law');
  t('Law & Adjustment');
  t('Law and Adjustment');
  t('Lawrence M. Schloss');
  t('News Room');
  t('Photos');
  t('Planning');
  t('Policy');
  t('Policy Management');
  t('Press Office');
  t('Press Releases');
  t('Public Finance');
  t('Reports');
  t('Ricardo E. Morales');
  t('Simcha Felder');
  t('The Administration');
  t('The Comptroller');
  t('Valerie Budzik');


  return $menu_links;
}
