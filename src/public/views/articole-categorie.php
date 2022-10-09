<?php include 'globals/header.php' ?>

<ul class="breadcrumb">
    <li><a href="/">Home</a></li>
    <li><a href="/newsletter">Newsletter</a></li>
    <li><a href="<?php $currentUrl.'/newsletter/'.$category[0]->getSlug() ?>"> Newsletter <?php echo $category[0]->getDenumire() ?></a></li>
</ul>

<div class="container">
    <div style="text-align: center">
        <h2>Articole din categoria <?php echo $category[0]->getDenumire() ?></h2>
    </div>
    <div class="d-flex" style="border-radius: 20px;justify-content: space-around; background-color: ghostwhite;">
            <?php
                echo $formNewsletterSearch["newsletterForm"].
                 '<div class="d-flex" style="justify-content: space-around"></div>'.
                   $formNewsletterSearch["query"].
                    '<div style="margin-top: 1.9rem !important;">'.
                   $formNewsletterSearch["go"] .
                    '</div>'.
                 '</div>'.
                   $formNewsletterSearch["_token"];
            ?>
    </div>
    <div id="container_newsletter" class="mt-5">
    <?php
    $templateString = '';
    $count = 0;

    if (!empty($newsletterContainer)) {
        foreach ($newsletterContainer as $newsletter) {
            $content = strip_tags(substr($newsletter->getContent(), 0, 760));

            $templateString .= "
            <div class='d-flex m-3' style='padding: 6px; border: 1px solid cornflowerblue; border-radius: 12px;'> 
                  <img width='150px;' height='150px;' src='../{$newsletter->getImgPrin()}' alt='img_newsletter'>
                  <div>
                    <p class='ml-3'>
                        <b><a href='$currentUrl/newsletter/{$category[0]->getSlug()}/{$newsletter->getSlug()}'>{$newsletter->getTitlu()}</a></b>
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
            if ($count === 8) {
                $templateString .= '<span id="page_1"></span>';
            }
            $count++;
        }
        echo $templateString;
    } else {
        echo 'Nu exista newsletter in categoria respectiva';
    }?>
    </div>
</div>

<script>
    let arrayOfseenElement = [];

    let page = 1;

    $(document).ready( () => {
        let currentUrl = `<?php echo $currentUrl.'/newsletter/'.$category[0]->getSlug() ?>`;
        let form = document.getElementById('newsletterForm');
        let query = document.getElementById('query');
        form.addEventListener('submit', (e)=> {
            e.preventDefault();

            if (query.value.length > 0) {
                currentUrl += '?query=' + query.value;
            }
            window.location.href = currentUrl;
        });

        arrayOfseenElement.push({
            element: document.getElementById('page_1'),
            seen: false
        });

        $(document).on('scroll', () => {
            for (let i = 0; i < arrayOfseenElement.length; i++) {
                if (arrayOfseenElement[i] && isInViewPort(arrayOfseenElement[i].element) && !arrayOfseenElement[i].seen) {
                    arrayOfseenElement[i].seen = true;
                    ajaxGetDataOfNewsletter();
                }
            }
        });

    });

    function isInViewPort(element)
    {
        let rect = element.getBoundingClientRect();

        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    function ajaxGetDataOfNewsletter()
    {
        let url = `<?php echo $currentUrl.'/newsletter/'.$category[0]->getSlug().'/ajax/ajaxGetNewsletterOfCategory'?>` + `?pageNr=${page}&key=<?php echo $apiKey;?>`;

        let xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
        xhr.send()
        xhr.onload = (e) => {
            if (xhr.readyState === XMLHttpRequest.DONE &&  xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.length > 0) {
                    injectTemplateOnCattegoryPagination(response);
                }
            }
        }


    }

    function injectTemplateOnCattegoryPagination(containerOfNewsletter)
    {
        let template = ``;

        page++;

        let count = 0;

        containerOfNewsletter.forEach((element) => {
            let content = element.content.replace(/(<([^>]+)>)/gi, "").slice(0, 760);

            template += `
              <div class='d-flex m-3' style='padding: 6px; border: 1px solid cornflowerblue; border-radius: 12px;'>
                  <img width='150px;' height='150px;' src='../${element.img_prin}' alt='img_newsletter'>
                  <div>
                    <p class='ml-3'>
                        <b><a href='<?php echo $currentUrl."/newsletter/".$category[0]->getSlug()."/"?>${element.slug.trim()}'>${element.titlu}</a></b>
                    </p>
                    <div class='ml-3'>
                        ${content}
                    </div>
                    <div class='ml-3'>
                         <p style='color: grey'><i>Postat la: ${element.created_at}</i></p>
                    </div>
                  </div>
            </div>
            `
            if (count === 8) {
                template += `<span id="page_${page}"></span>`
            }
            count++;
        });

        $('#container_newsletter').append(template)

        if (document.getElementById(`page_${page}`)) {
            arrayOfseenElement.push(
                {
                    element: document.getElementById(`page_${page}`),
                    seen: false
                }
            )
        }
    }


</script>

<?php include  \App\Kernel::getRootDirectory().'/public/views/globals/footer.php' ?>
