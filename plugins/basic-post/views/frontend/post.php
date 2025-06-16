<link rel="stylesheet" href="<?=plugin_http_path('assets/css/blog.css')?>">

<div id="postpage_header">
	<div id="postpage_title">
		<b>FEATURED POEMS</b>
	</div>
</div>

<div id="latest_section">
	<div class="latest_post"><a href="<?= htmlspecialchars($latest[0]->slug ?? '', ENT_QUOTES, 'UTF-8') ?>" style="text-decoration: none; color: inherit;">
		<div class="latest_image" style="background-image: url('<?=plugin_http_path('assets/images/flower01.png')?>');"></div>
		<section class="bdg-sect">
			<h1 class="heading"><?= htmlspecialchars($latest[0]->title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
			<p class="latest_desc"><?= htmlspecialchars($latest[0]->description ?? '', ENT_QUOTES, 'UTF-8') ?></p>
		</section></a>
	</div>
	<div class="latest_post"><a href="<?= htmlspecialchars($latest[1]->slug ?? '', ENT_QUOTES, 'UTF-8') ?>" style="text-decoration: none; color: inherit;">
		<div class="latest_image" style="background-image: url('<?=plugin_http_path('assets/images/flower02.png')?>');"></div>
		<section class="bdg-sect">
			<h1 class="heading"><?= htmlspecialchars($latest[1]->title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
			<p class="latest_desc"><?= htmlspecialchars($latest[1]->description ?? '', ENT_QUOTES, 'UTF-8') ?></p>
		</section></a>
	</div>
	<div class="latest_post"><a href="<?= htmlspecialchars($latest[2]->slug ?? '', ENT_QUOTES, 'UTF-8') ?>" style="text-decoration: none; color: inherit;">
		<div class="latest_image" style="background-image: url('<?=plugin_http_path('assets/images/flower03.png')?>');"></div>
		<section class="bdg-sect">
			<h1 class="heading"><?= htmlspecialchars($latest[2]->title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
			<p class="latest_desc"><?= htmlspecialchars($latest[2]->description ?? '', ENT_QUOTES, 'UTF-8') ?></p>
		</section></a>
	</div>
</div>

<div class="container">
  <div class="gutter"></div>
  <div class="post_image" style="background-image: url('<?=plugin_http_path('assets/images/bell2.png')?>');"></div>
  <div class="post_text"><div><?= $row->content ?></div></div>
  <div class="post_date"><div class="category">Categories: <?= implode(', ', $categoryNames) ?></div></div>
  <div class="post_icon" style="background-image: url('<?=plugin_http_path('assets/images/quill.png')?>');"></div>
</div>

<div class="post_bottom_nav">
  <div 
    id="bottom_link" 
    class="mx-auto d-flex justify-content-center"
    style="background-image: url('<?= plugin_http_path('assets/images/grass2.png') ?>');">
    <a href="blog-post" class="new">HOME</a>
  </div>
</div>