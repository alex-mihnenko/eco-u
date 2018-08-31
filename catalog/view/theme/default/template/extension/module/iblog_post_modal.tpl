<div itemscope itemprop="blogPost" itemType="http://schema.org/BlogPosting">

    <div class="blog text-align-left">
        <div class="container">

            <?php if ($thumb && isset($moduleData['MainImageEnabled']) && ($moduleData['MainImageEnabled']=='yes')) { ?>
                <div class="image thumbnails">
                    <a href="<?php echo $popup; ?>" title="<?php echo $title; ?>" class="thumbnail">
                    </a>
                </div>
            <?php } ?>

            <div class="post">
                <div class="author text-align-center">
                    <?php if($show_author == 1) { ?>
                        <div class="image" style="background: url(<?php echo $thumb_author; ?>) no-repeat center center scroll; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div>
                        <hr class="indent xs">

                        <h5 class="h5" itemprop="author"><span itemscope itemtype="http://schema.org/Person"><span itemprop="name"><?php echo $author; ?></span></span></h5>
                    <?php } ?> 
                    <hr class="indent xs">

                    <?php if (isset($moduleData['AddThisEnabled']) && ($moduleData['AddThisEnabled']=='yes')) { ?>
                        <div class="iblog-share-links">
                            <a href="http://www.addthis.com/bookmark.php?v=250" class="addthis_button"><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125"  height="16" border="0" alt="Share" /></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
                        </div>
                    <?php } ?>
                </div>
                
                <div class="about">
                    <h2 class="h2" itemprop="headline"><?php echo $title; ?></h2>

                    <div class="grid-row">
                        <div class="grid-col col-4">
                            <span class="date">
                                <i class="fa fa-calendar"></i> <?php echo $date_created; ?>
                            </span>
                            <meta itemprop="datePublished" content="<?php echo $date_created; ?>"/>
                        </div>

                        <div class="grid-col col-4">
                            <span class="tags">
                                <i class="fa fa-tags"></i>
                                
                                <?php if(isset($categories) && !empty($categories)) { ?>
                                    <?php foreach($categories as $category) { ?>
                                        <a href="<?php echo $category['href']; ?>" class="tag"><?php echo $category['name']; ?></a>
                                    <?php } ?> 
                                <?php } ?>
                            </span>
                        </div>
                    </div>
                    <hr class="indent md">


                    <p class="excerpt">
                        <?php echo $excerpt; ?>
                    </p>
                    <hr class="indent xs">

                    <div class="description" itemprop="articleBody">
                        <?php echo $body; ?>
                    </div>

                    <div class="post-keywords">
                        <?php if(is_array($keywords)) { ?>
                            <span><?php echo $iblog_keywords; ?></span>
                            
                            <span itemprop="keywords">
                                <?php foreach($keywords as $keyword) { ?>
                                    <a href="<?php echo $keyword['href']; ?>"><?php echo $keyword['name']; ?></a>, 
                                 <?php } ?>
                            </span>
                         <?php } ?>
                    </div>

                    <?php if (isset($moduleData['DisqusEnabled']) && ($moduleData['DisqusEnabled']=='yes')) {?>
                        <hr />
                        <div class="iblog-post-comments">
                            <script type="text/javascript"> 
                                var disqus_shortname = '<?php echo $moduleData["DisqusShortName"]; ?>';
                                   (function() {
                                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                                })();
                            </script>
                            <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                            <div id="disqus_thread"></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

                
</div>