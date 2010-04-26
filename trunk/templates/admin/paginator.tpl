<div class="paginator clearfix">
	<div class="emph left"> <span><b>{TOTAL_RECORDS}</b> records </span></div>
	<div class="emph left"> <span>Pages <b>({TOTAL_PAGES})</b> </span></div>
	<div class="right"> 
		<ul class="pagination">
                <!-- BEGIN previous -->
                <li>
                    <a href="{PREVIOUS_LINK}">Previous</a>
                </li>
                <!-- END previous -->
			<!-- BEGIN pages -->
				<!-- BEGIN current_page -->
                <li>
               	 <p>{PAGE_NUMBER}</p>
          		</li>
            	<!-- END current_page -->
				<!-- BEGIN other_page -->
            	<li>
                	<a href="{PAGE_LINK}">{PAGE_NUMBER}</a>
	            </li>
           		 <!-- END other_page -->
			<!-- END pages -->
			<!-- BEGIN next -->
            <li>
                <a href="{NEXT_LINK}">Next</a>
            </li>
            <!-- END next -->
            </ul></div>
</div>
