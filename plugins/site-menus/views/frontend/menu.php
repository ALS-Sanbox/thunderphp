<?php $links = $data['links'] ?>
<link href="<?=plugin_http_path('assets/css/all.min.css')?>" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');

nav {
  position: relative;
  z-index: 99;
  width: 100%;
  background: #242526;
}
nav .wrapper {
  max-width: 1300px;
  padding: 0 30px;
  height: 50px;
  line-height: 70px;
  margin: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.wrapper .logo a {
  color: #f2f2f2;
  font-size: 30px;
  font-weight: 600;
  text-decoration: none;
}
.wrapper .nav-links {
  display: inline-flex;
}
.nav-links li {
  list-style: none;
}
.nav-links li a {
  color: #f2f2f2;
  text-decoration: none;
  font-size: 18px;
  font-weight: 500;
  padding: 9px 15px;
  border-radius: 5px;
  transition: all 0.3s ease;
}
.nav-links li a:hover {
  background: #3A3B3C;
}
.nav-links .mobile-item {
  display: none;
}
.nav-links .drop-menu {
  position: absolute;
  background: #242526;
  width: 180px;
  line-height: 45px;
  top: 85px;
  opacity: 0;
  visibility: hidden;
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}
.nav-links li:hover .drop-menu,
.nav-links li:hover .mega-box {
  transition: all 0.3s ease;
  top: 50px;
  opacity: 1;
  visibility: visible;
}
.drop-menu li a {
  display: block;
  padding-left: 15px;
  font-weight: 400;
}
.mega-box {
  position: absolute;
  left: 0;
  width: 100%;
  padding: 0 30px;
  top: 85px;
  opacity: 0;
  visibility: hidden;
}
.mega-box .content {
  background: #242526;
  padding: 25px 20px;
  display: flex;
  justify-content: space-between;
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}
.mega-box .content .row {
  width: calc(25% - 30px);
  line-height: 45px;
}
.content .row img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.content .row header {
  color: #f2f2f2;
  font-size: 20px;
  font-weight: 500;
}
.content .row .mega-links {
  margin-left: -40px;
  border-left: 1px solid rgba(255,255,255,0.09);
}
.row .mega-links li {
  padding: 0 20px;
}
.row .mega-links li a {
  color: #d9d9d9;
  font-size: 17px;
  display: block;
}
.row .mega-links li a:hover {
  color: #f2f2f2;
}
.wrapper .btn {
  color: #fff;
  font-size: 20px;
  cursor: pointer;
  display: none;
}
.wrapper .btn.close-btn {
  position: absolute;
  right: 30px;
  top: 10px;
}

@media screen and (max-width: 970px) {
  .wrapper .btn {
    display: block;
  }
  
  label[for^="showDrop"]::after,
	label[for^="showMega"]::after {
	  content: ' ▼';
	  float: right;
	  transition: transform 0.3s ease;
	}

	input[id^="showDrop"]:checked + label::after,
	input[id^="showMega"]:checked + label::after {
	  content: ' ▲';
	}

  .wrapper .nav-links {
    position: fixed;
    height: 100vh;
    width: 100%;
    max-width: 350px;
    top: 0;
    left: -100%;
    background: #242526;
    display: block;
    padding: 50px 10px;
    line-height: 50px;
    overflow-y: auto;
    transition: all 0.3s ease;
  }
  #menu-btn:checked ~ .nav-links {
    left: 0%;
  }
  #menu-btn:checked ~ .btn.menu-btn {
    display: none;
  }
  #close-btn:checked ~ .btn.menu-btn {
    display: block;
  }
  .nav-links li {
    margin: 15px 10px;
  }
  .nav-links li a {
    padding: 0 20px;
    display: block;
    font-size: 20px;
  }
  .nav-links .drop-menu {
    position: static;
    opacity: 1;
    visibility: visible;
    padding-left: 20px;
    width: 100%;
    max-height: 0;
    overflow: hidden;
    box-shadow: none;
    transition: all 0.3s ease;
  }

  <?php if(!empty($links)): ?>
    <?php foreach($links as $link): ?>
      <?php if(!empty($link->children) && user_can($link->permission)): ?>
        <?php if(empty($link->is_mega)): ?>
          #showDrop<?= $link->id ?>:checked ~ .drop-menu {
            max-height: 500px;
            opacity: 1;
            visibility: visible;
          }
        <?php else: ?>
          #showMega<?= $link->id ?>:checked ~ .mega-box {
            max-height: 1500px;
            opacity: 1;
            visibility: visible;
          }
        <?php endif; ?>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>

  .nav-links .desktop-item {
    display: none;
  }
  .nav-links .mobile-item {
    display: block;
    color: #f2f2f2;
    font-size: 20px;
    font-weight: 500;
    padding-left: 20px;
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.3s ease;
  }
  .nav-links .mobile-item:hover {
    background: #3A3B3C;
  }
  .drop-menu li a {
    font-size: 18px;
  }
  .mega-box {
    position: static;
    opacity: 1;
    visibility: visible;
    padding: 0 20px;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  .mega-box .content {
    flex-direction: column;
    box-shadow: none;
    padding: 20px 20px 0;
  }
  .mega-box .content .row {
    width: 100%;
    margin-bottom: 15px;
    border-top: 1px solid rgba(255,255,255,0.08);
  }
  .content .row .mega-links {
    border-left: none;
    padding-left: 15px;
  }
  .content .row header {
    font-size: 19px;
  }
}

