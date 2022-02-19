<div id="boxedWrapper">

  <!-- navbar -->
  <nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only"><?php print t('Toggle navigation'); ?></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>

        <?php if ($logo): ?>
          <a class="navbar-brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
            <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
          </a>
        <?php endif; ?>

      </div>
      <div class="navbar-collapse collapse">
        <?php if (module_exists('search') && user_access('search content') && theme_get_setting('enable_navigation_search')): ?>
          <form action="<?php print url('search'); ?>" class="pull-right header-search" role="form" style="display:none;">
            <fieldset>
              <div class="container">
                <div class="form-group">
                  <input name="keys" type="text" class="form-control" placeholder="<?php print theme_get_setting('nagivation_search_placeholder'); ?>">
                </div>
                <button type="submit"><i class="fa fa-search"></i></button>
              </div>
            </fieldset>
          </form>
          <a href="#" id="showHeaderSearch" class="hidden-xs"><i class="fa fa-search"></i></a>
        <?php endif; ?>

        <?php if ($page['header']): ?>
          <div id="header" class="pull-right">
            <?php print render($page['header']); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($page['navigation'])): ?>
          <div id="main-navigation">
            <?php print render($page['navigation']); ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </nav>
  <!-- / navbar -->



  <?php if (!empty($title) || !empty($breadcrumb)): ?>
    <header class="main-header clearfix">
      <div class="container">
        <?php if (!empty($title)): ?>
          <h1 id="page-title" class="page-title pull-left"><?php print $title; ?></h1>
        <?php endif; ?>

        <?php
        if (theme_get_setting('use_default_breadcrumb') && !empty($breadcrumb)) {
          print $breadcrumb;
        } else {
          if ($page['custom_breadcrumb']) {
            print '<div class="breadcrumb pull-right">' . render($page['custom_breadcrumb']) . '</div>';
          }
        }
        ?>
      </div>
    </header>
  <?php endif; ?>

  <?php if ($page['slider']): ?>
    <!-- Main slider -->
    <section id="main-slider">
      <?php print render($page['slider']); ?>
    </section>
  <?php endif; ?>

  <!-- // Main slider -->

  <section class="content-area bg1">
    <div class="container">
      <div class="row">
        <?php if ($page['sidebar_first']): ?>
          <div id="sidebar-second" class="col-md-3 sidebar">
            <?php print render($page['sidebar_first']); ?>
          </div>
        <?php endif; ?>

        <div id="main-page-content"<?php print $content_column_class; ?>>
          <?php if (!empty($page['highlighted'])): ?>
            <div class="highlighted jumbotron"><?php print render($page['highlighted']); ?></div>
          <?php endif; ?>
          <a id="main-content"></a>
          <?php print render($title_prefix); ?>
          <?php print render($title_suffix); ?>
          <?php print $messages; ?>
          <?php if (!empty($tabs)): ?>
            <?php print render($tabs); ?>
          <?php endif; ?>
          <?php if (!empty($page['help'])): ?>
            <?php print render($page['help']); ?>
          <?php endif; ?>
          <?php if (!empty($action_links)): ?>
            <ul class="action-links"><?php print render($action_links); ?></ul>
          <?php endif; ?>
          <?php print render($page['content']); ?>
        </div>
        <?php if ($page['sidebar_second']): ?>
          <div id="sidebar-first" class="col-md-3 sidebar">
            <div id="blog-sidebar">
              <?php print render($page['sidebar_second']); ?>
            </div>
          </div>
        <?php endif; ?>


      </div>

    </div>
  </section>

  <?php if ($page['footer_left'] || $page['footer_right']): ?>
    <section id="prefooter" class="content-area prefooter">
      <div class="container">
        <div class="row">
          <?php if ($page['footer_left']): ?>
            <div class="col-md-6">
              <?php print render($page['footer_left']); ?>
            </div>
          <?php endif; ?>

          <?php if ($page['footer_right']): ?>
            <div class="col-md-6">
              <?php print render($page['footer_right']); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <!-- / section -->
  <?php endif; ?>

  <?php if ($page['footer_first'] || $page['footer_second'] || $page['footer_third'] || $page['footer_fourth'] || $page['footer']): ?>
    <footer>
      <?php if ($page['footer_first'] || $page['footer_second'] || $page['footer_third'] || $page['footer_fourth']): ?>
        <div id="footer-columns">  
          <div class="container mainfooter">
            <div class="row">
              <?php if ($page['footer_first']): ?>
                <aside class="col-md-3 widget">
                  <?php print render($page['footer_first']); ?>
                </aside>
              <?php endif; ?>

              <?php if ($page['footer_second']): ?>
                <aside class="col-md-3 widget">
                  <?php print render($page['footer_second']); ?>
                </aside>
              <?php endif; ?>

              <?php if ($page['footer_third']): ?>
                <aside class="col-md-3 widget">
                  <?php print render($page['footer_third']); ?>
                </aside>
              <?php endif; ?>

              <?php if ($page['footer_fourth']): ?>
                <aside class="col-md-3 widget">
                  <?php print render($page['footer_fourth']); ?>
                </aside>
              <?php endif; ?>

            </div>
          </div>
        </div>
      <?php endif; ?>
      <?php if ($page['footer']): ?>
        <div id="postfooter">
          <div class="container postfooter">
            <div class="row">
              <aside class="col-md-12 widget">
                <?php print render($page['footer']); ?>
              </aside>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </footer>
  <?php endif; ?>


</div>
<!-- / boxedWrapper -->
<a href="#" id="toTop"><i class="fa fa-angle-up"></i></a>