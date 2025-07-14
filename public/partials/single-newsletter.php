<?php
/**
 * Template for displaying single newsletter posts
 * Optimized for SEO and LLM crawlers
 *
 * @package Mailchimp_Newsletter_Archive
 */

get_header(); ?>

<div id="primary" class="content-area newsletter-single">
    <main id="main" class="site-main" role="main">
        
        <?php while ( have_posts() ) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('newsletter-article'); ?> itemscope itemtype="https://schema.org/Article">
                
                <!-- Newsletter Header -->
                <header class="newsletter-header">
                    <h1 class="newsletter-title" itemprop="headline"><?php the_title(); ?></h1>
                    
                    <div class="newsletter-meta">
                        <time class="newsletter-date" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                            <?php echo get_the_date(); ?>
                        </time>
                        
                        <?php if ( get_the_modified_date() !== get_the_date() ) : ?>
                            <time class="newsletter-modified" datetime="<?php echo get_the_modified_date('c'); ?>" itemprop="dateModified">
                                (Updated: <?php echo get_the_modified_date(); ?>)
                            </time>
                        <?php endif; ?>
                        
                        <span class="newsletter-type" itemprop="articleSection">Newsletter</span>
                    </div>
                </header>

                <!-- Newsletter Content -->
                <div class="newsletter-content" itemprop="articleBody">
                    <?php the_content(); ?>
                </div>

                <!-- Newsletter Footer -->
                <footer class="newsletter-footer">
                    <div class="newsletter-actions">
                        <!-- Removed: View in Mailchimp and View All Newsletters links -->
                    </div>
                </footer>

            </article>

            <!-- Navigation -->
            <nav class="newsletter-navigation">
                <div class="nav-links">
                    <?php
                    $prev_post = get_previous_post( true, '', 'newsletter' );
                    $next_post = get_next_post( true, '', 'newsletter' );
                    
                    if ( $prev_post ) : ?>
                        <div class="nav-previous">
                            <a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" rel="prev">
                                <span class="nav-subtitle">← Previous</span>
                                <span class="nav-title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
                            </a>
                        </div>
                    <?php endif;
                    
                    if ( $next_post ) : ?>
                        <div class="nav-next">
                            <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" rel="next">
                                <span class="nav-subtitle">Next →</span>
                                <span class="nav-title"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>

        <?php endwhile; ?>

    </main>
    
    <?php get_sidebar(); ?>
</div>

<!-- Structured Data for SEO/LLM -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "<?php echo esc_js( get_the_title() ); ?>",
    "datePublished": "<?php echo get_the_date('c'); ?>",
    "dateModified": "<?php echo get_the_modified_date('c'); ?>",
    "author": {
        "@type": "Organization",
        "name": "<?php echo esc_js( get_bloginfo('name') ); ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo esc_js( get_bloginfo('name') ); ?>",
        "url": "<?php echo esc_js( home_url() ); ?>"
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo esc_js( get_permalink() ); ?>"
    },
    "articleSection": "Newsletter",
    "url": "<?php echo esc_js( get_permalink() ); ?>"
    <?php if ( has_excerpt() ) : ?>,
    "description": "<?php echo esc_js( get_the_excerpt() ); ?>"
    <?php endif; ?>
}
</script>

<?php get_footer(); ?> 