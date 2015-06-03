<?php

/**
 * @file
 * @theme Skeleton
 *
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 */
?>

<header class="main-header">
  <div class="main-header-inner">

    <?php if ($logo): ?>
      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="main-header-logo">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
      </a>
    <?php endif; ?>

    <?php if ($site_name || $site_slogan): ?>
      <div class="name-and-slogan">
        <?php if ($site_name): ?>
          <?php if ($title): ?>
            <div class="site-name"><strong>
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
            </strong></div>
          <?php else: /* Use h1 when the content title is empty */ ?>
            <h1 class="site-name">
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
            </h1>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($site_slogan): ?>
          <div class="site-slogan"><?php print $site_slogan; ?></div>
        <?php endif; ?>
      </div> <!-- /.name-and-slogan -->
    <?php endif; ?>

    <?php print render($page['header']); ?>

  </div>
</header> <!-- /.main-header -->

<?php if ($main_menu || $secondary_menu): ?>
  <nav class="main-navigation">
    <div class="main-navigation-inner">
      <?php print theme('links__system_main_menu', array('links' => $main_menu, 'attributes' => array('class' => array('main-menu')), 'heading' => t('Main menu'))); ?>
      <?php print theme('links__system_secondary_menu', array('links' => $secondary_menu, 'attributes' => array('class' => array('secondary-menu')), 'heading' => t('Secondary menu'))); ?>
    </div>
  </nav> <!-- /.main-navigation -->
<?php endif; ?>

<?php if ($breadcrumb): ?>
  <div class="breadcrumb"><?php print $breadcrumb; ?></div>
<?php endif; ?>

<?php if ($messages): ?>
  <div class="system-messages">
    <?php print $messages; ?>
  </div>
<?php endif; ?>

<div class="main-content-wrapper clearfix">

  <main id="main-content" class="main-content">
    <div class="main-content-inner">
      <?php if ($page['highlighted']): ?>
        <div class="highlighted">
          <?php print render($page['highlighted']); ?>
        </div>
      <?php endif; ?>

      <?php print render($title_prefix); ?>

      <?php if ($title): ?>
        <h1 class="title page-title"><?php print $title; ?></h1>
      <?php endif; ?>

      <?php print render($title_suffix); ?>

      <?php if ($tabs): ?>
        <div class="tabs">
          <?php print render($tabs); ?>
        </div>
      <?php endif; ?>

      <?php print render($page['help']); ?>

      <?php if ($action_links): ?>
        <ul class="action-links">
          <?php print render($action_links); ?>
        </ul>
      <?php endif; ?>

      <?php print render($page['content']); ?>

      <?php print $feed_icons; ?>
    </div>
  </main><!-- /.main-content -->

  <?php if ($page['sidebar_first']): ?>
    <section class="sidebar sidebar-first">
      <div class="sidebar-inner">
        <?php print render($page['sidebar_first']); ?>
      </div>
    </section><!-- /.sidebar-first -->
  <?php endif; ?>

  <?php if ($page['sidebar_second']): ?>
    <section class="sidebar sidebar-second">
      <div class="sidebar-inner">
        <?php print render($page['sidebar_second']); ?>
      </div>
    </section> <!-- /.sidebar-second -->
  <?php endif; ?>

</div><!-- /.main-content-wrapper -->

<footer class="footer">
  <div class="footer-inner">
    <?php print render($page['footer']); ?>
  </div>
</div> <!-- /.footer -->
