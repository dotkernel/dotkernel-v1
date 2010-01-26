<table class="g_box" cellpadding="0" cellspacing="1">
    <tr>
        <td class="emph" width="100">
            <span>Pages ({TOTAL_PAGES}): </span>
        </td>
        <td>
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
            </ul>
        </td>
    </tr>
</table>
