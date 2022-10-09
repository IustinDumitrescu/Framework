<?php include 'globals/header.php' ?>

<ul class="breadcrumb">
    <li><a href="/">Home</a></li>
    <li><a href="/newsletter">Newsletter</a></li>
    <li><a href="<?php echo '/newsletter/'.$category[0]->getSlug() ?>"> Newsletter <?php echo $category[0]->getDenumire() ?></a></li>
    <li><a href="<?php echo '/newsletter/'.$category[0]->getSlug().'/'.$newsletter[0]->getTitlu() ?>"> Newsletter <?php echo $newsletter[0]->getTitlu() ?></a></li>
</ul>

<div class="container">
        <div class="d-flex mb-5 mt-5" style="border: 1px solid lightgrey; border-radius: 12px; padding: 10px;">
            <img style="width: 150px; height: 150px;" src="../../<?php echo $newsletter[0]->getImgPrin(); ?>" alt="img_prin">
            <div class="ml-3">
                 <h2><?php echo $newsletter[0]->getTitlu(); ?></h2>
                 <div>
                    <a href=" <?php echo '/newsletter/'.$category[0]->getSlug() ?>"> <span class="badge badge-danger"><?php echo $category[0]->getDenumire(); ?></span></a>
                     <p style='color: black'><i>Postat la: <?php echo $newsletter[0]->getCreatedAt(); ?></i></p>
                 </div>
            </div>
        </div>
         <div class="mt-5 mb-5" style="border: 1px solid lightgrey; border-radius: 14px; padding: 16px;">
            <h3>Continutul newsletter-ului:</h3>
            <div class="mt-5">
                <?php echo $newsletter[0]->getContent() ?>
            </div>
         </div>
        <div class="mt-5 mb-5" style=" text-align: center;border: 1px solid lightgrey; border-radius: 12px; padding: 10px;">
            <h4>Comentarii</h4>
             <?php if (isset($formComment)) {
                 echo $formComment["newsletterForm"].
                    "<div style='margin-left: 6em;' class='d-flex mt-3 mb-3 '>".
                        $formComment["comment"].
                     "<div class='mt-4'>".
                        $formComment["send"].
                    "</div>
                     </div>".
                        $formComment["_token"]
                    ."</form>";
             }?>
            <div id="comment-container" style="overflow-y: scroll; max-height: 500px;">
                <?php
                    if (!empty($comments)) {
                        $count = 0;
                        foreach ($comments as $comment) {
                            echo "
                            <div class='m-3' style='padding: 0.5em; border-radius: 10px; border: 1px solid lightgrey;'>
                                 <div style='padding: 0.5em;' class='d-flex m-3'>
                                    <img style='border-radius: 50%; height: 50px; width: 50px;' src='../../{$comment['user']->getImgPrin()}' alt='img_user'>
                                        <div class='ml-3 mt-3'>
                                            <p>{$comment["user"]->getFirstName()} {$comment["user"]->getLastName()}</p>
                                       </div>   
                                </div>
                                <div class='m-3' style='text-align: left;'>
                                    <div>
                                        {$comment["comment"]->getComentariu()}
                                    </div>
                                    <div>
                                        <p style='color: black' '><i>{$comment["comment"]->getCreatedAt()}</i></p>
                                    </div>
                                </div>
                            </div>
                            ";
                            if ($count === 8) {
                                echo '<span id="page_1"></span>';
                            }
                            $count++;

                        }
                    } else {
                        echo 'Nu exista comentarii !';
                    }
                ?>
            </div>
        </div>
</div>
    <script>

        let arrayOfseenElement = [];

        let page = 1;

        $(document).ready( ()=> {
            $('#newsletterForm').submit((e) => {
                e.preventDefault();
                let comment = document.getElementById('comment').value;
                if (comment.length > 0) {
                    let newData = {
                        comentariu: comment
                    }
                    document.getElementById('comment').value = '';

                    ajaxCreateNewsletterComments(newData);
                }
            });

            arrayOfseenElement.push({
                element: document.getElementById('page_1'),
                seen: false
            });

            $('#comment-container').on('scroll', ()=> {
               for (let i = 0; i < arrayOfseenElement.length; i++) {
                   if (arrayOfseenElement[i] && isInViewPort(arrayOfseenElement[i].element) && !arrayOfseenElement[i].seen) {
                        arrayOfseenElement[i].seen = true;
                        ajaxGetDataOfComments();
                   }
               }
            });

        });

        function ajaxCreateNewsletterComments(newData)
        {
            let url = `<?php echo '/newsletter/'.$category[0]->getSlug().'/'.$newsletter[0]->getSlug()."/ajaxCreateNewsletterComments"; ?>`;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', url);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader("Content-type","application/json");
            xhr.send(JSON.stringify(newData));
            xhr.onload = (e) => {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response) {
                        injectTemplateForComment(response);
                    }
                }
            };
        }

        function ajaxGetDataOfComments()
        {
            let url = `<?php echo '/newsletter/'.$category[0]->getSlug().'/'.$newsletter[0]->getSlug()."/ajaxGetDataOfComments"; ?>` + `?pageNr=${page}`+ `&key=<?php echo $apiKey;?>` ;

            const xhr = new XMLHttpRequest();

            xhr.open('GET', url);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader("Content-type","application/json");
            xhr.send();
            xhr.onload = (e) => {
                if (xhr.readyState === XMLHttpRequest.DONE &&  xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.length > 0) {
                       injectTemplateOnCommentsPagination(response);
                    }
                }
            };
        }


        function injectTemplateForComment(data)
        {
            let template = `
            <div class='m-3' style='padding: 0.5em; border-radius: 10px; border: 1px solid lightgrey;'>
                             <div style='padding: 0.5em;' class='d-flex m-3'>
                                <img style='border-radius: 50%; height: 50px; width: 50px;' src='../../${data.imgPrin}' alt='img_user'>
                                    <div class='ml-3 mt-3'>
                                        <p>${data.name}</p>
                                   </div>
                            </div>
                            <div class='m-3' style='text-align: left;'>
                                <div>
                                   ${data.comentariu}
                                </div>
                                <div>
                                    <p style='color: black'><i>${data.date}</i></p>
                                </div>
                            </div>
                        </div>
            `;

            $('#comment-container').prepend(template);
        }

        function injectTemplateOnCommentsPagination(containerOfComments)
        {
            let template = ``;

            page++;

            let count = 0;
            containerOfComments.forEach((element) => {
                 template += `
                 <div class='m-3' style='padding: 0.5em; border-radius: 10px; border: 1px solid lightgrey;'>
                     <div style='padding: 0.5em;' class='d-flex m-3'>
                        <img style='border-radius: 50%; height: 50px; width: 50px;' src='../../${element["user"].img}' alt='img_user'>
                            <div class='ml-3 mt-3'>
                                <p>${element["user"].nume}</p>
                           </div>
                    </div>
                    <div class='m-3' style='text-align: left;'>
                        <div>
                            ${element["comment"].comentariu}
                        </div>
                        <div>
                            <p style='color: black' '><i>${element["comment"].date}</i></p>
                        </div>
                    </div>
                   </div>`

                 if (count === 8) {
                     template += `<span id="page_${page}"></span>`
                 }
                 count++;
            });

            $('#comment-container').append(template);

            if (document.getElementById(`page_${page}`)) {
                arrayOfseenElement.push(
                    {
                        element: document.getElementById(`page_${page}`),
                        seen: false
                    }
                )
            }

        }

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


    </script>


<?php include  \App\Kernel::getRootDirectory().'/public/views/globals/footer.php' ?>
