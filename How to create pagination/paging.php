<?php

define('DBHOST','localhost');
define('DBUSER','databasename');
define('DBPASS','yourpass');
define('DBNAME','databasename');

// You need download adodb lib.
require_once('adodb5/adodb.inc.php');

$db = NewADOConnection('pdo');
$db->Connect('mysql:host='.DBHOST,DBUSER,DBPASS,DBNAME);

if (!$db) die("Connection failed");

$db->Execute("set names utf8");
$db->SetFetchMode(ADODB_FETCH_ASSOC);

// This class is out of Codeigniter

class Pagination {

	public $base_url			= ''; // The page we are linking to
	public $prefix				= ''; // A custom prefix added to the path.
	public $suffix				= ''; // A custom suffix added to the path.

	public $total_rows			=  0; // Total number of items (database results)
	public $per_page			= 10; // Max number of items you want shown per page
	public $num_links			=  2; // Number of "digit" links to show before/after the currently viewed page
	public $cur_page			=  0; // The current page being viewed
	public $first_link			= '&lsaquo; First';
	public $next_link			= '&gt;';
	public $prev_link			= '&lt;';
	public $last_link			= 'Last &rsaquo;';
	public $full_tag_open		= '';
	public $full_tag_close		= '';
	public $first_tag_open		= '';
	public $first_tag_close 	= '&nbsp;';
	public $last_tag_open		= '&nbsp;';
	public $last_tag_close		= '';
	public $first_url			= ''; // Alternative URL for the First Page.
	public $cur_tag_open		= '&nbsp;<strong>';
	public $cur_tag_close		= '</strong>';
	public $next_tag_open		= '&nbsp;';
	public $next_tag_close		= '&nbsp;';
	public $prev_tag_open		= '&nbsp;';
	public $prev_tag_close		= '';
	public $num_tag_open		= '&nbsp;';
	public $num_tag_close		= '';
	public $page_query_string	= FALSE;
	public $query_string_segment= 'per_page';
	public $display_pages		= TRUE;
	public $anchor_class		= '';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	public function __construct($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);
		}

		if ($this->anchor_class != '')
		{
			$this->anchor_class = 'class="'.$this->anchor_class.'" ';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	public function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */
	public function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			return '';
		}

		// Set the base page index for starting page number
		$base_page = 0;

		// Determine the current page number.
        if (isset($_GET[$this->query_string_segment]) && $_GET[$this->query_string_segment] != $base_page)
        {
            $this->cur_page = $_GET[$this->query_string_segment];

            // Prep the current page - no funny business!
            $this->cur_page = (int) $this->cur_page;
        }

		$this->num_links = (int)$this->num_links;

		if ($this->num_links < 1)
		{
			echo 'Your number of links must be a positive number.';
		}

		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = $base_page;
		}

		// Is the page number beyond the result range?
		// If so we show the last page
        if ($this->cur_page > $this->total_rows)
        {
            $this->cur_page = ($num_pages - 1) * $this->per_page;
        }
		

		$uri_page_number = $this->cur_page;

		$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';

		// And here we go...
		$output = '';

		// Render the "First" link
		if  ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1))
		{
			$first_url = ($this->first_url == '') ? $this->base_url : $this->first_url;
			$output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
		{
			$i = $uri_page_number - $this->per_page;	

			if ($i == 0 && $this->first_url != '')
			{
				$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
			else
			{
				$i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
				$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}

		}

		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++)
			{
				$i = ($loop * $this->per_page) - $this->per_page;

				if ($i >= $base_page)
				{
					if ($this->cur_page == $loop)
					{
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
					}
					else
					{
						$n = ($i == $base_page) ? '' : $i;

						if ($n == '' && $this->first_url != '')
						{
							$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$loop.'</a>'.$this->num_tag_close;
						}
						else
						{
							$n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;

							$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
						}
					}
				}
			}
		}

		// Render the "next" link
		if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
		{
			$i = ($this->cur_page * $this->per_page);

			$output .= $this->next_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if ($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = (($num_pages * $this->per_page) - $this->per_page);
			$output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

		return $output;
	}
}

?>

<html>
    <head></head>
    <meta charset="utf-8" />
    <body>
        <?php
        
            $pagination = new Pagination();
            
            $config['base_url'] = 'paging.php?pages=demo';
            $config['total_rows'] = $db->GetOne('SELECT COUNT(*) FROM album');
            $config['per_page'] = '2';
            $config['num_links'] = '5';
            $config['first_link'] = 'first';
            
            $pagination->initialize($config);
            $pl = $pagination->create_links();
            
            $limit = $config['per_page'];
            $offset = (isset($_GET["per_page"]) && $_GET["per_page"]>0 ? $_GET["per_page"].', ' : '');

            //$db->debug=true;
            $list = $db->GetAll('SELECT * FROM album LIMIT '.$offset.$limit);
            //$db->debug=false;
            
            $html = '<table cellspacing="0" cellpadding="0">';
            foreach ($list as $value) {
                $html .= '<tr><td>'
                        . $value['name']
                        . '</td></tr>';
            }
            $html .= '</table>';
            
            print $html;
            print $pl;
        ?>
    </body>
</html>
