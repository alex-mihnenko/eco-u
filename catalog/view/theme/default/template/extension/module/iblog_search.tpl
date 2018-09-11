<?php echo $header; ?>

<hr class="indent lg">

<!--
<ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
</ul> -->


<div class="blog">
    <div class="header">
        <hr class="indent sm">

        <div class="container">
            <div class="grid-row adaptive">
                <div class="grid-col col-75">
                    <h1 class="h2 padding no"><?php echo $heading_title; ?></h1>
                    <hr class="indent md hidden-xxl hidden-xl hidden-lg hidden-md">
                </div>
                
                <div class="grid-col col-25">
                    <div class="blog-search">
                        <input type="text" name="search" value="<?php echo $search; ?>" class="search-field form-control" placeholder="<?php echo $text_keyword; ?>">
                        <button class="btn btn-default" id="iblog-search-button" type="button"> <i class="svg" data-src="icon-search.svg" data-action="search-open"><?php loadSvg('name', 'icon-search.svg'); ?></i> </button>
                    </div>
                </div>
            </div>
        </div>

        <hr class="indent sm">
    </div>
</div>

<hr class="indent md">

<div class="blog">
  <div class="container">
    
    <div class="grid-row adaptive">
        <div class="grid-col col-75">
            <?php echo $content_top; ?>

            <div id="content">
                <?php if (isset($posts)) { ?>
                    <div class="list">
                        <?php foreach ($posts as $post) { ?>
                            <div class="post">
                                <div class="author text-align-center">
                                    <?php if($post['show_author'] == 1) { ?>
                                        <div class="image" style="background: url(<?php echo $post['thumb_author']; ?>) no-repeat center center scroll; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div>
                                        <hr class="indent xs">

                                        <h5 class="h5" itemprop="author"><span itemscope itemtype="http://schema.org/Person"><span itemprop="name"><?php echo $post['author']; ?></span></span></h5>
                                    <?php } ?> 
                                    <hr class="indent xs">

                                    <?php if (isset($moduleData['AddThisEnabled']) && ($moduleData['AddThisEnabled']=='yes')) { ?>
                                        <div class="iblog-share-links">
                                            <a href="http://www.addthis.com/bookmark.php?v=250" class="addthis_button"><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125"  height="16" border="0" alt="Share" /></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="about">
                                   <h2 class="title h2"><a href="<?php echo $post['href']; ?>"><?php echo $post['title']; ?></a></h2>

                                   <div class="grid-row">
                                        <div class="grid-col col-25">
                                            <span class="date">
                                                <i class="fa fa-calendar"></i> <?php echo $post['date_created']; ?>
                                            </span>
                                            <meta itemprop="datePublished" content="<?php echo $date_created; ?>"/>
                                        </div>

                                        <div class="grid-col col-25">
                                            <span class="tags">
                                                <i class="fa fa-tags"></i>
                                                
                                                <?php if(isset($post['categories']) && !empty($post['categories'])) { ?>
                                                    <?php foreach($post['categories'] as $category) { ?>
                                                        <a href="<?php echo $category['href']; ?>" class="tag"><?php echo $category['name']; ?></a>
                                                    <?php } ?> 
                                                <?php } ?>
                                            </span>
                                        </div>
                                    </div>
                                    <hr class="indent xxs">
                                        
                                    <?php if ($post['image']) { ?>
                                        <div class="image"><a href="<?php echo $post['href']; ?>"><img src="<?php echo $post['image']; ?>" title="<?php echo $post['title']; ?>" alt="<?php echo $post['title']; ?>" /></a></div>
                                    <?php } ?>

                                    <div class="description"><?php echo $post['excerpt']; ?></div>
                                    <a href="<?php echo $post['href']; ?>" class="btn btn-primary btn-sm"><?php echo $iblog_button; ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <hr class="indent md">

                    <div class="row">
                        <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                        <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                    </div>
                <?php } ?>

                <?php if (!isset($posts)) { ?>
                    <div class="content"> <?php echo $text_empty; ?> </div>
                <?php } ?>
            </div>

            <?php echo $content_bottom; ?>
        </div>

        <div class="grid-col col-25">
            <?php echo $column_right; ?>
        </div>
    </div>

  </div>
</div>

<hr class="indent xl">

<?php echo $footer; ?> 

<script type="text/javascript"><!--
// $('#button-search').bind('click', function() {
// 	url = '<?php echo $moduleSearch; ?>';
// 	var search = $('#content input[name=\'search\']').prop('value');
// 	if (search) {
// 		url += '&search=' + encodeURIComponent(search);
// 	}
// 	var category_id = $('#content select[name=\'category_id\']').prop('value');
// 	if (category_id > 0) {
// 		url += '&category_id=' + encodeURIComponent(category_id);
// 	}
// 	var sub_category = $('#content input[name=\'sub_category\']:checked').prop('value');
// 	if (sub_category) {
// 		url += '&sub_category=true';
// 	}
// 	var filter_description = $('#content input[name=\'description\']:checked').prop('value');
// 	if (filter_description) {
// 		url += '&description=true';
// 	}
// 	location = url;
// });
// $('#content input[name=\'search\']').bind('keydown', function(e) {
// 	if (e.keyCode == 13) {
// 		$('#button-search').trigger('click');
// 	}
// });
--></script>