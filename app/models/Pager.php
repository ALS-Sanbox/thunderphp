<?php

namespace Core;

defined('ROOT') or die("Direct script access denied");

/**
 * Pager class
 */
class Pager
{
	public $links 		= [];
	public $limit 		= 10;
	public $offset 		= 0;
	public $start 		= 1;
	public $end 		= 1;
	public $page_number = 1;
	public $total_count = 0;
	public $total_pages = 1;

	public function __construct(int $limit = 10, int $total_count = 0, $extras = 1)
	{
		$this->limit = $limit;
		$this->total_count = $total_count;
		$this->total_pages = $total_count > 0 ? (int) ceil($total_count / $limit) : 1;

		$page_number = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$page_number = max(1, min($page_number, $this->total_pages));

		$this->start = max(1, $page_number - $extras);
		$this->end = min($this->total_pages, $page_number + $extras);

		$this->page_number = $page_number;
		$this->offset = ($page_number - 1) * $limit;

		$url = $_GET['url'] ?? 'home';
		$query_string = str_replace("url=","",$_SERVER['QUERY_STRING']);
		$current_link = ROOT . '/'.$url . '?' . trim(str_replace($url, "", $query_string),'&');
		if(!strstr($current_link, "page="))
			$current_link .= '&page='.$page_number;

		$first_link = preg_replace("/page=[0-9]+/", "page=1", $current_link);
		$prev_page = max(1, $page_number - 1);
		$next_page = min($this->total_pages, $page_number + 1);
		$prev_link = preg_replace("/page=[0-9]+/", "page=".$prev_page, $current_link);
		$next_link = preg_replace("/page=[0-9]+/", "page=".$next_page, $current_link);

		$this->links['current'] 	= $current_link;
		$this->links['first'] 		= $first_link;
		$this->links['prev']	 	= $prev_link;
		$this->links['next']	 	= $next_link;

	}

	public function display(): void
	{
		if ($this->total_pages <= 1) {
			return;
		}
		?>
		<nav aria-label="Page navigation example">
		  <ul class="pagination">
		    <li class="page-item <?=$this->page_number <= 1 ? 'disabled' : ''?>">
		    	<a class="page-link" href="<?=$this->links['first']?>">First</a>
		    </li>
		    <li class="page-item <?=$this->page_number <= 1 ? 'disabled' : ''?>">
		    	<a class="page-link" href="<?=$this->links['prev']?>">Prev</a>
		    </li>

		    <?php for($x = $this->start;$x <= $this->end;$x++):?>
			    <li class="page-item <?=$x == $this->page_number ? 'active':''?>">
			    	<a class="page-link" href="<?=preg_replace("/page=[0-9]+/", "page=".$x, $this->links['current'])?>"><?=$x?></a>
			    </li>
			<?php endfor?>

		    <li class="page-item <?=$this->page_number >= $this->total_pages ? 'disabled' : ''?>">
		    	<a class="page-link" href="<?=$this->links['next']?>">Next</a>
		    </li>
		  </ul>
		</nav>
		<?php
	}

	/** @deprecated identical to display(), kept for backwards compatibility */
	public function displayTailwind(): void
	{
		$this->display();
	}

	/** @deprecated identical to display(), kept for backwards compatibility */
	public function displayCustom(): void
	{
		$this->display();
	}

}