<link rel="stylesheet" href="<?=plugin_http_path('assets/css/blog.css')?>">

<div id="i4u1ycu" class="gjs-row">
	<div id="id6peiu" class="gjs-cell">
		<div id="i9kpg0o">
			<b id="ish4vun">FEATURED POEMS</b>
		</div>
	</div>
</div>

<div id="latest_section" class="gjs-row">
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
    style="
      background-image: url('<?= plugin_http_path('assets/images/grass2.png') ?>');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    "
  >
    <a href="blog-post" class="new">HOME</a>
  </div>
</div>

<style>* { box-sizing: border-box; } body {margin: 0;}*{box-sizing:border-box;}body{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;}#i6jl{transform:scale(1) translate(0px, 0px);}#ifhi{left:0px;top:0px;}#ir9pf{display:none;}#imnx2{pointer-events:none;}#gjs-tools{pointer-events:none;display:none;}#ibb27{pointer-events:all;}#i9h4w{pointer-events:all;top:-1000px;left:-1000px;display:none;}#ie9tn{font-size:1.4rem;padding-top:0px;padding-right:4px;padding-bottom:2px;padding-left:4px;}#iajnh{pointer-events:none;display:none;}#i5uio{pointer-events:all;}#ixw0nj{display:none;}#iusb53{display:none;}#gjs-clm-new{display:none;}#i7i3s3{display:none;}#i5sff7{display:none;}#i7phhu{display:none;}#ib4ogi{display:none;}#ixifpc{display:none;}#in3heg{display:none;}#i6lw2o{display:none;}#ixd2pl{display:none;}#i1092i{display:none;}#ivlu6x{display:none;}#ih59f3{display:none;}#iavr3b{display:none;}#i96tfc{display:none;}#iyw8gk{display:none;}#ikvs0l{display:none;}#if9tuo{display:none;}#i4z9qs{display:none;}#i4ee9e{display:none;}#iipp4z{display:none;}#iskt6k{display:none;}#if5cg4{display:none;}#i8kjsw{display:none;}#ielm1c{display:none;}#iofe5p{display:none;}#igl0bq{display:none;}#idegtk{display:none;}#ik90zj{display:none;}#inucmn{display:none;}#i4pr9v{display:none;}#i6cayy{display:none;}#idqzru{display:none;}#id8gyn{display:none;}#idss61{display:none;}#i32xu4{display:none;}#ihbu5p{display:none;}#igj0bv{display:none;}#i6pr9p{display:none;}#i3xyoyv{display:none;}#ipb0d93{display:none;}#i98y57w{display:none;}#itzcd0s{display:none;}#ief2hen{background-color:black;}#ijp2lzo{display:none;}#ihp0zq2{display:none;}#i2b3vhn{display:none;}#is6uxs2{display:none;}#ifcu7ci{display:none;}#i55zfxq{display:none;}#is23jjk{display:none;}#ibbnd5i{display:none;}#iiarrvm{display:none;}#it66jw6{display:none;}#i5yjhi2{display:none;}#izgxyww{display:none;}#iuqtbfb{display:none;}#id7pskp{display:none;}#i6phtew{background-color:black;}#i0jak29{display:none;}#i5xfn9r{display:none;}#iqa1fhc{display:none;}#iye4clj{display:none;}#i4xyar4{display:none;}#iq4giaj{display:none;}#i1pwmig{transform:scale(1) translate(0px, 0px);}#i1eq3la{left:0px;top:0px;}#i3udyr3{display:none;}#i7xn23a{pointer-events:none;}#tools{pointer-events:none;}#ikt6ubk{pointer-events:all;}#itfaxvy{pointer-events:none;display:none;}#iamc3a2{pointer-events:all;}#i7sqqxc{clear:both;}#iozl7i5{display:none;}.gjs-row{display:table;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;width:100%;}.gjs-cell{width:8%;display:table-cell;height:75px;}#i4u1ycu{height:297px;padding-top:10px;padding-right:80px;padding-bottom:10px;padding-left:80px;text-align:center;margin-top:0px;margin-right:0px;margin-bottom:10px;margin-left:0px;}#i9kpg0o{padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;text-align:center;font-family:Georgia, serif;font-size:68px;margin-top:4px;margin-right:0px;margin-bottom:0px;margin-left:0px;}#id6peiu{border-top-width:5px;border-right-width:5px;border-bottom-width:5px;border-left-width:5px;border-top-style:solid;border-right-style:solid;border-bottom-style:solid;border-left-style:solid;border-top-color:black;border-right-color:black;border-bottom-color:black;border-left-color:black;border-image-source:initial;border-image-slice:initial;border-image-width:initial;border-image-outset:initial;border-image-repeat:initial;}#iqpoicl-2{height:151px;background-color:rgb(255, 255, 255);}.bdg-sect{max-width:241px;}#i7q0jvv{color:black;text-align:center;border-top-width:5px;border-right-width:5px;border-bottom-width:5px;border-left-width:5px;border-top-style:solid;border-right-style:solid;border-bottom-style:solid;border-left-style:solid;border-top-color:black;border-right-color:black;border-bottom-color:black;border-left-color:black;border-image-source:initial;border-image-slice:initial;border-image-width:initial;border-image-outset:initial;border-image-repeat:initial;width:240px;height:240px;}#i7q0jvv-2{color:black;text-align:center;border-top-width:5px;border-right-width:5px;border-bottom-width:5px;border-left-width:5px;border-top-style:solid;border-right-style:solid;border-bottom-style:solid;border-left-style:solid;border-top-color:black;border-right-color:black;border-bottom-color:black;border-left-color:black;border-image-source:initial;border-image-slice:initial;border-image-width:initial;border-image-outset:initial;border-image-repeat:initial;width:240px;height:240px;}#i7q0jvv-3{color:black;text-align:center;border-top-width:5px;border-right-width:5px;border-bottom-width:5px;border-left-width:5px;border-top-style:solid;border-right-style:solid;border-bottom-style:solid;border-left-style:solid;border-top-color:black;border-right-color:black;border-bottom-color:black;border-left-color:black;border-image-source:initial;border-image-slice:initial;border-image-width:initial;border-image-outset:initial;border-image-repeat:initial;width:240px;height:240px;}#ia5kh2o{width:340px;border-top-width:5px;border-right-width:5px;border-bottom-width:5px;border-left-width:5px;border-top-style:solid;border-right-style:solid;border-bottom-style:solid;border-left-style:solid;border-top-color:black;border-right-color:black;border-bottom-color:black;border-left-color:black;border-image-source:initial;border-image-slice:initial;border-image-width:initial;border-image-outset:initial;border-image-repeat:initial;height:340px;top:76px;right:-21px;position:absolute;left:-70px;align-self:flex-start;margin-top:113px;margin-right:0px;margin-bottom:0px;margin-left:0px;}#io6i{background-color:#ffca95;}@media (max-width: 768px){.gjs-cell{width:100%;display:block;}} </style>
