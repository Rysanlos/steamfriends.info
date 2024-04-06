<?php
function pagination($query,$per_page=25,$page=1,$url='/'){   
    global $db; 
	
    $query = "SELECT COUNT(*) as num FROM $query";
	$result = $db->query($query);
	$row = $result->fetch_array(MYSQLI_BOTH);
    $total = $row['num'];
    $adjacents = 2; 
      
    $prevlabel = '&lsaquo; Prev';
    $nextlabel = 'Next &rsaquo;';
    $lastlabel = 'Last &rsaquo;&rsaquo;';
      
    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               
      
    $prev = $page - 1;                          
    $next = $page + 1;
      
    $lastpage = ceil($total/$per_page);
      
    $lpm1 = $lastpage - 1;
      
    $pagination = '';
    if($lastpage > 1)
	{
        $pagination .= '<ul class="pagination flex-wrap">';
              
        if($page <= 1)
		{
			$pagination.= '<li class="page-item disabled"><a class="page-link" href="'.$url.'page/'.$prev.'">'.$prevlabel.'</a></li>';
		}
		
        if ($page > 1)
		{
			$pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$prev.'">'.$prevlabel.'</a></li>';
		}
              
        if ($lastpage < 8 + ($adjacents * 2))
		{   
            for ($counter = 1; $counter <= $lastpage; $counter++)
			{
                if($counter == $page)
				{
                    $pagination.= '<li class="page-item active"><a class="page-link">'.$counter.'</a></li>';
				}
                else
				{
                    $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$counter.'">'.$counter.'</a></li>'; 
				}
            }
        }
		elseif($lastpage > 6 + ($adjacents * 2))
		{
            if($page < 1 + ($adjacents * 2))
			{
                for ($counter = 1; $counter < 5 + ($adjacents * 2); $counter++)
				{
                    if($counter == $page)
					{
                        $pagination.= '<li class="page-item active"><a class="page-link">'.$counter.'</a></li>';
					}
                    else
					{
                        $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$counter.'">'.$counter.'</a></li>';
					}
                }
				
                $pagination.= '<li class="page-item"><span class="page-link">...</span></li>';
                $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$lpm1.'">'.$lpm1.'</a></li>';
                $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$lastpage.'">'.$lastpage.'</a></li>';  
            }
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
                $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/1">1</a></li>';
                $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/2">2</a></li>';
                $pagination.= '<li class="page-item"><span class="page-link">...</span></li>';
				
                for($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
                    if($counter == $page)
					{
                        $pagination.= '<li class="page-item active"><a class="page-link">'.$counter.'</a></li>';
					}
                    else
					{
                        $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$counter.'">'.$counter.'</a></li>';
					}
                }
				
                $pagination.= '<li class="page-item"><span class="page-link">...</span></li>';
                $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$lpm1.'">'.$lpm1.'</a></li>';
                $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$lastpage.'">'.$lastpage.'</a></li>';      
            }
			else
			{
                $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/1">1</a></li>';
                $pagination.= '<li class="page-item"><a  class="page-link" href="'.$url.'page/2">2</a></li>';
                $pagination.= '<li class="page-item"><span class="page-link">...</span></li>';
               
			   for($counter = $lastpage - (3 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
			   {
                    if($counter == $page)
					{
                        $pagination.= '<li class="page-item active"><a class="page-link">'.$counter.'</a></li>';
					}
                    else
					{
                        $pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$counter.'">'.$counter.'</a></li>';
					}
                }
            }
        }
          
		if ($page < $counter - 1)
		{
			$pagination.= '<li class="page-item"><a class="page-link" href="'.$url.'page/'.$next.'">'.$nextlabel.'</a></li>';
		}

		if ($page >= $counter - 1)
		{
			$pagination.= '<li class="page-item disabled"><a class="page-link" href="'.$url.'page/'.$next.'">'.$nextlabel.'</a></li>';
		}

		$pagination.= '</ul>';        
    }
      
    return $pagination;
}