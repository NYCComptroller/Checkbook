<div class="checkbook-grading-right">
			<div class="legend">
				<div class="title">New York City Spending</div>
		    	<form action="" name="mglegend">
					<div class="checkbox-grading-legend">	
					<table>
					<tbody><tr class="legend_entry">
						<td><!-- <span name="lengend_checkbox"></span> --><input type="checkbox" checked="" value="aa_mwbe" name="mwbe_right_filter"></td>
						<td class="color"><img src="/<?php print drupal_get_path("module","checkbook_mwbe_agency_grading")?>/images/legend-color-0.png"></td>
						<td class="desc"><span name="legend_description">Asian American</span></td>
					</tr>
					<tr class="legend_entry">
						<td><!-- <span name="lengend_checkbox"></span> --><input type="checkbox" checked=""  value="ba_mwbe" name="mwbe_right_filter"></td>
						<td class="color"><img src="/<?php print drupal_get_path("module","checkbook_mwbe_agency_grading")?>/images/legend-color-1.png"></td>
						<td class="desc"><span name="legend_description">Black American</span></td>
					</tr>
					<tr class="legend_entry">
						<td><!-- <span name="lengend_checkbox"></span> --><input type="checkbox" checked="" value="ba_mwbe" name="mwbe_right_filter"></td>
						<td class="color"><img src="/<?php print drupal_get_path("module","checkbook_mwbe_agency_grading")?>/images/legend-color-2.png"></td>
						<td class="desc"><span name="legend_description">Hispanic American</span></td>
					</tr>
					<tr class="legend_entry">
						<td><!-- <span name="lengend_checkbox"></span> --><input type="checkbox" checked="" value="w_mwbe" name="mwbe_right_filter"></td>
						<td class="color"><img src="/<?php print drupal_get_path("module","checkbook_mwbe_agency_grading")?>/images/legend-color-3.png"></td>
						<td class="desc"><span name="legend_description">Caucasian Women</span></td>
					</tr>
					<tr class="legend_entry">
						<td><!-- <span name="lengend_checkbox"></span> --><input type="checkbox" value="n_mwbe" name="mwbe_right_filter"></td>
						<td class="color"><img src="/<?php print drupal_get_path("module","checkbook_mwbe_agency_grading")?>/images/legend-color-4.png"></td>
						<td class="desc"><span name="legend_description">Non-M/WBE</span></td>
					</tr>
					<tr class="legend_entry">
						<td><!-- <span name="lengend_checkbox"></span> --><input type="checkbox" value="io_mwbe" name="mwbe_right_filter"></td>
						<td class="color"><img src="/<?php print drupal_get_path("module","checkbook_mwbe_agency_grading")?>/images/legend-color-5.png"></td>
						<td class="desc"><span name="legend_description">Individuals &amp; Other</span></td>
					</tr>
					</tbody></table>
					</div>
				</form>
			</div>
			<div clas="right-side-bar-summary-items">
				<div class="first">
		          <label class="label"> Number of Agencies:</label>
		          <span class="value"><?php print $nyc_data['agencies']; ?></span>
	 			</div>
	 			
	 			<div>
		          <label class=label> M/WBE Share:</label>
		          <span class="value"><?php print $nyc_data['mwbe_share']; ?></span>
	 			</div>
	 			
	 			<div>
		          <label class="label"> M/WBE:</label>
		          <span class="value"><?php print $nyc_data['total_mwbe']; ?></span>
	 			</div>
	 			
	 			<div>
		          <label class="label"> Non-M/WBE: </label>
		          <span class="value"><?php print $nyc_data['total_non_mwbe']; ?></span>
	 			</div>
	
	 			<div>
		          <label class="label"> Individuals & Other: </label>
		          <span class="value"><?php print $nyc_data['total_io']; ?></span>
	 			</div>
	 			
	 			<div class="last">
		          <label class="label"> Total: </label>
		          <span class="value"><?php print $nyc_data['total']; ?></span>
	 			</div>
 			</div>
 			
</div>

