<?php if(user_can('manage_post_layout')): ?>
  <link rel="stylesheet" href="<?=ROOT?>/assets/grapesjs/grapes.min.css">

<?php
  $savedLayout = setting('post_default_layout');
  if (!is_array($savedLayout) || empty($savedLayout['html'])) {
      $savedLayout = [
          'html' => '<div class="post-layout-wrapper" style="max-width:800px;margin:0 auto;padding:40px 20px;">'
              . '<div class="post-title-block" style="padding:15px;border:2px dashed #999;background:#f8f9fa;font-size:2rem;font-weight:bold;">{{POST_TITLE}}</div>'
              . '<div class="post-content-block" style="padding:20px;border:2px dashed #999;background:#f8f9fa;margin-top:10px;">{{POST_CONTENT}}</div>'
              . '<div class="post-list-block" style="padding:20px;border:2px dashed #999;background:#f8f9fa;margin-top:10px;">{{POST_LIST}}</div>'
              . '</div>',
          'css'  => '',
      ];
  }
?>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Post Layout</h4>
      <small class="text-muted">Design the template every post uses by default. Drag in "Post Title", "Post Content", and "Post List" to mark where each post's title, body, and a list of other posts will appear.</small>
    </div>
    <div>
      <button id="saveLayoutBtn" class="btn btn-danger">Save Layout</button>
      <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-secondary">Back to Posts</a>
    </div>
  </div>

  <form method="post" action="" id="layoutForm">
    <input type="hidden" name="_token" value="<?= csrf() ?>">
    <input type="hidden" name="layout_html" id="layout_html">
    <input type="hidden" name="layout_css" id="layout_css">
  </form>

  <div id="gjs">
    <?= $savedLayout['html'] ?>
    <style><?= $savedLayout['css'] ?></style>
  </div>
</div>

<script src="<?=ROOT?>/assets/grapesjs/grapes.min.js"></script>
<script src="<?=ROOT?>/assets/grapesjs/grapesjs-blocks-basic.min.js"></script>
<script src="<?=ROOT?>/assets/grapesjs/grapesjs-plugin-forms.min.js"></script>
<script src="<?=ROOT?>/assets/grapesjs/grapesjs-navbar.min.js"></script>
<script src="<?=ROOT?>/assets/grapesjs/grapesjs-custom-code.min.js"></script>
<script src="<?=ROOT?>/assets/grapesjs/grapesjs-preset-webpage.min.js"></script>
<script src="<?=ROOT?>/assets/grapesjs/nav-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/hero-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/footer-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/feature-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/cta-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/testimonial-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/pricing-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/team-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/faq-blocks.js?v=1"></script>
<script src="<?=ROOT?>/assets/grapesjs/contact-blocks.js?v=1"></script>
<script>
  const layoutEditor = grapesjs.init({
    container: '#gjs',
    fromElement: true,
    height: '80vh',
    width: 'auto',
    showOffsets: true,
    allowScripts: true,
    storageManager: false,
    plugins: [
      'gjs-blocks-basic',
      'grapesjs-plugin-forms',
      'grapesjs-custom-code',
      'grapesjs-navbar',
      'grapesjs-preset-webpage',
    ],
    pluginsOpts: {
      'grapesjs-custom-code': {
        modalTitle: 'Edit Custom Code',
      },
    },
  });

  layoutEditor.BlockManager.add('post-title', {
    label: 'Post Title',
    category: 'Post',
    content: '<div class="post-title-block" style="padding:15px;border:2px dashed #999;background:#f8f9fa;font-size:2rem;font-weight:bold;">{{POST_TITLE}}</div>',
  });

  layoutEditor.BlockManager.add('post-content', {
    label: 'Post Content',
    category: 'Post',
    content: '<div class="post-content-block" style="padding:20px;border:2px dashed #999;background:#f8f9fa;">{{POST_CONTENT}}</div>',
  });

  layoutEditor.BlockManager.add('post-list', {
    label: 'Post List',
    category: 'Post',
    content: '<div class="post-list-block" style="padding:20px;border:2px dashed #999;background:#f8f9fa;">{{POST_LIST}}</div>',
  });

  [
    'registerNavBlocks',
    'registerHeroBlocks',
    'registerFooterBlocks',
    'registerFeatureBlocks',
    'registerCtaBlocks',
    'registerTestimonialBlocks',
    'registerPricingBlocks',
    'registerTeamBlocks',
    'registerFaqBlocks',
    'registerContactBlocks',
  ].forEach(function (fn) {
    if (window[fn]) window[fn](layoutEditor);
  });

  document.getElementById('saveLayoutBtn').addEventListener('click', function () {
    document.getElementById('layout_html').value = layoutEditor.getHtml();
    document.getElementById('layout_css').value = layoutEditor.getCss();
    document.getElementById('layoutForm').submit();
  });
</script>

<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
  <div class="card-body">
    <h5 class="card-title text-danger fw-bold">Access Denied</h5>
    <p class="card-text text-muted">You don't have permission for this action.</p>
  </div>
</div>
<?php endif ?>
