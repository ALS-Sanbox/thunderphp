<link rel="stylesheet" href="<?=plugin_http_path('assets/css/style.css')?>">

<div id="post_view_container" class="gjs-row">
	<div id="text_cell" class="gjs-cell">
		<div><?= $row->content ?></div>
	</div>
	<div class="container py-4">
		<div class="row g-4 justify-content-center">
			<?php foreach ($matchedPosts as $post): ?>
				<div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
					<div class="card custom-card flex-fill">
						<div class="card-body d-flex flex-column">
							<h5 class="card-title"><?= htmlspecialchars($post->title) ?></h5>
							<p class="card-text flex-grow-1"><?= htmlspecialchars($post->description) ?></p>
							<a href="<?= htmlspecialchars($post->slug ?? '#') ?>" class="btn btn-primary mt-auto">Read More</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div class="blog_footer" style="background-image: url('<?= plugin_http_path('assets/images/grass2.png') ?>');"></div> 
