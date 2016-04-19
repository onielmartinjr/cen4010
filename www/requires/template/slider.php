<section id="main-slider" class="no-margin" style="margin-top:-20px !important;">
	<div class="carousel slide wet-asphalt">
		<ol class="carousel-indicators">
			<li data-target="#main-slider" data-slide-to="0" class="active"></li>
			<li data-target="#main-slider" data-slide-to="1"></li>
		</ol>
		<div class="carousel-inner">
			<div class="item active" style="background-image: url(UI-links/images/slider/bg2.jpg)">
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<div class="carousel-content center centered">
								<h2 class="boxed animation animated-item-1"><?php 
									echo (isset($website_settings['main_site_heading']) ? 
										$website_settings['main_site_heading'] : 'Welcome to our site!'); 
								?></h2><br>
								<p class="boxed animation animated-item-2"><?php 
									echo (isset($website_settings['main_site_text']) ? 
										$website_settings['main_site_text'] : 'Fall in love with one of our adorable pets!'); 
								?></p>
							</div>
						</div>
					</div>
				</div>
			</div><!--/.item-->
			<div class="item" style="background-image: url(UI-links/images/slider/bg1.jpg)">
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<div class="carousel-content center centered">
								<h2 class="boxed animation animated-item-1"><?php 
									echo (isset($website_settings['main_site_heading']) ? 
										$website_settings['main_site_heading'] : 'Welcome to our site!'); 
								?></h2><br>
								<p class="boxed animation animated-item-2"><?php 
									echo (isset($website_settings['main_site_text']) ? 
										$website_settings['main_site_text'] : 'Fall in love with one of our adorable pets!'); 
								?></p>
								<!--<a class="btn btn-md animation animated-item-3" href="#">Learn More</a>-->
							</div>
						</div>
					</div>
				</div>
			</div><!--/.item-->
		</div><!--/.carousel-inner-->
	</div><!--/.carousel-->
	<a class="prev hidden-xs" href="#main-slider" data-slide="prev">
		<i class="icon-angle-left"></i>
	</a>
	<a class="next hidden-xs" href="#main-slider" data-slide="next">
		<i class="icon-angle-right"></i>
	</a>
</section><!--/#main-slider-->
