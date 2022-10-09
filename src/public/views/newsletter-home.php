<?php include 'globals/header.php' ?>

<ul class="breadcrumb">
    <li><a href="/">Home</a></li>
    <li><a href="/newsletter">Newsletter</a></li>
</ul>

<div class="container">
    <div style="text-align: center">
        <h1>Articole din orice categorie</h1>
        <h3>Alege ce vrei sa citesti !</h3>
    </div>
        <?php
            $templateString = '';

            if (!empty($newsletterContainer)) {
                foreach ($newsletterContainer as $container) {
                    $templateString .= "
                <div class='m-5'> 
                    <h4> 
                        <a href='{$currentUrl}/newsletter/{$container['category']->getSlug()}'>
                            Newsletter din categoria {$container['category']->getDenumire()}
                        </a>
                    </h4>
                    <div style='padding: 12px;background-color: white; border: 1px solid royalblue; border-radius: 10px;'>";

                    foreach ($container["content"] as $newsletter) {
                        $content = strip_tags(substr($newsletter->getContent(), 0, 670));

                        $templateString .= "
                            <div class='d-flex m-1'> 
                                  <img width='150px;' height='150px;' src='{$newsletter->getImgPrin()}' alt='img_newsletter'>
                                  <div>
                                    <p class='ml-3'>
                                        <b><a href='$currentUrl/newsletter/{$container['category']->getSlug()}/{$newsletter->getSlug()}'>{$newsletter->getTitlu()}</a></b>
                                    </p>
                                    <div class='ml-3'>
                                        {$content} 
                                    </div>
                                    <div class='ml-3'>
                                         <p style='color: grey'><i>Postat la: {$newsletter->getCreatedAt()}</i></p>
                                    </div>
                                  </div>
                                  
                            </div>
                        ";
                    }

                    $templateString .= "
                        </div>
                    </div>";
                }
            }
        ?>
        <?php echo $templateString;?>

</div>

<?php include  \App\Kernel::getRootDirectory().'/public/views/globals/footer.php' ?>
