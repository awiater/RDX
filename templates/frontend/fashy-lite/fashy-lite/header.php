<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js" >
<!-- start -->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="format-detection" content="telephone=no">
	
	<?php wp_head();?>
</head>		
<!-- start body -->
<body <?php body_class(); ?> >
	<!-- start header -->
			<!-- fixed menu -->		
			<?php 
			?>	
			<?php if(fashy_globals('display_scroll')) { ?>
			<div class="pagenav fixedmenu">						
				<div class="holder-fixedmenu">							
					<div class="logo-fixedmenu">								
					<?php if(fashy_globals('scroll_logo')){ ?>
						<a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url(fashy_data('scroll_logo')); ?>" data-rjs="3" alt="<?php esc_html(bloginfo('name')); ?> - <?php esc_html(bloginfo('description')) ?>" ></a>
					<?php } ?>
					</div>
						<div class="menu-fixedmenu home">
						<?php
						if ( has_nav_menu( 'fashy_scrollmenu' ) ) {
						wp_nav_menu( array(
						'container' =>false,
						'container_class' => 'menu-scroll',
						'theme_location' => 'fashy_scrollmenu',
						'echo' => true,
						'fallback_cb' => 'fashy_fallback_menu',
						'before' => '',
						'after' => '',
						'link_before' => '',
						'link_after' => '',
						'depth' => 0,
						'walker' => new fashy_Walker_Main_Menu())
						);
						}
						?>	
					</div>
				</div>	
			</div>
			<?php } ?>
				<header>
				<!-- top bar -->
				<?php if(fashy_globals('top_bar')) { ?>
					<div class="top-wrapper">
						<div class="top-wrapper-content">
							<div class="top-left">
								<?php if(is_active_sidebar( 'fashy_sidebar-top-left' )) { ?>
									<?php dynamic_sidebar( 'fashy_sidebar-top-left' ); ?>
								<?php } ?>
							</div>
							<div class="top-right">
								<?php if(is_active_sidebar( 'fashy_sidebar-top-right' )) { ?>
									<?php dynamic_sidebar( 'fashy_sidebar-top-right' ); ?>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>			
					<div id="headerwrap">			
						<!-- logo and main menu -->
						<div id="header">
							<div class="header-image">
							<!-- respoonsive menu main-->
							<!-- respoonsive menu no scrool bar -->
							<div class="respMenu noscroll">
								<div class="resp_menu_button"><i class="fa fa-list-ul fa-2x"></i></div>
								<?php 
								if ( has_nav_menu( 'fashy_respmenu' ) ) {
									$menuParameters =  array(
									  'theme_location' => 'fashy_respmenu', 
									  'walker'         => new fashy_Walker_Responsive_Menu(),
									  'echo'            => false,
									  'container_class' => 'menu-main-menu-container',
									  'items_wrap'     => '<div class="event-type-selector-dropdown">%3$s</div>',
									);
									echo strip_tags(wp_nav_menu( $menuParameters ), '<a>,<br>,<div>,<i>,<strong>' );
								}
								?>	
							</div>	
							<!-- logo -->
							<?php if(fashy_data('logo_position') == 1 ){ 
								fashy_logo();
							} ?>
							</div>
							<!-- main menu -->
							<div class="pagenav"> 	
								<div class="pmc-main-menu">
								<?php
									if ( has_nav_menu( 'fashy_mainmenu' ) ) {	
										wp_nav_menu( array(
										'container' =>false,
										'container_class' => 'menu-header home',
										'menu_id' => 'menu-main-menu-container',
										'theme_location' => 'fashy_mainmenu',
										'echo' => true,
										'fallback_cb' => 'fashy_fallback_menu',
										'before' => '',
										'after' => '',
										'link_before' => '',
										'link_after' => '',
										'depth' => 0,
										'walker' => new fashy_Walker_Main_Menu()));								
									} ?>											
								</div> 	
							</div> 
							<?php if(fashy_data('logo_position') == 2){ 
								fashy_logo();
							} ?>							
						</div>
					</div> 												
				</header>	
				<?php
				if(function_exists( 'putRevSlider')){						
					if(fashy_globals('rev_slider') && is_front_page() ){ ?>
						<div id="fashy-slider-wrapper">
							<div id="fashy-slider">
								<?php putRevSlider(fashy_data('rev_slider'),"homepage") ?>
							</div>
						</div>
					<?php } ?>
				<?php } ?>		
				<?php 					
				if(is_front_page() && fashy_globals('use_categories')){ ?>
						<?php fashy_block_one(); ?>
					<?php } ?>	
					<?php if(is_front_page() && fashy_globals('use_block2') ){ ?>	
						<?php fashy_block_two(); ?>
					<?php } ?>				
				<?php if(is_front_page()){ ?>
				<?php fashy_custom_layout(); ?>
				<?php } ?>				
