<?php get_header(); ?>
<!-- Block Banner -->
<div class="fre-background" id="background_banner">
	<div class="video-wrapper">
		<video autoplay="" loop="" class="video" id="video-frame">
            <source src="https://cadpad3d.co.uk/wp-content/uploads/2017/10/CadPad-Header.mp4" type="video/mp4">
            <source src="https://cadpad3d.co.uk/wp-content/uploads/2017/10/CadPad-Header.webm" type="video/webm">
			Video not supported.
		</video>
	</div>
	<div class="fre-bg-content">
		<div class="container">
			<h1 id="title_banner"><?php echo get_theme_mod("title_banner") ? get_theme_mod("title_banner") : __("Find professional 3D modellers & 3D designers for your projects and turn your ideas into reality", ET_DOMAIN);?></h1>
			<?php if(!is_user_logged_in()){ ?>
				<a class="fre-btn" href="<?php echo et_get_page_link( "register" ) ?>/?role=employer"><?php _e( 'SIGN UP', ET_DOMAIN ); ?></a>
			<?php }else{ ?>
                    <?php if(ae_user_role($user_ID) == FREELANCER){ ?>
                        <a class="fre-btn" href="<?php echo get_post_type_archive_link( PROJECT ); ?>"><?php _e('Find Projects', ET_DOMAIN);?></a>
                        <a class="fre-btn" href="<?php echo et_get_page_link('profile').'#tab_profile_details'; ?>"><?php _e('Update Profile', ET_DOMAIN);?></a>
                    <?php }else{ ?>
                        <a class="fre-btn" href="<?php echo et_get_page_link('submit-project'); ?>"><?php _e('Post a Project', ET_DOMAIN);?></a>
                        <a class="fre-btn" href="<?php echo get_post_type_archive_link( PROFILE ); ?>"><?php _e('Find Freelancers', ET_DOMAIN);?></a>
                    <?php } ?>
			<?php } ?>
		</div>
	</div>
</div>
<!-- Block Banner -->
<!-- how Banner -->
<div class="container slider-wrapper">
<h1 style="text-align:center">How it Works</h1>
	<div class="how-btn-holder">
        <a id="designee-how" class="explore-btn button how-btn fre-btn">User</a>
        <a id="designer-how" class="explore-btn button how-btn fre-btn">Freelancer</a>
    </div>
    <section class="how-slider">
        <div class="how-section">
            <div class="how-section-inner">
            <div class="section-title">User</div>
                <article class="circle-container">
                    <div class="circle">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sign-up.jpg">
                    </div>
                    <div class="how-title">Sign Up</div>
                </article>
                <article class="circle-container">
                    <div class="circle">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/post-work.jpg">
                    </div>
                    <div class="how-title">Post Work</div>
                </article>
                <article class="circle-container">
                    <div class="circle">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/download-files.jpg">
                    </div>
                    <div class="how-title">Download Files</div>
                </article>
            </div>
        </div> 
        <div class="how-section">
            <div class="how-section-inner">
            <div class="section-title">Freelancer</div>
                <article class="circle-container">
                    <div class="circle">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sign-up.jpg">
                    </div>
                    <div class="how-title">Sign Up</div>
                </article>
                <article class="circle-container">
                    <div class="circle">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/find-work.jpg">
                    </div>
                    <div class="how-title">Find Work</div>
                </article>
                <article class="circle-container">
                    <div class="circle">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/upload-file.jpg">
                    </div>
                    <div class="how-title">Upload Files</div>
                </article>
                <article class="circle-container">
                    <div class="circle">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/get-paid.jpg">
                    </div>
                    <div class="how-title">Get Paid</div>
                </article>
            </div>
        </div> 
    </section>
</div>
<?php get_footer(); ?>