nav input {
  display: none;
}
</style>

<nav>
  <div class="wrapper">
    <div class="logo"><a href="#">Logo</a></div>
    <input type="radio" name="slider" id="menu-btn">
    <input type="radio" name="slider" id="close-btn">
    <ul class="nav-links" style="margin-top: 1rem;">
      <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
      <?php if(!empty($links)): ?>
	  <?php 
		usort($links, function($a, $b){
			return ($a->list_order ?? 10) <=> ($b->list_order ?? 10);
		});
	  ?>
        <?php foreach($links as $link): ?>
          <?php if(user_can($link->permission)): ?>
            <li>
              <a class="<?= !empty($link->children) ? 'desktop-item' : '' ?>" href="<?=ROOT?>/<?= $link->slug ?>">
                <?php if(!empty($link->image) && file_exists($link->image)): ?>
                  <img src="<?= get_image($link->image) ?>" class="rounded-circle" style="width:40px;height:40px;object-fit: cover;">
                <?php endif ?>
                <?= esc(ucfirst($link->title)) ?>
              </a>
              <?php if(!empty($link->children) && empty($link->is_mega)): ?>
                <input type="checkbox" id="showDrop<?= $link->id ?>">
                <label for="showDrop<?= $link->id ?>" class="mobile-item">
					<?php if(!empty($link->image) && file_exists($link->image)): ?>
						<img src="<?= get_image($link->image) ?>" class="rounded-circle" style="width:40px;height:40px;object-fit: cover;">
					<?php endif ?>
					<?= esc(ucfirst($link->title)) ?>
				</label>
                <ul class="drop-menu">
                  <?php foreach($link->children as $child): ?>
                    <?php if(user_can($child->permission)): ?>
                      <li><a href="<?=ROOT?>/<?= $child->slug ?>"><?= esc($child->title) ?></a></li>
                    <?php endif ?>
                  <?php endforeach ?>
                </ul>
              <?php elseif(!empty($link->children) && !empty($link->is_mega)): ?>
                <input type="checkbox" id="showMega<?= $link->id ?>">
                <label for="showMega<?= $link->id ?>" class="mobile-item">
                  <?php if(!empty($link->image) && file_exists($link->image)): ?>
                    <img src="<?= get_image($link->image) ?>" class="rounded-circle" style="width:40px;height:40px;object-fit: cover;">
                  <?php endif ?>
                  <?= esc(ucfirst($link->title)) ?>
                </label>
                <div class="mega-box">
                  <div class="content">
                    <div class="row">
                      <img src="<?= ROOT ?>/<?= $link->mega_image ?>" alt="">
                    </div>
                    <?php foreach($link->children as $child): ?>
                      <?php if(user_can($child->permission)): ?>
                        <div class="row">
                          <header><?= esc($child->title) ?></header>
                          <?php if(!empty($child->grandchildren)): ?>
                            <ul class="mega-links">
                              <?php foreach($child->grandchildren as $grandchild): ?>
                                <li><a href="<?=ROOT?>/<?= $grandchild->slug ?>"><?= esc($grandchild->title) ?></a></li>
                              <?php endforeach ?>
                            </ul>
                          <?php endif ?>
                        </div>
                      <?php endif ?>
                    <?php endforeach ?>
                  </div>
                </div>
              <?php endif ?>
            </li>
          <?php endif ?>
        <?php endforeach ?>
      <?php endif ?>
    </ul>
    <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
  </div>
</nav>
