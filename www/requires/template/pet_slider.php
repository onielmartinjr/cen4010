<section id="recent-works" class="emerald">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<h3>Latest Pets</h3>
				<p>Navigate this section to preview the latest pets.</p>
				<div class="btn-group">
					<a class="btn btn-danger" href="#scroller" data-slide="prev"><i class="icon-angle-left"></i></a>
					<a class="btn btn-danger" href="#scroller" data-slide="next"><i class="icon-angle-right"></i></a>
				</div>
				<p class="gap"></p><br>
				<strong><p><a href="search_pets.php">Search for Pets</a></p></strong>
			</div>
			<div class="col-md-9">
				<div id="scroller" class="carousel slide">
					<div class="carousel-inner">
						<div class="item active">
							<div class="row">
							<?php 
								$pets = get_slider_pets();
								if(count($pets) > 0){
									$num = 0;
									foreach($pets as $pet){ 
										$num++;
							?>
									<div class="col-xs-4">
										<div class="portfolio-item">
											<div class="item-inner">
												<img class="img-responsive" src="uploads/<?php echo $pet->image_wk->filename; ?>">
												<h5 class="center">
													<?php if($pet->name !== "") echo $pet->name; else echo '&nbsp;'; ?>
												</h5></img>
												<div class="overlay">
													<a class="preview btn btn-danger" title="<?php if($pet->name !== "") echo $pet->name; else echo '&nbsp;'; ?>" href="uploads/<?php echo $pet->image_wk->filename; ?>" rel="prettyPhoto"><i class="icon-eye-open"></i></a>
                                                </div>
											</div>
										</div>
									</div>                            
							<?php
										if($num%3 == 0 ){
											echo '</div></div><div class="item"><div class="row">';
										}
									}	
								} 
							?>
							</div><!--/.row-->
						</div><!--/.item-->
					</div>
				</div>
			</div>
		</div><!--/.row-->
	</div>
</section>