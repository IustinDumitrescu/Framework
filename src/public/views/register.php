<?php include 'globals/header.php' ?>

<style>
    .custom_padding-all {
        padding: 40px;
    }
</style>

    <div class="container">
        <div style="margin: 2em auto; text-align: center">
            <h1>Register</h1>
        </div>
        <div class="container pt-1">
            <div class="col-xl-9 col-lg-7 col-md-9 col-12 mx-auto">
                <div class='bg-white shadow rounded custom_padding-all'>
                    <?php
                    if (isset($flash)) {
                        if ($flash["flashType"]) {
                            echo "<div class=\"alert alert-success\" role=\"alert\">
                                   {$flash["flashString"]}
                                   </div>
                                  ";
                        } else {
                            echo "<div class=\"alert alert-danger\" role=\"alert\">
                                 {$flash["flashString"]}
                                 </div>
                                 ";
                        }
                    }
                    echo
                    $formRegister["form_register"].
                    "<div style='display: flex; justify-content: space-around'>
                    <div>".
                      $formRegister["first_name"].
                      $formRegister["last_name"].
                      $formRegister["email"].
                      $formRegister["password_register"].
                      $formRegister["confirm_password"].
                      $formRegister["imagPrin"].
                    "</div> 
                    <div>".
                    $formRegister["telefon"].
                    $formRegister["adresa"].
                    $formRegister["age"].
                    "</div>
                    </div>".
                    $formRegister["_token"].
                    "<div style='margin: 1em auto; text-align: center'>".
                    $formRegister["register_submit"].
                    "</div>
                    </form>"
                    ?>
                </div>
        </div>
            </div>
        </div>
    <script src="../js/showFileInput.js"></script>

<script>
        $(document).ready(() => {
            bsCustomFileInput.init();
        });
</script>

<?php include 'globals/footer.php'?>